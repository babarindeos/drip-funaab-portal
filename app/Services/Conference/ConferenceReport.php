<?php

namespace App\Services\Conference;

use App\Models\Conference\ConferenceApplication;
use App\Models\Conference\ConferenceCall;
use Illuminate\Support\Collection;

/** The list of a call's applications as used by the PDF and CSV exports. */
class ConferenceReport
{
    /**
     * Every submitted application of a call (drafts are not included), with
     * first-time beneficiaries first, because the memo gives them preference,
     * then in the order they were submitted.
     */
    public static function applications(ConferenceCall $call): Collection
    {
        return ConferenceApplication::where('conference_call_id', $call->id)
            ->where('status', '!=', ConferenceApplication::STATUS_DRAFT)
            ->orderBy('previously_sponsored', 'asc')
            ->orderBy('submitted_at', 'asc')
            ->get();
    }

    /**
     * Totals per currency. Different currencies are NEVER added together.
     *
     * @return array<string, array{requested:float, approved:float}>
     */
    public static function totalsByCurrency(Collection $applications): array
    {
        $totals = [];

        foreach ($applications->groupBy('cost_currency') as $currency => $group) {
            $totals[$currency] = [
                'requested' => (float) $group->sum(fn ($a) => $a->total_cost),
                'approved' => (float) $group->where('status', ConferenceApplication::STATUS_APPROVED)->sum(fn ($a) => $a->total_cost),
            ];
        }

        ksort($totals);

        return $totals;
    }

    /**
     * Stops a spreadsheet from running text as a formula: a name or title that
     * starts with = + - or @ would otherwise be executed by Excel.
     */
    public static function csv_safe($value): string
    {
        $value = (string) $value;

        return preg_match('/^[=+\-@\t\r]/', $value) ? "'".$value : $value;
    }
}
