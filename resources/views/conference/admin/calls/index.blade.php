<x-admin-layout>
    <div class="drip-page">
        <div class="drip-container">

            <nav class="drip-breadcrumb" aria-label="Breadcrumb">
                <span aria-current="page">Conference Grants</span>
            </nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1">
                        Conference Grants
                        <span class="drip-count">{{ str_pad($stats['total'], 2, '0', STR_PAD_LEFT) }}</span>
                    </h1>
                    <div class="drip-subtitle">
                        <x-icon name="graduation-cap" :size="20" />
                        <span>TETFund conference and workshop attendance grant calls</span>
                    </div>
                </div>

                <div class="drip-actions">
                    <a href="{{ route('admin.conference.analytics') }}" class="drip-btn drip-btn--outline"><x-icon name="bar-chart" :size="18" /> Analytics</a>
                    <a href="{{ route('admin.conference.calls.create') }}" class="drip-btn drip-btn--primary">
                        <x-icon name="plus" :size="18" /> New call
                    </a>
                </div>
            </div>

            @if (session('success'))
                <div class="drip-alert drip-alert--success"><x-icon name="circle-check" :size="18" /> <span>{{ session('success') }}</span></div>
            @endif
            @if (session('error'))
                <div class="drip-alert drip-alert--error"><x-icon name="circle-x" :size="18" /> <span>{{ session('error') }}</span></div>
            @endif

            {{-- summary strip --}}
            <div class="drip-stats">
                <div class="drip-stat">
                    <span class="drip-stat__icon"><x-icon name="calendar" :size="22" /></span>
                    <div>
                        <div class="drip-stat__value">{{ str_pad($stats['total'], 2, '0', STR_PAD_LEFT) }}</div>
                        <div class="drip-stat__label">Total calls</div>
                    </div>
                </div>
                <div class="drip-stat">
                    <span class="drip-stat__icon"><x-icon name="circle-check" :size="22" /></span>
                    <div>
                        <div class="drip-stat__value">{{ str_pad($stats['open'], 2, '0', STR_PAD_LEFT) }}</div>
                        <div class="drip-stat__label">Open now</div>
                    </div>
                </div>
                <div class="drip-stat">
                    <span class="drip-stat__icon"><x-icon name="copy" :size="22" /></span>
                    <div>
                        <div class="drip-stat__value">{{ str_pad($stats['submitted'], 2, '0', STR_PAD_LEFT) }}</div>
                        <div class="drip-stat__label">Applications submitted</div>
                    </div>
                </div>
            </div>

            <div class="drip-section-head">
                <div>
                    <h2 class="drip-section-title">Calls</h2>
                    <div class="drip-section-sub">Create and manage conference grant calls</div>
                </div>

                <form method="GET" action="{{ route('admin.conference.calls.index') }}" class="drip-filters">
                    <label class="drip-field">
                        <x-icon name="search" :size="18" />
                        <input type="search" name="q" value="{{ $search }}" class="drip-input" placeholder="Search calls..." autocomplete="off" aria-label="Search calls">
                    </label>

                    <select name="status" class="drip-select" aria-label="Filter by status" onchange="this.form.submit()">
                        <option value="">All statuses</option>
                        @foreach (['open' => 'Open', 'upcoming' => 'Upcoming', 'closed' => 'Closed', 'draft' => 'Draft', 'archived' => 'Archived'] as $value => $label)
                            <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>

                    @if ($search !== '' || $status)
                        <a href="{{ route('admin.conference.calls.index') }}" class="drip-clear">Clear</a>
                    @endif
                </form>
            </div>

            <div class="drip-card">
                <div class="drip-table-wrap">
                    <table class="drip-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Call</th>
                                <th>Deadline</th>
                                <th>Who can apply</th>
                                <th>Applications</th>
                                <th>Status</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($calls as $call)
                                @php
                                    $call_status = $call->computed_status;
                                    $pill = match ($call_status) { 'Open' => 'drip-pill--success', 'Upcoming' => 'drip-pill--warning', default => 'drip-pill--muted' };
                                    $pill_icon = match ($call_status) { 'Open' => 'circle-check', 'Upcoming' => 'clock', default => 'minus' };
                                    $days_left = $call_status === 'Open' ? (int) now()->startOfDay()->diffInDays($call->close_date) : null;
                                    $drafts = $call->applications_total - $call->applications_submitted;
                                @endphp
                                <tr>
                                    <td class="drip-num">{{ str_pad($calls->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        <a class="drip-app-title" href="{{ route('admin.conference.calls.edit', ['call' => $call->id]) }}">{{ $call->title }}</a>
                                        <div class="drip-app-meta">
                                            Opens {{ $call->open_date->format('M j, Y') }} &middot; Conferences from {{ $call->earliest_conference_date->format('M j, Y') }} &middot; up to {{ $call->max_duration_days }} {{ \Illuminate\Support\Str::plural('day', $call->max_duration_days) }}
                                        </div>
                                    </td>
                                    <td>
                                        {{ $call->close_date->format('M j, Y') }}
                                        @if (! is_null($days_left))
                                            <div class="drip-app-meta">{{ $days_left === 0 ? 'Closes today' : $days_left.' '.\Illuminate\Support\Str::plural('day', $days_left).' left' }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="drip-chips">
                                            @if ($call->allow_teaching)<span class="drip-chip">Teaching</span>@endif
                                            @if ($call->allow_non_teaching)<span class="drip-chip">Non-teaching</span>@endif
                                        </div>
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.conference.applications.index', ['call' => $call->id]) }}" class="drip-reviews drip-reviews--active" style="text-decoration:none;"><x-icon name="users" :size="16" /> {{ $call->applications_submitted }}</a>
                                        @if ($drafts > 0)
                                            <div class="drip-app-meta">+ {{ $drafts }} {{ \Illuminate\Support\Str::plural('draft', $drafts) }}</div>
                                        @endif
                                    </td>
                                    <td><span class="drip-pill {{ $pill }}"><x-icon :name="$pill_icon" :size="14" />{{ $call_status }}</span></td>
                                    <td>
                                        <div class="drip-row-actions">
                                            <a href="{{ route('admin.conference.applications.index', ['call' => $call->id]) }}" class="drip-btn drip-btn--primary drip-btn--sm"><x-icon name="users" :size="15" /> Applications</a>
                                            <a href="{{ route('admin.conference.calls.edit', ['call' => $call->id]) }}" class="drip-btn drip-btn--outline drip-btn--sm"><x-icon name="pencil" :size="15" /> Edit</a>

                                            @if ($call->applications_total > 0)
                                                <span class="drip-btn drip-btn--outline drip-btn--sm" style="opacity:.45; cursor:not-allowed;" title="This call has applications. Archive it instead of deleting."><x-icon name="trash" :size="15" /> Delete</span>
                                            @else
                                                <form method="POST" action="{{ route('admin.conference.calls.destroy', ['call' => $call->id]) }}" onsubmit="return confirm('Delete this call? This cannot be undone.');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="drip-btn drip-btn--danger drip-btn--sm"><x-icon name="trash" :size="15" /> Delete</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="7" class="drip-empty">
                                        @if ($stats['total'] === 0)
                                            <strong>No conference calls yet</strong>
                                            Create the first call to start receiving applications.
                                            <div style="margin-top:16px;"><a href="{{ route('admin.conference.calls.create') }}" class="drip-btn drip-btn--primary"><x-icon name="plus" :size="18" /> New call</a></div>
                                        @else
                                            <strong>No calls match your search</strong>
                                            Try a different word, or <a href="{{ route('admin.conference.calls.index') }}" class="drip-clear">clear the filters</a>.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($calls->total() > 0)
                    @php
                        $current = $calls->currentPage();
                        $last = $calls->lastPage();
                        $pages = collect([1, $last])->merge(range(max(1, $current - 1), min($last, $current + 1)))->unique()->sort()->values();
                        $previous_page = 0;
                    @endphp
                    <div class="drip-table-foot">
                        <span>Showing {{ $calls->firstItem() }}&ndash;{{ $calls->lastItem() }} of {{ $calls->total() }} {{ \Illuminate\Support\Str::plural('call', $calls->total()) }}</span>

                        <nav class="drip-pager" aria-label="Pagination">
                            @if ($calls->onFirstPage())
                                <span class="is-disabled" aria-hidden="true"><x-icon name="chevron-left" :size="16" /></span>
                            @else
                                <a href="{{ $calls->previousPageUrl() }}" aria-label="Previous page"><x-icon name="chevron-left" :size="16" /></a>
                            @endif

                            @foreach ($pages as $page)
                                @if ($page - $previous_page > 1)
                                    <span>&hellip;</span>
                                @endif
                                @if ($page === $current)
                                    <span class="is-current" aria-current="page">{{ $page }}</span>
                                @else
                                    <a href="{{ $calls->url($page) }}">{{ $page }}</a>
                                @endif
                                @php $previous_page = $page; @endphp
                            @endforeach

                            @if ($calls->hasMorePages())
                                <a href="{{ $calls->nextPageUrl() }}" aria-label="Next page"><x-icon name="chevron-right" :size="16" /></a>
                            @else
                                <span class="is-disabled" aria-hidden="true"><x-icon name="chevron-right" :size="16" /></span>
                            @endif
                        </nav>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-admin-layout>
