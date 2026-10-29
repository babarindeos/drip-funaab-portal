<?php

namespace App\Http\Controllers\Conference\Staff;

use App\Http\Controllers\Controller;
use App\Models\Conference\ConferenceApplication;
use App\Models\Conference\ConferenceApplicationDocument;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/** Upload, replace and remove the documents of a draft application. */
class ConferenceUploadController extends Controller
{
    public function store(Request $request, ConferenceApplication $application, string $type)
    {
        $this->authorizeEditable($application);

        $definition = config('conference.document_types.'.$type);
        abort_unless($definition && in_array($type, $application->requiredDocumentTypes(), true), 404);

        $rules = ['required', 'file', 'mimes:'.implode(',', $definition['extensions']), 'max:'.$definition['max_kb']];
        if ($definition['kind'] === 'image') {
            $rules[] = 'dimensions:min_width=150,min_height=150';
        }

        // errors go into a bag named after the document, so only that row shows them
        $request->validateWithBag('upload_'.$type, ['file' => $rules], [
            'file.required' => 'Choose a file to upload.',
            'file.mimes' => 'This must be a '.strtoupper(implode(', ', $definition['extensions'])).' file.',
            'file.max' => 'This file is too large. The limit is '.round($definition['max_kb'] / 1024).' MB.',
            'file.dimensions' => 'The photograph is too small. It must be at least 150 x 150 pixels.',
            'file.uploaded' => 'The upload failed. Please try again.',
        ]);

        $file = $request->file('file');
        $disk = config('conference.documents_disk');
        $directory = config('conference.documents_folder').'/'.$application->uuid;
        $stored_name = $type.'-'.Str::random(12).'.'.$file->extension();   // extension from the real file type, not the client's claim
        $path = $file->storeAs($directory, $stored_name, $disk);

        $existing = $application->documents()->where('type', $type)->first();

        if ($existing) {
            Storage::disk($disk)->delete($existing->path);
            $existing->update([
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        } else {
            ConferenceApplicationDocument::create([
                'conference_application_id' => $application->id,
                'type' => $type,
                'path' => $path,
                'original_name' => $file->getClientOriginalName(),
                'mime' => $file->getMimeType(),
                'size' => $file->getSize(),
            ]);
        }

        $application->recordEvent($existing ? 'document_replaced' : 'document_uploaded', Auth::id(), $definition['label']);

        return redirect()->route('staff.conference.applications.documents', ['application' => $application->uuid])
                         ->with('success', $definition['label'].' has been '.($existing ? 'replaced.' : 'uploaded.'));
    }

    public function destroy(ConferenceApplication $application, string $type)
    {
        $this->authorizeEditable($application);

        $document = $application->documents()->where('type', $type)->firstOrFail();

        Storage::disk(config('conference.documents_disk'))->delete($document->path);
        $document->delete();

        $application->recordEvent('document_removed', Auth::id(), $document->label);

        return redirect()->route('staff.conference.applications.documents', ['application' => $application->uuid])
                         ->with('success', $document->label.' has been removed.');
    }

    private function authorizeEditable(ConferenceApplication $application): void
    {
        abort_unless((int) $application->user_id === (int) Auth::id(), 404);
        abort_unless($application->isEditable() && $application->call->isOpen(), 403, 'This application can no longer be changed.');
    }
}
