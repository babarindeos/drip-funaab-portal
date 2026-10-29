<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Applications - {{ $call->title }}</title>
    <style>
        @page { margin: 26px 30px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 9px; color: #1f2937; }
        .org { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #166534; margin-bottom: 6px; }
        h1 { font-size: 15px; margin: 0 0 2px 0; }
        h2 { font-size: 12px; margin: 0 0 8px 0; font-weight: normal; color: #4b5563; }
        .meta { margin-bottom: 10px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #e5e7eb; text-align: left; padding: 6px 5px; border: 1px solid #d1d5db; font-size: 8.5px; }
        td { padding: 6px 5px; border: 1px solid #d1d5db; vertical-align: top; }
        tr { page-break-inside: avoid; }
        .center { text-align: center; }
        .muted { color: #6b7280; }
        .approved { color: #15803d; font-weight: bold; }
        .not_approved { color: #b91c1c; font-weight: bold; }
        .submitted { color: #b45309; font-weight: bold; }
        .totals { width: auto; margin-top: 12px; }
        .totals th, .totals td { padding: 5px 10px; }
        .footer { margin-top: 12px; font-size: 8px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="org">Directorate of Research, Innovations and Partnerships (DRIP)</div>
    <h1>{{ $call->title }}</h1>
    <h2>Conference and workshop attendance grant applications</h2>

    <div class="meta">
        {{ $applications->count() }} {{ \Illuminate\Support\Str::plural('application', $applications->count()) }} submitted.
        First-time beneficiaries are listed first (they are given preference), then by date of submission.
    </div>

    <table>
        <thead>
            <tr>
                <th width="3%" class="center">No.</th>
                <th width="18%">Applicant</th>
                <th width="9%">Category</th>
                <th width="9%">Beneficiary</th>
                <th width="27%">Conference / workshop</th>
                <th width="12%">Total cost</th>
                <th width="9%" class="center">Status</th>
                <th width="8%">Submitted</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($applications as $index => $application)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td><strong>{{ $application->candidate_name }}</strong><br><span class="muted">{{ $application->department }}</span></td>
                    <td>{{ $categories[$application->staff_category] ?? '' }}</td>
                    <td>{{ $application->previously_sponsored ? 'Previous' : 'First-time' }}</td>
                    <td>
                        {{ $application->conference_title }}<br>
                        <span class="muted">{{ $application->conference_venue }}<br>
                        {{ optional($application->conference_start_date)->format('j M Y') }}@if ($application->conference_end_date) &ndash; {{ $application->conference_end_date->format('j M Y') }}@endif
                        &middot; {{ ucfirst((string) $application->conference_scope) }}</span>
                    </td>
                    <td>{{ $application->cost_currency }} {{ number_format($application->total_cost, 2) }}</td>
                    <td class="center"><span class="{{ $application->status }}">{{ $application->status_label }}</span></td>
                    <td>{{ optional($application->submitted_at)->format('j M Y') }}</td>
                </tr>
            @empty
                <tr><td colspan="8" class="center muted">No applications have been submitted for this call.</td></tr>
            @endforelse
        </tbody>
    </table>

    @if (! empty($totals))
        <table class="totals">
            <thead><tr><th>Currency</th><th>Requested</th><th>Approved</th></tr></thead>
            <tbody>
                @foreach ($totals as $currency => $row)
                    <tr><td><strong>{{ $currency }}</strong></td><td>{{ number_format($row['requested'], 2) }}</td><td>{{ number_format($row['approved'], 2) }}</td></tr>
                @endforeach
            </tbody>
        </table>
    @endif

    <div class="footer">Generated {{ $generated_at->format('F j, Y \a\t g:i A') }}. For internal committee use. Bank details and the committee's remarks are not included.</div>
</body>
</html>
