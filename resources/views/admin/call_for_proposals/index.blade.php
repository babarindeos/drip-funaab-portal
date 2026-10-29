<x-admin-layout>
    @php $pad = fn ($n) => str_pad($n, 2, '0', STR_PAD_LEFT); @endphp

    <div class="drip-page">
        <div class="drip-container">

            <nav class="drip-breadcrumb" aria-label="Breadcrumb">
                <span aria-current="page">Call for Proposals</span>
            </nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1">Call for Proposals <span class="drip-count">{{ $pad($stats['total']) }}</span></h1>
                    <div class="drip-subtitle"><x-icon name="book-open" :size="20" /> <span>Research proposal calls, applications and reviews</span></div>
                </div>
                <div class="drip-actions">
                    <a href="{{ route('admin.call_for_proposals.create') }}" class="drip-btn drip-btn--primary"><x-icon name="plus" :size="18" /> New call</a>
                </div>
            </div>

            <x-flash />

            <div class="drip-stats">
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="calendar" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($stats['total']) }}</div><div class="drip-stat__label">Total calls</div></div></div>
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="circle-check" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($stats['open']) }}</div><div class="drip-stat__label">Open now</div></div></div>
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="copy" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($stats['applications']) }}</div><div class="drip-stat__label">Applications received</div></div></div>
            </div>

            <div class="drip-section-head">
                <div>
                    <h2 class="drip-section-title">Calls</h2>
                    <div class="drip-section-sub">Create a call, then follow its applications through review</div>
                </div>

                <form method="GET" action="{{ route('admin.call_for_proposals.index') }}" class="drip-filters">
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
                        <a href="{{ route('admin.call_for_proposals.index') }}" class="drip-clear">Clear</a>
                    @endif
                </form>
            </div>

            <div class="drip-card">
                <div class="drip-table-wrap">
                    <table class="drip-table" style="min-width: 940px;">
                        <thead>
                            <tr><th>No.</th><th>Call</th><th>Deadline</th><th>Applications</th><th>Status</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($call_for_proposals as $call)
                                @php
                                    $call_status = $call->computed_status;
                                    $pill = match ($call_status) { 'Open' => 'drip-pill--success', 'Upcoming' => 'drip-pill--warning', default => 'drip-pill--muted' };
                                    $pill_icon = match ($call_status) { 'Open' => 'circle-check', 'Upcoming' => 'clock', default => 'minus' };
                                    $days_left = $call_status === 'Open' ? (int) now()->startOfDay()->diffInDays($call->close_date) : null;
                                @endphp
                                <tr>
                                    <td class="drip-num">{{ $pad($call_for_proposals->firstItem() + $loop->index) }}</td>
                                    <td>
                                        <a class="drip-app-title" href="{{ route('admin.call_for_proposals.show', ['call_for_proposal' => $call->id]) }}">{{ $call->title }}</a>
                                        <div class="drip-app-meta">Opens {{ $call->open_date->format('M j, Y') }}</div>
                                    </td>
                                    <td>
                                        {{ $call->close_date->format('M j, Y') }}
                                        @if (! is_null($days_left))
                                            <div class="drip-app-meta">{{ $days_left === 0 ? 'Closes today' : $days_left.' '.\Illuminate\Support\Str::plural('day', $days_left).' left' }}</div>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.call_for_proposals.submissions', ['call_for_proposal' => $call->id]) }}" class="drip-reviews drip-reviews--active" style="text-decoration:none;"><x-icon name="users" :size="16" /> {{ $call->proposal_applications_count }}</a>
                                        @if ($call->reviews_count > 0)
                                            <div class="drip-app-meta">{{ $call->reviews_count }} sent for review</div>
                                        @endif
                                    </td>
                                    <td><span class="drip-pill {{ $pill }}"><x-icon :name="$pill_icon" :size="14" />{{ $call_status }}</span></td>
                                    <td>
                                        <div class="drip-row-actions">
                                            <a href="{{ route('admin.call_for_proposals.submissions', ['call_for_proposal' => $call->id]) }}" class="drip-btn drip-btn--primary drip-btn--sm"><x-icon name="users" :size="15" /> Applications</a>
                                            <a href="{{ route('admin.call_for_proposals.results', ['call_for_proposal' => $call->id]) }}" class="drip-btn drip-btn--outline drip-btn--sm"><x-icon name="bar-chart" :size="15" /> Results</a>
                                            <a href="{{ route('admin.call_for_proposals.edit', ['call_for_proposal' => $call->id]) }}" class="drip-btn drip-btn--outline drip-btn--sm"><x-icon name="pencil" :size="15" /> Edit</a>
                                            @if ($call->proposal_applications_count > 0)
                                                <span class="drip-btn drip-btn--outline drip-btn--sm" style="opacity:.45; cursor:not-allowed;" title="This call has applications. Archive it instead of deleting."><x-icon name="trash" :size="15" /> Delete</span>
                                            @else
                                                <form method="POST" action="{{ route('admin.call_for_proposals.destroy', ['call_for_proposal' => $call->id]) }}" onsubmit="return confirm('Delete this call? This cannot be undone.');">
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
                                    <td colspan="6" class="drip-empty">
                                        @if ($stats['total'] === 0)
                                            <strong>No calls yet</strong>
                                            Create the first call to start receiving proposals.
                                            <div style="margin-top:16px;"><a href="{{ route('admin.call_for_proposals.create') }}" class="drip-btn drip-btn--primary"><x-icon name="plus" :size="18" /> New call</a></div>
                                        @else
                                            <strong>No calls match your search</strong>
                                            Try different words, or <a href="{{ route('admin.call_for_proposals.index') }}" class="drip-clear">clear the filters</a>.
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($call_for_proposals->total() > 0)
                    @php
                        $current = $call_for_proposals->currentPage();
                        $last = $call_for_proposals->lastPage();
                        $pages = collect([1, $last])->merge(range(max(1, $current - 1), min($last, $current + 1)))->unique()->sort()->values();
                        $previous_page = 0;
                    @endphp
                    <div class="drip-table-foot">
                        <span>Showing {{ $call_for_proposals->firstItem() }}&ndash;{{ $call_for_proposals->lastItem() }} of {{ $call_for_proposals->total() }} {{ \Illuminate\Support\Str::plural('call', $call_for_proposals->total()) }}</span>
                        <nav class="drip-pager" aria-label="Pagination">
                            @if ($call_for_proposals->onFirstPage())
                                <span class="is-disabled" aria-hidden="true"><x-icon name="chevron-left" :size="16" /></span>
                            @else
                                <a href="{{ $call_for_proposals->previousPageUrl() }}" aria-label="Previous page"><x-icon name="chevron-left" :size="16" /></a>
                            @endif
                            @foreach ($pages as $page)
                                @if ($page - $previous_page > 1)<span>&hellip;</span>@endif
                                @if ($page === $current)
                                    <span class="is-current" aria-current="page">{{ $page }}</span>
                                @else
                                    <a href="{{ $call_for_proposals->url($page) }}">{{ $page }}</a>
                                @endif
                                @php $previous_page = $page; @endphp
                            @endforeach
                            @if ($call_for_proposals->hasMorePages())
                                <a href="{{ $call_for_proposals->nextPageUrl() }}" aria-label="Next page"><x-icon name="chevron-right" :size="16" /></a>
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
