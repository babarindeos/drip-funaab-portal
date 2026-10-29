<x-admin-layout>
    <div class="drip-page">
        <div class="drip-container">

            <nav class="drip-breadcrumb" aria-label="Breadcrumb"><span aria-current="page">Reviewers</span></nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1">Reviewers <span class="drip-count">{{ str_pad($total, 2, '0', STR_PAD_LEFT) }}</span></h1>
                    <div class="drip-subtitle"><x-icon name="users" :size="20" /> <span>People who score proposals for the Call for Proposals</span></div>
                </div>
                <div class="drip-actions">
                    <a href="{{ route('admin.reviewers.create') }}" class="drip-btn drip-btn--primary"><x-icon name="plus" :size="18" /> Add reviewer</a>
                </div>
            </div>

            <x-flash />

            <div class="drip-section-head">
                <div>
                    <h2 class="drip-section-title">All reviewers</h2>
                    <div class="drip-section-sub">Reviewers receive a private link by email for each proposal they are sent</div>
                </div>

                <form method="GET" action="{{ route('admin.reviewers.index') }}" class="drip-filters">
                    <label class="drip-field">
                        <x-icon name="search" :size="18" />
                        <input type="search" name="q" value="{{ $search }}" class="drip-input" placeholder="Search name, email, expertise..." autocomplete="off" aria-label="Search reviewers">
                    </label>
                    @if ($search !== '')
                        <a href="{{ route('admin.reviewers.index') }}" class="drip-clear">Clear</a>
                    @endif
                </form>
            </div>

            <div class="drip-card">
                <div class="drip-table-wrap">
                    <table class="drip-table" style="min-width:760px;">
                        <thead>
                            <tr><th>No.</th><th>Reviewer</th><th>Expertise</th><th>Proposals sent</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($reviewers as $reviewer)
                                <tr>
                                    <td class="drip-num">{{ str_pad($reviewers->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        <a class="drip-app-title" href="{{ route('admin.reviewers.edit', ['reviewer' => $reviewer->id]) }}">{{ $reviewer->name }}</a>
                                        <div class="drip-app-meta">{{ $reviewer->email }}</div>
                                    </td>
                                    <td>{{ $reviewer->expertise_area ?: '-' }}</td>
                                    <td><span class="drip-reviews {{ $reviewer->assignments_count > 0 ? 'drip-reviews--active' : '' }}"><x-icon name="send" :size="16" /> {{ $reviewer->assignments_count }}</span></td>
                                    <td>
                                        <div class="drip-row-actions">
                                            <a href="{{ route('admin.reviewers.edit', ['reviewer' => $reviewer->id]) }}" class="drip-btn drip-btn--outline drip-btn--sm"><x-icon name="pencil" :size="15" /> Edit</a>
                                            @if ($reviewer->assignments_count > 0)
                                                <span class="drip-btn drip-btn--outline drip-btn--sm" style="opacity:.45; cursor:not-allowed;" title="This reviewer has been sent proposals, so they cannot be deleted."><x-icon name="trash" :size="15" /> Delete</span>
                                            @else
                                                <form method="POST" action="{{ route('admin.reviewers.destroy', ['reviewer' => $reviewer->id]) }}" onsubmit="return confirm('Delete this reviewer?');">
                                                    @csrf
                                                    <button type="submit" class="drip-btn drip-btn--danger drip-btn--sm"><x-icon name="trash" :size="15" /> Delete</button>
                                                </form>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="5" class="drip-empty">
                                        @if ($total === 0)
                                            <strong>No reviewers yet</strong>
                                            Add the people who will score proposals.
                                            <div style="margin-top:16px;"><a href="{{ route('admin.reviewers.create') }}" class="drip-btn drip-btn--primary"><x-icon name="plus" :size="18" /> Add reviewer</a></div>
                                        @else
                                            <strong>No reviewers match your search</strong>
                                            <a href="{{ route('admin.reviewers.index') }}" class="drip-clear">Clear the search</a>
                                        @endif
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($reviewers->hasPages())
                    <div class="drip-table-foot">
                        <span>Showing {{ $reviewers->firstItem() }}&ndash;{{ $reviewers->lastItem() }} of {{ $reviewers->total() }} reviewers</span>
                        <nav class="drip-pager" aria-label="Pagination">
                            @if ($reviewers->onFirstPage())<span class="is-disabled"><x-icon name="chevron-left" :size="16" /></span>@else<a href="{{ $reviewers->previousPageUrl() }}" aria-label="Previous page"><x-icon name="chevron-left" :size="16" /></a>@endif
                            <span class="is-current">{{ $reviewers->currentPage() }}</span>
                            @if ($reviewers->hasMorePages())<a href="{{ $reviewers->nextPageUrl() }}" aria-label="Next page"><x-icon name="chevron-right" :size="16" /></a>@else<span class="is-disabled"><x-icon name="chevron-right" :size="16" /></span>@endif
                        </nav>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-admin-layout>
