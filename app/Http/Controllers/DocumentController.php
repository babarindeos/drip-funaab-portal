<?php

namespace App\Http\Controllers;

use App\Models\ProposalApplication;
use App\Models\ProposalReviewers;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Str;

/**
 * Serves proposal documents through the portal so access can be checked,
 * instead of linking straight to a file in the public storage folder.
 */
class DocumentController extends Controller
{
    /** URL type => [column on proposal_applications, friendly label] */
    private const TYPES = [
        'title'          => ['proposal_title_file', 'title-page'],
        'proposal'       => ['proposal_file',       'proposal'],
        'college_review' => ['college_review',      'college-review'],
    ];

    /**
     * For logged-in users: admins can open any document; a staff member can
     * open only the documents of their own application.
     */
    public function application(string $uuid, string $type)
    {
        $application = ProposalApplication::where('uuid', $uuid)->firstOrFail();
        $user = Auth::user();

        $allowed = $user->role === 'admin' || (int) $user->id === (int) $application->user_id;

        abort_unless($allowed, 403);

        return $this->serve($application, $type);
    }

    /**
     * For external reviewers (no account): the unique review link is their
     * credential. They may open ONLY the proposal document assigned to them -
     * never the title page or college review, which would identify the
     * applicant and break the anonymous review.
     */
    public function review(string $review, string $type)
    {
        $proposal_reviewer = ProposalReviewers::where('uuid', $review)->firstOrFail();

        abort_unless($type === 'proposal', 403);

        return $this->serve($proposal_reviewer->proposal_application, $type);
    }

    private function serve(ProposalApplication $application, string $type)
    {
        abort_unless(isset(self::TYPES[$type]), 404);

        [$column, $label] = self::TYPES[$type];

        $path = $application->{$column};
        abort_if(empty($path), 404);

        // Look on the configured disk first, then the others, so documents
        // uploaded before the disk was changed are still found.
        foreach (array_unique([config('drip.documents_disk'), 'public', 'local']) as $disk) {
            if (Storage::disk($disk)->exists($path)) {
                $extension = pathinfo($path, PATHINFO_EXTENSION);
                $download_name = Str::slug($application->proposal_title ?: 'proposal').'-'.$label.'.'.$extension;

                return Storage::disk($disk)->response($path, $download_name);
            }
        }

        abort(404, 'This document could not be found.');
    }
}
