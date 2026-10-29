<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalReviewers extends Model
{
    use HasFactory;

    protected $casts = [
        'emailed_at' => 'datetime',
    ];

    public function reviewer()
    {
        return $this->belongsTo(Reviewer::class, 'reviewer_id', 'id');
    }

    public function proposal_application()
    {
        return $this->belongsTo(ProposalApplication::class, 'proposal_application_id', 'id');
    }

    public function is_reviewed()
    {
    
        return $this->hasOne(ScoringSheet::class, 'proposal_reviewers_uuid', 'uuid');
    }

    /**
     * The reviewer's unique review link, built on the configurable public
     * base URL (config/drip.php -> DRIP_BASE_URL in .env) so it can be
     * swapped between local and production without touching code.
     */
    public function buildReviewLink(): string
    {
        $path = route('guests.call_for_proposals.proposal_applications.review', [
            'call_for_proposal' => $this->proposal_application->call_for_proposal->uuid,
            'proposal_application' => $this->proposal_application_uuid,
            'reviewer' => $this->reviewer_uuid,
            'review' => $this->uuid,
        ], false); // false = relative path only; the base URL is added below

        return rtrim(config('drip.base_url'), '/').$path;
    }

    /**
     * The invitation text sent to the reviewer. Stored on the record so the
     * admin can see exactly what was sent. Paragraphs are separated by blank
     * lines so the same text renders cleanly in the email.
     */
    public function composeInvitationMessage(): string
    {
        $call_title = $this->proposal_application->call_for_proposal->title;

        return implode("\n\n", [
            'Dear '.$this->reviewer->name.',',
            'You have been invited to review a research proposal submitted under the call "'.$call_title.'".',
            'Proposal: '.$this->proposal_application->proposal_title,
            'Please use the link below to open the proposal and submit your scores. No login is required:',
            $this->buildReviewLink(),
            'Thank you for your support.',
            config('app.name').' - Directorate of Research, Innovations and Partnerships (DRIP)',
        ]);
    }
}
