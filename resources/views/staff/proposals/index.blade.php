<x-staff-layout>
    <div class="drip-page">
        <div class="drip-container">

            <nav class="drip-breadcrumb" aria-label="Breadcrumb"><span aria-current="page">Call for Proposals</span></nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1">Call for Proposals <span class="drip-count">{{ str_pad($call_for_proposals->total(), 2, '0', STR_PAD_LEFT) }}</span></h1>
                    <div class="drip-subtitle"><x-icon name="book-open" :size="20" /> <span>Research calls you can apply to</span></div>
                </div>
            </div>

            <x-flash />

            <div class="drip-section-head">
                <div>
                    <h2 class="drip-section-title">Available calls</h2>
                    <div class="drip-section-sub">Open a call to read it and apply</div>
                </div>
            </div>

            <div class="drip-card">
                <div class="drip-table-wrap">
                    <table class="drip-table" style="min-width:860px;">
                        <thead>
                            <tr><th>No.</th><th>Call</th><th>Deadline</th><th>Call status</th><th>My application</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($call_for_proposals as $call)
                                @php
                                    $call_status = $call->computed_status;
                                    $pill = match ($call_status) { 'Open' => 'drip-pill--success', 'Upcoming' => 'drip-pill--warning', default => 'drip-pill--muted' };
                                    $pill_icon = match ($call_status) { 'Open' => 'circle-check', 'Upcoming' => 'clock', default => 'minus' };
                                    $application = $mine->get($call->id);
                                    $days_left = $call_status === 'Open' ? (int) now()->startOfDay()->diffInDays($call->close_date) : null;
                                    $target = route('staff.call_for_proposals.application', ['uuid' => $call->uuid]);
                                @endphp
                                <tr>
                                    <td class="drip-num">{{ str_pad($call_for_proposals->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        <a class="drip-app-title" href="{{ $target }}">{{ $call->title }}</a>
                                        <div class="drip-app-meta">Opens {{ $call->open_date->format('M j, Y') }}</div>
                                    </td>
                                    <td>
                                        {{ $call->close_date->format('M j, Y') }}
                                        @if (! is_null($days_left))
                                            <div class="drip-app-meta">{{ $days_left === 0 ? 'Closes today' : $days_left.' '.\Illuminate\Support\Str::plural('day', $days_left).' left' }}</div>
                                        @endif
                                    </td>
                                    <td><span class="drip-pill {{ $pill }}"><x-icon :name="$pill_icon" :size="14" />{{ $call_status }}</span></td>
                                    <td>
                                        @if ($application)
                                            <x-status-badge :status="$application->status" />
                                        @else
                                            <span class="drip-reviews"><x-icon name="minus" :size="16" /> Not started</span>
                                        @endif
                                    </td>
                                    <td>
                                        <div class="drip-row-actions">
                                            @if ($application)
                                                <a href="{{ $target }}" class="drip-btn drip-btn--outline drip-btn--sm"><x-icon name="eye" :size="15" /> View application</a>
                                            @elseif ($call_status === 'Open')
                                                <a href="{{ $target }}" class="drip-btn drip-btn--primary drip-btn--sm"><x-icon name="plus" :size="15" /> Apply</a>
                                            @else
                                                <a href="{{ $target }}" class="drip-btn drip-btn--outline drip-btn--sm">Details</a>
                                            @endif
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="drip-empty">
                                        <strong>No calls are available right now</strong>
                                        New calls for proposals will appear here when they are published.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($call_for_proposals->hasPages())
                    <div class="drip-table-foot">
                        <span>Showing {{ $call_for_proposals->firstItem() }}&ndash;{{ $call_for_proposals->lastItem() }} of {{ $call_for_proposals->total() }} calls</span>
                        <nav class="drip-pager" aria-label="Pagination">
                            @if ($call_for_proposals->onFirstPage())<span class="is-disabled"><x-icon name="chevron-left" :size="16" /></span>@else<a href="{{ $call_for_proposals->previousPageUrl() }}" aria-label="Previous page"><x-icon name="chevron-left" :size="16" /></a>@endif
                            <span class="is-current">{{ $call_for_proposals->currentPage() }}</span>
                            @if ($call_for_proposals->hasMorePages())<a href="{{ $call_for_proposals->nextPageUrl() }}" aria-label="Next page"><x-icon name="chevron-right" :size="16" /></a>@else<span class="is-disabled"><x-icon name="chevron-right" :size="16" /></span>@endif
                        </nav>
                    </div>
                @endif
            </div>
        </div>
    </div>
</x-staff-layout>
