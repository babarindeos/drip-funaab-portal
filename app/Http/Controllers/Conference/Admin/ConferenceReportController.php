<?php

namespace App\Http\Controllers\Conference\Admin;

use App\Http\Controllers\Controller;
use App\Models\Conference\ConferenceApplication;
use App\Models\Conference\ConferenceCall;
use App\Services\Conference\ConferenceReport;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Str;

/** Conference analytics and the exports of a call's applications. */
class ConferenceReportController extends Controller
{
    /** Sum of every cost an applicant entered, as SQL. */
    private const TOTAL_SQL = 'COALESCE(registration_fee,0)+COALESCE(flight_cost,0)+COALESCE(accommodation_cost,0)+COALESCE(feeding_cost,0)+COALESCE(excursion_cost,0)';

    public function analytics(Request $request)
    {
        $calls = ConferenceCall::orderBy('created_at', 'desc')->get();
        $call = $request->filled('call') ? $calls->firstWhere('id', (int) $request->query('call')) : null;

        // submitted applications only (drafts are not part of the picture), optionally for one call
        $base = fn () => ConferenceApplication::query()
            ->where('status', '!=', ConferenceApplication::STATUS_DRAFT)
            ->when($call, fn ($q) => $q->where('conference_call_id', $call->id));

        $by_status = $base()->selectRaw('status, COUNT(*) as total')->groupBy('status')->pluck('total', 'status');

        $stats = [
            'submitted' => (int) $by_status->sum(),
            'awaiting' => (int) ($by_status[ConferenceApplication::STATUS_SUBMITTED] ?? 0),
            'approved' => (int) ($by_status[ConferenceApplication::STATUS_APPROVED] ?? 0),
            'not_approved' => (int) ($by_status[ConferenceApplication::STATUS_NOT_APPROVED] ?? 0),
        ];

        $categories = config('conference.staff_categories');

        $breakdowns = [
            'Beneficiary type' => $this->breakdown($base, 'previously_sponsored', [0 => 'First-time beneficiaries', 1 => 'Previous beneficiaries']),
            'Staff category' => $this->breakdown($base, 'staff_category', $categories),
            'Local or overseas' => $this->breakdown($base, 'conference_scope', ['local' => 'Local', 'overseas' => 'Overseas']),
        ];

        // departments are matched ignoring case and stray spaces, so "Physics" and "physics " count together
        $departments = $base()
            ->selectRaw("LOWER(TRIM(COALESCE(department, ''))) as grp, MIN(department) as label, COUNT(*) as submitted, SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved")
            ->groupBy('grp')
            ->orderByDesc('submitted')
            ->limit(8)
            ->get()
            ->map(fn ($row) => ['label' => trim((string) $row->label) ?: 'Not stated', 'submitted' => (int) $row->submitted, 'approved' => (int) $row->approved]);

        // money is grouped by currency and never added across currencies
        $total = self::TOTAL_SQL;
        $costs = $base()
            ->selectRaw("cost_currency, SUM({$total}) as requested, SUM(CASE WHEN status = 'submitted' THEN {$total} ELSE 0 END) as awaiting, SUM(CASE WHEN status = 'approved' THEN {$total} ELSE 0 END) as approved")
            ->groupBy('cost_currency')
            ->orderBy('cost_currency')
            ->get();

        // one row per call (only when looking at all calls)
        $per_call = $call ? collect() : ConferenceCall::withCount([
            'applications as submitted_count' => fn ($q) => $q->where('status', '!=', ConferenceApplication::STATUS_DRAFT),
            'applications as awaiting_count' => fn ($q) => $q->where('status', ConferenceApplication::STATUS_SUBMITTED),
            'applications as approved_count' => fn ($q) => $q->where('status', ConferenceApplication::STATUS_APPROVED),
            'applications as not_approved_count' => fn ($q) => $q->where('status', ConferenceApplication::STATUS_NOT_APPROVED),
        ])->orderBy('created_at', 'desc')->get();

        return view('conference.admin.analytics', compact('calls', 'call', 'stats', 'breakdowns', 'departments', 'costs', 'per_call'));
    }

