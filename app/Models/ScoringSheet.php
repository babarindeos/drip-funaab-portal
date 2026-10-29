<?php

namespace App\Models;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class ScoringSheet extends Model
{
    use HasFactory;

    protected $fillable = [
        'uuid', 'proposal_reviewers_id', 'proposal_reviewers_uuid', 'proposal_application_uuid',
        'reviewer_uuid', 'reviewer_id', 'comment',
        'scoring_guide_1', 'scoring_guide_2', 'scoring_guide_3', 'scoring_guide_4', 'scoring_guide_5',
        'scoring_guide_6', 'scoring_guide_7', 'scoring_guide_8', 'scoring_guide_9', 'scoring_guide_10',
        'scoring_guide_11', 'scoring_guide_12', 'scoring_guide_13', 'scoring_guide_14', 'scoring_guide_15',
        'scoring_guide_16', 'scoring_guide_17', 'scoring_guide_18', 'scoring_guide_19',
    ];

    /** How many scoring_guide_n columns the fixed rubric currently has. */
    const SCORING_GUIDE_COUNT = 19;

    /**
     * Sum of this reviewer's scores across all 19 scoring_guide_n columns.
     * Centralizing this here means the aggregation logic only lives in one
     * place if the rubric size ever changes.
     */
    public function getTotalScoreAttribute(): float
    {
        $total = 0;

        for ($i = 1; $i <= self::SCORING_GUIDE_COUNT; $i++) {
            $total += (float) ($this->{'scoring_guide_'.$i} ?? 0);
        }

        return $total;
    }
}
