<!DOCTYPE html>
<html lang="en">
<head>
    <meta charset="utf-8">
    <title>Results - {{ $call_for_proposal->title }}</title>
    <style>
        @page { margin: 28px 32px; }
        body { font-family: 'DejaVu Sans', sans-serif; font-size: 10px; color: #1f2937; }
        h1 { font-size: 15px; margin: 0 0 2px 0; }
        h2 { font-size: 12px; margin: 0 0 10px 0; font-weight: normal; color: #4b5563; }
        .org { font-size: 9px; text-transform: uppercase; letter-spacing: 1px; color: #166534; margin-bottom: 6px; }
        .meta { margin-bottom: 10px; color: #4b5563; }
        table { width: 100%; border-collapse: collapse; }
        th { background: #e5e7eb; text-align: left; padding: 6px 5px; border: 1px solid #d1d5db; font-size: 9px; }
        td { padding: 6px 5px; border: 1px solid #d1d5db; vertical-align: top; }
        tr { page-break-inside: avoid; }
        .center { text-align: center; }
        .muted { color: #9ca3af; }
        .accepted { color: #15803d; font-weight: bold; }
        .rejected { color: #b91c1c; font-weight: bold; }
        .acknowledged { color: #1d4ed8; font-weight: bold; }
        .pending { color: #b45309; font-weight: bold; }
        .footer { margin-top: 12px; font-size: 8px; color: #6b7280; }
    </style>
</head>
<body>
    <div class="org">Directorate of Research, Innovations and Partnerships (DRIP)</div>
    <h1>Results: {{ $call_for_proposal->title }}</h1>
    <h2>{{ $call_for_proposal->open_date->format('F j, Y') }} to {{ $call_for_proposal->close_date->format('F j, Y') }}</h2>

    <div class="meta">
        {{ $rows->count() }} {{ \Illuminate\Support\Str::plural('application', $rows->count()) }},
        ranked by average reviewer score. Maximum obtainable score per reviewer: {{ $max_obtainable_score }}.
    </div>

    <table>
        <thead>
            <tr>
                <th width="5%" class="center">Rank</th>
                <th width="31%">Proposal Title</th>
                <th width="17%">Principal Investigator</th>
                <th width="17%">Applicant</th>
                <th width="10%" class="center">Reviewers</th>
                <th width="12%" class="center">Average Score</th>
                <th width="8%" class="center">Decision</th>
            </tr>
        </thead>
        <tbody>
            @forelse ($rows as $index => $row)
                <tr>
                    <td class="center">{{ $index + 1 }}</td>
                    <td>{{ $row->title }}</td>
                    <td>{{ $row->principal_investigator }}</td>
                    <td>{{ $row->applicant }}</td>
                    <td class="center">{{ $row->completed }} / {{ $row->assigned }}</td>
                    <td class="center">
                        @if (is_null($row->average))
                            <span class="muted">Not yet reviewed</span>
                        @else
                            {{ $row->average }} / {{ $max_obtainable_score }}
                        @endif
                    </td>
                    <td class="center"><span class="{{ $row->status }}">{{ ucfirst($row->status) }}</span></td>
                </tr>
            @empty
                <tr><td colspan="7" class="center muted">No applications were submitted for this call.</td></tr>
            @endforelse
        </tbody>
    </table>

    <div class="footer">Generated {{ $generated_at->format('F j, Y \a\t g:i A') }}. For internal committee use.</div>
</body>
</html>
