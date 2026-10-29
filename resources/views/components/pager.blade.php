@props(['paginator', 'noun' => 'items'])
{{-- Previous / page / next, in the DRIP style. Shows nothing when everything fits on one page. --}}
@if ($paginator->hasPages())
    <div class="drip-table-foot">
        <span>Showing {{ $paginator->firstItem() }}&ndash;{{ $paginator->lastItem() }} of {{ $paginator->total() }} {{ $noun }}</span>
        <nav class="drip-pager" aria-label="Pagination">
            @if ($paginator->onFirstPage())<span class="is-disabled"><x-icon name="chevron-left" :size="16" /></span>@else<a href="{{ $paginator->previousPageUrl() }}" aria-label="Previous page"><x-icon name="chevron-left" :size="16" /></a>@endif
            <span class="is-current">{{ $paginator->currentPage() }}</span>
            @if ($paginator->hasMorePages())<a href="{{ $paginator->nextPageUrl() }}" aria-label="Next page"><x-icon name="chevron-right" :size="16" /></a>@else<span class="is-disabled"><x-icon name="chevron-right" :size="16" /></span>@endif
        </nav>
    </div>
@endif