    /** The call's applications as a spreadsheet (opens directly in Excel). */
    public function csv(ConferenceCall $call)
    {
        $applications = ConferenceReport::applications($call);
        $filename = Str::slug($call->title).'-applications-'.now()->format('Y-m-d').'.csv';
        $categories = config('conference.staff_categories');

        return response()->streamDownload(function () use ($applications, $categories) {
            $out = fopen('php://output', 'w');
            fwrite($out, "\xEF\xBB\xBF"); // UTF-8 marker so Excel shows accents correctly

            fputcsv($out, ['No.', 'Candidate', 'Department', 'Staff category', 'Beneficiary', 'Conference / workshop', 'Venue',
                           'Local or overseas', 'Start date', 'End date', 'Days', 'Currency', 'Registration fee', 'Flight',
                           'Accommodation', 'Feeding', 'Excursion', 'Total', 'Status', 'Submitted', 'Decided',
                           'FUNAAB email', 'Phone']);

            foreach ($applications as $index => $a) {
                fputcsv($out, [
                    $index + 1,
                    ConferenceReport::csv_safe($a->candidate_name),
                    ConferenceReport::csv_safe($a->department),
                    $categories[$a->staff_category] ?? '',
                    $a->previously_sponsored ? 'Previous beneficiary' : 'First-time',
                    ConferenceReport::csv_safe($a->conference_title),
                    ConferenceReport::csv_safe($a->conference_venue),
                    ucfirst((string) $a->conference_scope),
                    optional($a->conference_start_date)->format('Y-m-d'),
                    optional($a->conference_end_date)->format('Y-m-d'),
                    $a->duration_days,
                    $a->cost_currency,
                    $a->registration_fee, $a->flight_cost, $a->accommodation_cost, $a->feeding_cost, $a->excursion_cost,
                    number_format($a->total_cost, 2, '.', ''),
                    $a->status_label,
                    optional($a->submitted_at)->format('Y-m-d'),
                    optional($a->decided_at)->format('Y-m-d'),
                    ConferenceReport::csv_safe($a->funaab_email),
                    ConferenceReport::csv_safe($a->phone),
                ]);
            }

            fclose($out);
        }, $filename, ['Content-Type' => 'text/csv; charset=UTF-8']);
    }

    /** The call's applications as a printable PDF for committee records. */
    public function pdf(ConferenceCall $call)
    {
        $applications = ConferenceReport::applications($call);

        return Pdf::loadView('conference.pdf.applications_report', [
                    'call' => $call,
                    'applications' => $applications,
                    'totals' => ConferenceReport::totalsByCurrency($applications),
                    'categories' => config('conference.staff_categories'),
                    'generated_at' => now(),
                ])
                ->setPaper('a4', 'landscape')
                ->download(Str::slug($call->title).'-applications-'.now()->format('Y-m-d').'.pdf');
    }

    /**
     * Submitted and approved counts for each value of a column.
     * $labels maps the stored value to the text shown; anything else is "Not stated".
     */
    private function breakdown(callable $base, string $column, array $labels): array
    {
        $rows = $base()
            ->selectRaw("{$column} as grp, COUNT(*) as submitted, SUM(CASE WHEN status = 'approved' THEN 1 ELSE 0 END) as approved")
            ->groupBy($column)
            ->get()
            ->keyBy(fn ($row) => (string) $row->grp);

        $result = [];

        foreach ($labels as $value => $label) {
            $row = $rows->get((string) $value);
            $result[] = ['label' => $label, 'submitted' => (int) ($row->submitted ?? 0), 'approved' => (int) ($row->approved ?? 0)];
        }

        $known = collect(array_keys($labels))->map(fn ($v) => (string) $v);
        $other = $rows->reject(fn ($row, $key) => $known->contains((string) $key));

        if ($other->isNotEmpty()) {
            $result[] = ['label' => 'Not stated', 'submitted' => (int) $other->sum('submitted'), 'approved' => (int) $other->sum('approved')];
        }

        return $result;
    }
}
