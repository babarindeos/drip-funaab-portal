<?php

namespace App\Http\Classes;

use Illuminate\Support\Collection;

/**
 * Builds the ranked results of a call from its applications. Used by both
 * the CSV and PDF exports so they always show identical figures.
 */
class ResultsReport
{
    /**
     * One row per application, ranked by average reviewer score (highest
     * first; applications nobody has scored yet go last).
     */
    public static function rows(Collection $applications): Collection
    {
        // a handful of queries in total, however many applications there are
        $applications->loadMissing(['owner', 'reviews.reviewer', 'reviews.is_reviewed']);

        $rows = $applications->map(function ($application) {
            $scored = $application->reviews->filter(fn ($review) => $review->is_reviewed);
            $completed = $scored->count();

            $applicant = $application->owner
                ? trim($application->owner->surname.' '.$application->owner->firstname)
                : '';

            return (object) [
                'title' => $application->proposal_title,
                'principal_investigator' => $application->principal_investigator,
                'applicant' => $applicant,
                'email' => $application->owner->email ?? '',
                'assigned' => $application->reviews->count(),
                'completed' => $completed,
                'average' => $completed > 0
                    ? round($scored->avg(fn ($review) => $review->is_reviewed->total_score), 2)
                    : null,
                'reviewer_scores' => $scored
                    ->map(fn ($review) => ($review->reviewer->name ?? 'Reviewer').': '.$review->is_reviewed->total_score)
                    ->implode('; '),
                'status' => $application->status ?: 'pending',
            ];
        });

        return $rows->sortByDesc(fn ($row) => $row->average ?? -1)->values();
    }

    /**
     * Stops a spreadsheet from running text as a formula. A title or name
     * starting with = + - or @ would otherwise be executed by Excel when the
     * CSV is opened.
     */
    public static function csv_safe($value): string
    {
        $value = (string) $value;

        return preg_match('/^[=+\-@\t\r]/', $value) ? "'".$value : $value;
    }
}
