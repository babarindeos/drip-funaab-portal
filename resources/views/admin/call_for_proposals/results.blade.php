<x-admin-layout>
    @php
        $pad = fn ($n) => str_pad($n, 2, '0', STR_PAD_LEFT);

        // worked out once per proposal, so the figures below and the table always agree
        $rows = $proposal_applications->map(function ($application) {
            $assigned = $application->reviewers_assigned_count;
            $completed = $application->reviewers_completed_count;

            return (object) [
                'application' => $application,
                'assigned' => $assigned,
                'completed' => $completed,
                'average' => $application->average_score,
                'complete' => $assigned > 0 && $completed >= $assigned,
            ];
        });

        $totals = (object) [
            'applications' => $rows->count(),
            'fully_reviewed' => $rows->where('complete', true)->count(),
            'assigned' => $rows->sum('assigned'),
            'completed' => $rows->sum('completed'),
            'decided' => $rows->filter(fn ($row) => in_array($row->application->status, ['accepted', 'rejected'], true))->count(),
        ];
    @endphp

    <div class="drip-page">
        <div class="drip-container">

            <nav class="drip-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('admin.call_for_proposals.index') }}">Call for Proposals</a>
                <x-icon name="chevron-right" :size="14" />
                <a href="{{ route('admin.call_for_proposals.show', ['call_for_proposal' => $call_for_proposal->id]) }}">{{ \Illuminate\Support\Str::limit($call_for_proposal->title, 42) }}</a>
                <x-icon name="chevron-right" :size="14" />
                <span aria-current="page">Results</span>
            </nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1">Results</h1>
                    <div class="drip-subtitle"><x-icon name="book-open" :size="20" /> <span>{{ $call_for_proposal->title }}</span></div>
                </div>
                <div class="drip-actions">
                    <a href="{{ route('admin.call_for_proposals.submissions', ['call_for_proposal' => $call_for_proposal->id]) }}" class="drip-btn drip-btn--outline"><x-icon name="arrow-left" :size="18" /> Applications</a>
                    <a href="{{ route('admin.call_for_proposals.results.pdf', ['call_for_proposal' => $call_for_proposal->id]) }}" class="drip-btn drip-btn--outline"><x-icon name="download" :size="18" /> Export PDF</a>
                    <a href="{{ route('admin.call_for_proposals.results.csv', ['call_for_proposal' => $call_for_proposal->id]) }}" class="drip-btn drip-btn--primary"><x-icon name="download" :size="18" /> Export Excel (CSV)</a>
                </div>
            </div>

            <x-flash />

            <div class="drip-stats drip-stats--4">
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="copy" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($totals->applications) }}</div><div class="drip-stat__label">Applications</div></div></div>
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="circle-check" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($totals->fully_reviewed) }}</div><div class="drip-stat__label">Fully reviewed</div></div></div>
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="users" :size="22" /></span><div><div class="drip-stat__value">{{ $totals->completed }}/{{ $totals->assigned }}</div><div class="drip-stat__label">Reviews completed</div></div></div>
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="check" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($totals->decided) }}</div><div class="drip-stat__label">Decisions made</div></div></div>
            </div>

            <div class="drip-section-head">
                <div>
                    <h2 class="drip-section-title">Ranking</h2>
                    <div class="drip-section-sub">Ranked by average score across the reviewers who have finished. Maximum obtainable score per reviewer: <strong>{{ $max_obtainable_score }}</strong>.</div>
                </div>
            </div>

            <div class="drip-card">
                <div class="drip-table-wrap">
                    <table class="drip-table" style="min-width:860px;">
                        <thead>
                            <tr><th>Rank</th><th>Proposal</th><th>Reviews</th><th>Average score</th><th>Decision</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($rows as $row)
                                @php $application = $row->application; @endphp
                                <tr>
                                    <td class="drip-num">{{ $pad($loop->iteration) }}</td>
                                    <td>
                                        <a class="drip-app-title" href="{{ route('admin.call_for_proposals.proposal_application', ['call_for_proposal' => $call_for_proposal->id, 'proposal_application' => $application->id]) }}">{{ $application->proposal_title }}</a>
                                        <div class="drip-app-meta">Principal investigator: {{ $application->principal_investigator }}</div>
                                    </td>
                                    <td>
                                        @if ($row->assigned === 0)
                                            <span class="drip-reviews"><x-icon name="minus" :size="16" /> Not sent</span>
                                        @else
                                            <span class="drip-reviews {{ $row->complete ? 'drip-reviews--done' : 'drip-reviews--active' }}"><x-icon name="users" :size="16" /> {{ $row->completed }} / {{ $row->assigned }}</span>
                                        @endif
                                    </td>
                                    <td style="white-space:nowrap;">
                                        @if (is_null($row->average))
                                            <span class="drip-reviews">Not reviewed yet</span>
                                        @else
                                            <strong>{{ $row->average }}</strong> <span class="drip-app-meta" style="display:inline;">/ {{ $max_obtainable_score }}</span>
                                        @endif
                                    </td>
                                    <td><x-status-badge :status="$application->status" /></td>
                                    <td>
                                        <a href="{{ route('admin.call_for_proposals.proposal_application', ['call_for_proposal' => $call_for_proposal->id, 'proposal_application' => $application->id]) }}" class="drip-btn drip-btn--primary drip-btn--sm"><x-icon name="check" :size="15" /> Decide</a>
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="6" class="drip-empty"><strong>No applications yet</strong>Applications appear here once staff have submitted them.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>

            <div class="drip-note"><x-icon name="lock" :size="16" /> Application information is confidential and restricted to authorised personnel.</div>
        </div>
    </div>
</x-admin-layout>
