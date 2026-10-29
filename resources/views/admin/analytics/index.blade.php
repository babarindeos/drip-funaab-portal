<x-admin-layout>
    @php
        $pad = fn ($n) => str_pad($n, 2, '0', STR_PAD_LEFT);
        $reviewer_pct = $totals->reviewers_assigned > 0 ? (int) round($totals->reviewers_completed / $totals->reviewers_assigned * 100) : null;
    @endphp

    <div class="drip-page">
        <div class="drip-container">

            <nav class="drip-breadcrumb" aria-label="Breadcrumb"><span aria-current="page">Analytics</span></nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1">Analytics</h1>
                    <div class="drip-subtitle"><x-icon name="bar-chart" :size="20" /> <span>Calls for proposals: applications, decisions and reviewer progress</span></div>
                </div>
            </div>

            <div class="drip-stats drip-stats--4">
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="calendar" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($totals->calls) }}</div><div class="drip-stat__label">Calls ({{ $totals->open_calls }} open now)</div></div></div>
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="copy" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($totals->applications) }}</div><div class="drip-stat__label">Applications</div></div></div>
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="clock" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($totals->awaiting_decision) }}</div><div class="drip-stat__label">Awaiting decision</div></div></div>
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="users" :size="22" /></span><div><div class="drip-stat__value">{{ $totals->reviewers_completed }}/{{ $totals->reviewers_assigned }}</div><div class="drip-stat__label">Reviews done{{ is_null($reviewer_pct) ? '' : ' ('.$reviewer_pct.'%)' }}</div></div></div>
            </div>

            <div class="drip-section-head">
                <div>
                    <h2 class="drip-section-title">By call</h2>
                    <div class="drip-section-sub">Accepted: {{ $totals->accepted }} &middot; Rejected: {{ $totals->rejected }} &middot; Maximum score per reviewer: {{ $max_obtainable_score }}</div>
                </div>
            </div>

            <div class="drip-card">
                <div class="drip-table-wrap">
                    <table class="drip-table" style="min-width:980px;">
                        <thead>
                            <tr><th>Call</th><th>Status</th><th>Applications</th><th style="min-width:230px;">Decisions</th><th>Reviews done</th><th>Average score</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($per_call as $row)
                                @php
                                    $call_status = $row->call->computed_status;
                                    $pill = match ($call_status) { 'Open' => 'drip-pill--success', 'Upcoming' => 'drip-pill--warning', default => 'drip-pill--muted' };
                                    $total = max($row->applications, 1);
                                    $segments = [
                                        ['Accepted', $row->accepted, '#22c55e'],
                                        ['Acknowledged', $row->acknowledged, '#60a5fa'],
                                        ['Pending', $row->pending, '#fbbf24'],
                                        ['Rejected', $row->rejected, '#ef4444'],
                                    ];
                                @endphp
                                <tr>
                                    <td>
                                        <a class="drip-app-title" href="{{ route('admin.call_for_proposals.submissions', ['call_for_proposal' => $row->call->id]) }}">{{ $row->call->title }}</a>
                                        <div class="drip-app-meta">{{ $row->call->open_date->format('M j, Y') }} to {{ $row->call->close_date->format('M j, Y') }}</div>
                                    </td>
                                    <td><span class="drip-pill {{ $pill }}">{{ $call_status }}</span></td>
                                    <td><strong>{{ $row->applications }}</strong></td>
                                    <td>
                                        @if ($row->applications > 0)
                                            <div style="display:flex; height:10px; border-radius:5px; overflow:hidden; background:var(--muted);">
                                                @foreach ($segments as [$name, $count, $color])
                                                    @if ($count > 0)<div title="{{ $name }}: {{ $count }}" style="width: {{ round($count / $total * 100, 1) }}%; background: {{ $color }};"></div>@endif
                                                @endforeach
                                            </div>
                                            <div class="drip-app-meta" style="margin-top:8px;">
                                                @foreach ($segments as [$name, $count, $color])
                                                    @if ($count > 0)<span style="white-space:nowrap; margin-right:10px;"><span style="display:inline-block;width:8px;height:8px;border-radius:50%;background:{{ $color }};"></span> {{ $name }} {{ $count }}</span>@endif
                                                @endforeach
                                            </div>
                                        @else
                                            <span class="drip-app-meta">No applications</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if ($row->reviewers_assigned > 0)
                                            <span class="drip-reviews {{ $row->reviewers_completed >= $row->reviewers_assigned ? 'drip-reviews--done' : 'drip-reviews--active' }}"><x-icon name="users" :size="16" /> {{ $row->reviewers_completed }} / {{ $row->reviewers_assigned }}</span>
                                        @else
                                            <span class="drip-reviews"><x-icon name="minus" :size="16" /> Not sent</span>
                                        @endif
                                    </td>
                                    <td>
                                        @if (is_null($row->average_score))
                                            <span class="drip-app-meta">-</span>
                                        @else
                                            <strong>{{ $row->average_score }}</strong> <span class="drip-app-meta" style="display:inline;">/ {{ $max_obtainable_score }}</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="drip-row-actions" style="flex-wrap:wrap;">
                                            <a href="{{ route('admin.call_for_proposals.results', ['call_for_proposal' => $row->call->id]) }}" class="drip-btn drip-btn--outline drip-btn--sm"><x-icon name="bar-chart" :size="15" /> Results</a>
                                            @if ($row->applications > 0)
                                                <a href="{{ route('admin.call_for_proposals.results.pdf', ['call_for_proposal' => $row->call->id]) }}" class="drip-btn drip-btn--outline drip-btn--sm">PDF</a>
                                                <a href="{{ route('admin.call_for_proposals.results.csv', ['call_for_proposal' => $row->call->id]) }}" class="drip-btn drip-btn--outline drip-btn--sm">Excel</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="7" class="drip-empty"><strong>No calls yet</strong>Create a call for proposals to see its figures here.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
