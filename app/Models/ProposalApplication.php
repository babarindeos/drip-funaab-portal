<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ProposalApplication extends Model
{
    use HasFactory;

    protected $fillable = ['uuid', 'user_id', 'call_for_proposal_id', 'proposal_title_file', 'proposal_file', 'college_review', 'principal_investigator', 'proposal_title', 'proposal_description', 'status', 'remark'];



    public function call_for_proposal()
    {
        return $this->belongsTo(CallForProposal::class, 'call_for_proposal_id', 'id');
        
    }

    public function reviews()
    {
        return $this->hasMany(ProposalReviewers::class, 'proposal_application_id', 'id');
    }

    public function owner()
    {
        return $this->belongsTo(User::class, 'user_id', 'id');
    }

    /**
     * Every completed scoring sheet for this application, across all
     * reviewers it was sent to.
     */
    public function scoring_sheets()
    {
        return $this->hasManyThrough(
            ScoringSheet::class,
            ProposalReviewers::class,
            'proposal_application_id', // FK on proposal_reviewers
            'proposal_reviewers_id',   // FK on scoring_sheets
            'id',                      // local key on proposal_applications
            'id'                       // local key on proposal_reviewers
        );
    }

    public function getReviewersAssignedCountAttribute(): int
    {
        return $this->reviews()->count();
    }

    public function getReviewersCompletedCountAttribute(): int
    {
        return $this->scoring_sheets()->count();
    }

    /**
     * Average total score across every reviewer who has scored this
     * application so far. Null if nobody has reviewed it yet.
     */
    public function getAverageScoreAttribute(): ?float
    {
        $sheets = $this->scoring_sheets()->get();

        if ($sheets->isEmpty()) {
            return null;
        }

        return round($sheets->avg(fn ($sheet) => $sheet->total_score), 2);
    }

    public function getReviewCompleteAttribute(): bool
    {
        return $this->reviewers_assigned_count > 0
            && $this->reviewers_completed_count >= $this->reviewers_assigned_count;
    }
}
