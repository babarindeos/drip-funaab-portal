<x-staff-layout>
    <div class="drip-page">
        <div class="drip-container">

            <nav class="drip-breadcrumb" aria-label="Breadcrumb">
                <span aria-current="page">Conference Grants</span>
            </nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1">
                        Conference Grants
                        <span class="drip-count">{{ str_pad($calls->total(), 2, '0', STR_PAD_LEFT) }}</span>
                    </h1>
                    <div class="drip-subtitle">
                        <x-icon name="graduation-cap" :size="20" />
                        <span>Apply for a TETFund conference or workshop attendance grant</span>
                    </div>
                </div>
            </div>

            @if (session('success'))
                <div class="drip-alert drip-alert--success"><x-icon name="circle-check" :size="18" /> <span>{{ session('success') }}</span></div>
            @endif
            @if (session('error'))
                <div class="drip-alert drip-alert--error"><x-icon name="circle-x" :size="18" /> <span>{{ session('error') }}</span></div>
            @endif

            <div class="drip-section-head">
                <div>
                    <h2 class="drip-section-title">Available calls</h2>
                    <div class="drip-section-sub">Open a call to see what is required before you apply</div>
                </div>
            </div>

            <div class="drip-card">
                <div class="drip-table-wrap">
                    <table class="drip-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Call</th>
                                <th>Deadline</th>
                                <th>Call status</th>
                                <th>My application</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($calls as $call)
                                @php
                                    $call_status = $call->computed_status;
                                    $pill = match ($call_status) { 'Open' => 'drip-pill--success', 'Upcoming' => 'drip-pill--warning', default => 'drip-pill--muted' };
                                    $pill_icon = match ($call_status) { 'Open' => 'circle-check', 'Upcoming' => 'clock', default => 'minus' };
                                    $application = $mine->get($call->id);
                                    $days_left = $call_status === 'Open' ? (int) now()->startOfDay()->diffInDays($call->close_date) : null;
                                @endphp
                                <tr>
                                    <td class="drip-num">{{ str_pad($calls->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        <a class="drip-app-title" href="{{ route('staff.conference.calls.show', ['call' => $call->uuid]) }}">{{ $call->title }}</a>
                                        <div class="drip-app-meta">Opens {{ $call->open_date->format('M j, Y') }} &middot; Up to {{ $call->max_duration_days }} {{ \Illuminate\Support\Str::plural('day', $call->max_duration_days) }}</div>
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
                                            @if ($application && $application->isEditable() && $call->isOpen())
                                                <a href="{{ route('staff.conference.applications.edit', ['application' => $application->uuid]) }}" class="drip-btn drip-btn--primary drip-btn--sm"><x-icon name="pencil" :size="15" /> Continue</a>
                                            @elseif (! $application && $call->isOpen())
                                                <form method="POST" action="{{ route('staff.conference.calls.apply', ['call' => $call->uuid]) }}">
                                                    @csrf
                                                    <button type="submit" class="drip-btn drip-btn--primary drip-btn--sm"><x-icon name="plus" :size="15" /> Apply</button>
                                                </form>
                                            @endif
                                            @if ($application && (! $application->isEditable() || ! $call->isOpen()))
                                                <a href="{{ route('staff.conference.applications.show', ['application' => $application->uuid]) }}" class="drip-btn drip-btn--primary drip-btn--sm"><x-icon name="eye" :size="15" /> View</a>
                                            @endif
                                            <a href="{{ route('staff.conference.calls.show', ['call' => $call->uuid]) }}" class="drip-btn drip-btn--outline drip-btn--sm">Details</a>
                                        </div>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="drip-empty">
                                        <strong>No calls are open right now</strong>
                                        New conference and workshop grant calls will appear here when they are published.
                                    </td>
                                </tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>

                @if ($calls->hasPages())
                    <div class="drip-table-foot">
                        <span>Showing {{ $calls->firstItem() }}&ndash;{{ $calls->lastItem() }} of {{ $calls->total() }} calls</span>
                        <nav class="drip-pager" aria-label="Pagination">
                            @if ($calls->onFirstPage())
                                <span class="is-disabled"><x-icon name="chevron-left" :size="16" /></span>
                            @else
                                <a href="{{ $calls->previousPageUrl() }}" aria-label="Previous page"><x-icon name="chevron-left" :size="16" /></a>
                            @endif
                            <span class="is-current">{{ $calls->currentPage() }}</span>
                            @if ($calls->hasMorePages())
                                <a href="{{ $calls->nextPageUrl() }}" aria-label="Next page"><x-icon name="chevron-right" :size="16" /></a>
                            @else
                                <span class="is-disabled"><x-icon name="chevron-right" :size="16" /></span>
                            @endif
                        </nav>
                    </div>
                @endif
            </div>

        </div>
    </div>
</x-staff-layout>
