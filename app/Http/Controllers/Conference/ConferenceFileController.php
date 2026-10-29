<?php

namespace App\Http\Controllers\Conference;

use App\Http\Controllers\Controller;
use App\Models\Conference\ConferenceApplication;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Serves the private files of an application: uploaded documents and the
 * printable nomination form. Only the applicant and admins may open them.
 */
class ConferenceFileController extends Controller
{
    public function document(ConferenceApplication $application, string $type)
    {
        $this->authorizeView($application);

        $document = $application->documents()->where('type', $type)->firstOrFail();
        $disk = config('conference.documents_disk');

        abort_unless(Storage::disk($disk)->exists($document->path), 404);

        $name = Str::slug(($application->candidate_name ?: 'applicant').'-'.$type).'.'.pathinfo($document->path, PATHINFO_EXTENSION);

        return Storage::disk($disk)->response($document->path, $name, ['Cache-Control' => 'private, no-store']);
    }

    /** The pre-filled TETFund Conference Attendance Nomination Form, ready to print and sign. */
    public function form(ConferenceApplication $application)
    {
        $this->authorizeView($application);

        $application->loadMissing('call', 'documents');

        $photo = null;
        $photo_document = $application->document('passport_photo');
        $disk = config('conference.documents_disk');

        if ($photo_document && Storage::disk($disk)->exists($photo_document->path)) {
            // embedded in the PDF itself, so the PDF never has to fetch anything from the web
            $photo = 'data:'.($photo_document->mime ?: 'image/jpeg').';base64,'.base64_encode(Storage::disk($disk)->get($photo_document->path));
        }

        $pdf = Pdf::loadView('conference.pdf.nomination_form', [
                    'application' => $application,
                    'call' => $application->call,
                    'photo' => $photo,
                    'printed_at' => now(),
                ])
                ->setPaper('a4', 'portrait');

        return $pdf->stream(Str::slug(($application->candidate_name ?: 'applicant').'-nomination-form').'.pdf');
    }

    private function authorizeView(ConferenceApplication $application): void
    {
        $user = Auth::user();

        // 404 (not 403) so nobody can probe which applications exist
        abort_unless($user && ($user->role === 'admin' || (int) $user->id === (int) $application->user_id), 404);
    }
}
