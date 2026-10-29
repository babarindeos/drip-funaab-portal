<x-admin-layout>
    @php
        $labels = config('conference.staff_categories');
        $pad = fn ($n) => str_pad($n, 2, '0', STR_PAD_LEFT);
        $filters_active = $search !== '' || $status || $beneficiary || $sort !== 'newest';
    @endphp

    <div class="drip-page">
        <div class="drip-container">

            <nav class="drip-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('admin.conference.calls.index') }}">Conference Grants</a>
                <x-icon name="chevron-right" :size="14" />
                <a href="{{ route('admin.conference.calls.edit', ['call' => $call->id]) }}">{{ \Illuminate\Support\Str::limit($call->title, 42) }}</a>
                <x-icon name="chevron-right" :size="14" />
                <span aria-current="page">Applications</span>
            </nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1">Applications <span class="drip-count">{{ $pad($stats['submitted']) }}</span></h1>
                    <div class="drip-subtitle"><x-icon name="graduation-cap" :size="20" /> <span>{{ $call->title }}</span></div>
                </div>
                <div class="drip-actions">
                    <a href="{{ route('admin.conference.calls.index') }}" class="drip-btn drip-btn--outline"><x-icon name="arrow-left" :size="18" /> Conference Grants</a>
                    <a href="{{ route('admin.conference.analytics', ['call' => $call->id]) }}" class="drip-btn drip-btn--outline"><x-icon name="bar-chart" :size="18" /> Analytics</a>
                    @if ($stats['submitted'] > 0)
                        <a href="{{ route('admin.conference.applications.pdf', ['call' => $call->id]) }}" class="drip-btn drip-btn--outline"><x-icon name="download" :size="18" /> Export PDF</a>
                        <a href="{{ route('admin.conference.applications.csv', ['call' => $call->id]) }}" class="drip-btn drip-btn--primary"><x-icon name="download" :size="18" /> Export Excel (CSV)</a>
                    @endif
                </div>
            </div>

            @if (session('success'))
                <div class="drip-alert drip-alert--success"><x-icon name="circle-check" :size="18" /> <span>{{ session('success') }}</span></div>
            @endif
            @if (session('error'))
                <div class="drip-alert drip-alert--error"><x-icon name="circle-x" :size="18" /> <span>{{ session('error') }}</span></div>
            @endif

            <div class="drip-stats drip-stats--4">
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="copy" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($stats['submitted']) }}</div><div class="drip-stat__label">Submitted</div></div></div>
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="clock" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($stats['awaiting']) }}</div><div class="drip-stat__label">Awaiting decision</div></div></div>
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="circle-check" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($stats['approved']) }}</div><div class="drip-stat__label">Approved</div></div></div>
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="circle-x" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($stats['not_approved']) }}</div><div class="drip-stat__label">Not approved</div></div></div>
            </div>

            <div class="drip-section-head">
                <div>
                    <h2 class="drip-section-title">Submitted applications</h2>
                    <div class="drip-section-sub">
                        Review each application and record the committee's decision.
                        @if ($stats['drafts'] > 0) {{ $stats['drafts'] }} {{ \Illuminate\Support\Str::plural('draft', $stats['drafts']) }} not shown until submitted. @endif
                    </div>
                </div>

                <form method="GET" action="{{ route('admin.conference.applications.index', ['call' => $call->id]) }}" class="drip-filters">
                    <label class="drip-field">
                        <x-icon name="search" :size="18" />
                        <input type="search" name="q" value="{{ $search }}" class="drip-input" placeholder="Search name, title, department..." autocomplete="off" aria-label="Search applications">
                    </label>
                    <select name="status" class="drip-select" aria-label="Filter by status" onchange="this.form.submit()">
                        <option value="">All statuses</option>
                        @foreach (['submitted' => 'Awaiting decision', 'approved' => 'Approved', 'not_approved' => 'Not approved'] as $value => $label)
                            <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>
                    <select name="beneficiary" class="drip-select" aria-label="Filter by beneficiary type" onchange="this.form.submit()">
                        <option value="">All applicants</option>
                        <option value="first" @selected($beneficiary === 'first')>First-time beneficiaries</option>
                        <option value="previous" @selected($beneficiary === 'previous')>Previous beneficiaries</option>
                    </select>
                    <select name="sort" class="drip-select" aria-label="Sort order" onchange="this.form.submit()">
                        <option value="newest" @selected($sort === 'newest')>Newest first</option>
                        <option value="oldest" @selected($sort === 'oldest')>Oldest first</option>
                        <option value="priority" @selected($sort === 'priority')>First-time beneficiaries first</option>
                    </select>
                    @if ($filters_active)
                        <a href="{{ route('admin.conference.applications.index', ['call' => $call->id]) }}" class="drip-clear">Clear</a>
                    @endif
                </form>
            </div>

            <div class="drip-card">
                <div class="drip-table-wrap">
                    <table class="drip-table" style="min-width: 1020px;">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Applicant</th>
                                <th>Conference / workshop</th>
                                <th>Total cost</th>
                                <th>Beneficiary</th>
                                <th>Status</th>
                                <th>Submitted</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($applications as $application)
                                <tr>
                                    <td class="drip-num">{{ $pad($applications->firstItem() + $loop->index) }}</td>
                                    <td>
                                        <a class="drip-app-title" href="{{ route('admin.conference.applications.show', ['application' => $application->uuid]) }}">{{ $application->candidate_name ?: 'Unnamed applicant' }}</a>
                                        <div class="drip-app-meta">{{ $application->department ?: '-' }}</div>
                                        <div class="drip-chips" style="margin-top:8px;"><span class="drip-chip">{{ $labels[$application->staff_category] ?? 'Category not set' }}</span></div>
                                    </td>
                                    <td>
                                        <div style="font-weight:550;">{{ $application->conference_title ?: '-' }}</div>
                                        <div class="drip-app-meta">
                                            {{ optional($application->conference_start_date)->format('M j, Y') }}@if ($application->conference_end_date) &ndash; {{ $application->conference_end_date->format('M j, Y') }}@endif
                                            @if ($application->conference_venue)<br>{{ $application->conference_venue }}@endif
                                        </div>
                                    </td>
                                    <td style="white-space:nowrap;">{{ $application->cost_currency }} {{ number_format($application->total_cost, 2) }}</td>
                                    <td>
                                        @if ($application->previously_sponsored)
                                            <span class="drip-pill drip-pill--warning">Previous</span>
                                        @else
                                            <span class="drip-pill drip-pill--success">First-time</span>
                                        @endif
                                    </td>
                                    <td><x-status-badge :status="$application->status" /></td>
                                    <td style="white-space:nowrap;">{{ optional($application->submitted_at)->format('M j, Y') }}</td>
                                    <td>
                                        <a href="{{ route('admin.conference.applications.show', ['application' => $application->uuid]) }}" class="drip-btn drip-btn--primary drip-btn--sm"><x-icon name="eye" :size="15" /> Review</a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="8" class="drip-empty">
                                        @if ($stats['submitted'] === 0)
                                            <strong>No applications submitted yet</strong>
                                            Submitted applications appear here for review.
                                        @else
                                            <strong>No applications match your search</strong>
                                            Try different words, or <a href="{{ route('admin.conference.applications.index', ['call' => $call->id]) }}" class="drip-clear">clear the filters</a>.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($applications->total() > 0)
                    @php
                        $current = $applications->currentPage();
                        $last = $applications->lastPage();
                        $pages = collect([1, $last])->merge(range(max(1, $current - 1), min($last, $current + 1)))->unique()->sort()->values();
                        $previous_page = 0;
                    @endphp
                    <div class="drip-table-foot">
                        <span>Showing {{ $applications->firstItem() }}&ndash;{{ $applications->lastItem() }} of {{ $applications->total() }} {{ \Illuminate\Support\Str::plural('application', $applications->total()) }}</span>
                        <nav class="drip-pager" aria-label="Pagination">
                            @if ($applications->onFirstPage())
                                <span class="is-disabled" aria-hidden="true"><x-icon name="chevron-left" :size="16" /></span>
                            @else
                                <a href="{{ $applications->previousPageUrl() }}" aria-label="Previous page"><x-icon name="chevron-left" :size="16" /></a>
                            @endif
                            @foreach ($pages as $page)
                                @if ($page - $previous_page > 1)<span>&hellip;</span>@endif
                                @if ($page === $current)
                                    <span class="is-current" aria-current="page">{{ $page }}</span>
                                @else
                                    <a href="{{ $applications->url($page) }}">{{ $page }}</a>
                                @endif
                                @php $previous_page = $page; @endphp
                            @endforeach
                            @if ($applications->hasMorePages())
                                <a href="{{ $applications->nextPageUrl() }}" aria-label="Next page"><x-icon name="chevron-right" :size="16" /></a>
                            @else
                                <span class="is-disabled" aria-hidden="true"><x-icon name="chevron-right" :size="16" /></span>
                            @endif
                        </nav>
                    </div>
                @endif
            </div>

            <div class="drip-note"><x-icon name="lock" :size="16" /> Application information is confidential and restricted to authorised personnel.</div>
        </div>
    </div>
</x-admin-layout>
