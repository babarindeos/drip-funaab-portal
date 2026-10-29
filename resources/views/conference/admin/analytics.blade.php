<x-admin-layout>
    @php
        $pad = fn ($n) => str_pad($n, 2, '0', STR_PAD_LEFT);
        $money = fn ($value) => number_format((float) $value, 2);
    @endphp

    <div class="drip-page">
        <div class="drip-container">

            <nav class="drip-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('admin.conference.calls.index') }}">Conference Grants</a>
                <x-icon name="chevron-right" :size="14" />
                <span aria-current="page">Analytics</span>
            </nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1">Conference analytics</h1>
                    <div class="drip-subtitle">
                        <x-icon name="bar-chart" :size="20" />
                        <span>{{ $call ? $call->title : 'All calls' }}</span>
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.conference.analytics') }}" class="drip-filters">
                    <select name="call" class="drip-select" aria-label="Choose a call" onchange="this.form.submit()" style="min-width:260px;">
                        <option value="">All calls</option>
                        @foreach ($calls as $option)
                            <option value="{{ $option->id }}" @selected($call && $call->id === $option->id)>{{ \Illuminate\Support\Str::limit($option->title, 50) }}</option>
                        @endforeach
                    </select>
                    @if ($call)
                        <a href="{{ route('admin.conference.applications.index', ['call' => $call->id]) }}" class="drip-btn drip-btn--outline"><x-icon name="users" :size="18" /> Applications</a>
                    @endif
                </form>
            </div>

            <div class="drip-stats drip-stats--4">
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="copy" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($stats['submitted']) }}</div><div class="drip-stat__label">Submitted</div></div></div>
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="clock" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($stats['awaiting']) }}</div><div class="drip-stat__label">Awaiting decision</div></div></div>
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="circle-check" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($stats['approved']) }}</div><div class="drip-stat__label">Approved</div></div></div>
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="circle-x" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($stats['not_approved']) }}</div><div class="drip-stat__label">Not approved</div></div></div>
            </div>

            @if ($stats['submitted'] === 0)
                <div class="drip-card" style="margin-top:32px;">
                    <div class="drip-empty" style="padding:56px 24px;">
                        <strong>No submitted applications yet</strong>
                        The charts and totals appear here once applications have been submitted.
                    </div>
                </div>
            @else
                <div class="drip-two-col">
                    <div class="drip-stack">
                        @include('conference.admin._breakdown', ['title' => 'Beneficiary type', 'rows' => $breakdowns['Beneficiary type'], 'total' => $stats['submitted']])
                        @include('conference.admin._breakdown', ['title' => 'Local or overseas', 'rows' => $breakdowns['Local or overseas'], 'total' => $stats['submitted']])
                    </div>
                    <div class="drip-stack">
                        @include('conference.admin._breakdown', ['title' => 'Staff category', 'rows' => $breakdowns['Staff category'], 'total' => $stats['submitted']])
                        @include('conference.admin._breakdown', ['title' => 'Departments (top 8)', 'rows' => $departments, 'total' => $stats['submitted']])
                    </div>
                </div>

                <div class="drip-section-head">
                    <div>
                        <h2 class="drip-section-title">Costs</h2>
                        <div class="drip-section-sub">Totals per currency. Different currencies are never added together.</div>
                    </div>
                </div>

                <div class="drip-card">
                    <div class="drip-table-wrap">
                        <table class="drip-table" style="min-width:640px;">
                            <thead>
                                <tr><th>Currency</th><th>Requested (all submitted)</th><th>Awaiting decision</th><th>Approved</th></tr>
                            </thead>
                            <tbody>
                                @foreach ($costs as $row)
                                    <tr>
                                        <td><strong>{{ $row->cost_currency }}</strong></td>
                                        <td style="white-space:nowrap;">{{ $money($row->requested) }}</td>
                                        <td style="white-space:nowrap;">{{ $money($row->awaiting) }}</td>
                                        <td style="white-space:nowrap; font-weight:600;">{{ $money($row->approved) }}</td>
                                    </tr>
                                @endforeach
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

            @if (! $call)
                <div class="drip-section-head">
                    <div>
                        <h2 class="drip-section-title">By call</h2>
                        <div class="drip-section-sub">Open a call's applications, or export its records.</div>
                    </div>
                </div>

                <div class="drip-card">
                    <div class="drip-table-wrap">
                        <table class="drip-table" style="min-width:900px;">
                            <thead>
                                <tr><th>Call</th><th>Status</th><th>Submitted</th><th>Awaiting</th><th>Approved</th><th>Not approved</th><th>Action</th></tr>
                            </thead>
                            <tbody>
                                @forelse ($per_call as $row)
                                    @php
                                        $call_status = $row->computed_status;
                                        $pill = match ($call_status) { 'Open' => 'drip-pill--success', 'Upcoming' => 'drip-pill--warning', default => 'drip-pill--muted' };
                                    @endphp
                                    <tr>
                                        <td style="font-weight:600;">{{ $row->title }}</td>
                                        <td><span class="drip-pill {{ $pill }}">{{ $call_status }}</span></td>
                                        <td>{{ $row->submitted_count }}</td>
                                        <td>{{ $row->awaiting_count }}</td>
                                        <td>{{ $row->approved_count }}</td>
                                        <td>{{ $row->not_approved_count }}</td>
                                        <td>
                                            <div class="drip-row-actions">
                                                <a href="{{ route('admin.conference.analytics', ['call' => $row->id]) }}" class="drip-btn drip-btn--outline drip-btn--sm"><x-icon name="bar-chart" :size="15" /> Analytics</a>
                                                <a href="{{ route('admin.conference.applications.index', ['call' => $row->id]) }}" class="drip-btn drip-btn--primary drip-btn--sm"><x-icon name="users" :size="15" /> Applications</a>
                                            </div>
                                        </td>
                                    </tr>
                                @empty
                                    <tr><td colspan="7" class="drip-empty"><strong>No calls yet</strong>Create a call to start receiving applications.</td></tr>
                                @endforelse
                            </tbody>
                        </table>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-admin-layout>
