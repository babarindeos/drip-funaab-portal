<x-admin-layout>
    <div class="drip-page">
        <div class="drip-container">

            {{-- breadcrumb --}}
            <nav class="drip-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('admin.call_for_proposals.index') }}">Call for Proposals</a>
                <x-icon name="chevron-right" :size="14" />
                <a href="{{ route('admin.call_for_proposals.show', ['call_for_proposal' => $call_for_proposal->id]) }}">{{ \Illuminate\Support\Str::limit($call_for_proposal->title, 42) }}</a>
                <x-icon name="chevron-right" :size="14" />
                <span aria-current="page">Applications</span>
            </nav>

            {{-- page heading --}}
            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1">
                        Applications
                        <span class="drip-count">{{ str_pad($stats['total'], 2, '0', STR_PAD_LEFT) }}</span>
                    </h1>
                    <div class="drip-subtitle">
                        <x-icon name="book-open" :size="20" />
                        <span>{{ $call_for_proposal->title }}</span>
                    </div>
                </div>

                <div class="drip-actions">
                    <a href="{{ route('admin.call_for_proposals.index') }}" class="drip-btn drip-btn--outline">
                        <x-icon name="arrow-left" :size="18" /> Call for Proposals
                    </a>
                    <a href="{{ route('admin.call_for_proposals.results', ['call_for_proposal' => $call_for_proposal->id]) }}" class="drip-btn drip-btn--primary">
                        <x-icon name="bar-chart" :size="18" /> View results <x-icon name="arrow-up-right" :size="16" />
                    </a>
                </div>
            </div>

            {{-- summary strip --}}
            <div class="drip-stats">
                <div class="drip-stat">
                    <span class="drip-stat__icon"><x-icon name="copy" :size="22" /></span>
                    <div>
                        <div class="drip-stat__value">{{ str_pad($stats['total'], 2, '0', STR_PAD_LEFT) }}</div>
                        <div class="drip-stat__label">Total applications</div>
                    </div>
                </div>
                <div class="drip-stat">
                    <span class="drip-stat__icon"><x-icon name="circle-check" :size="22" /></span>
                    <div>
                        <div class="drip-stat__value">{{ str_pad($stats['acknowledged'], 2, '0', STR_PAD_LEFT) }}</div>
                        <div class="drip-stat__label">Acknowledged</div>
                    </div>
                </div>
                <div class="drip-stat">
                    <span class="drip-stat__icon"><x-icon name="clock" :size="22" /></span>
                    <div>
                        <div class="drip-stat__value">{{ str_pad($stats['pending'], 2, '0', STR_PAD_LEFT) }}</div>
                        <div class="drip-stat__label">Pending review</div>
                    </div>
                </div>
            </div>

            {{-- section heading + search / filter --}}
            <div class="drip-section-head">
                <div>
                    <h2 class="drip-section-title">Submitted applications</h2>
                    <div class="drip-section-sub">All submissions for this call for proposals</div>
                </div>

                <form method="GET" action="{{ route('admin.call_for_proposals.submissions', ['call_for_proposal' => $call_for_proposal->id]) }}" class="drip-filters">
                    <label class="drip-field">
                        <x-icon name="search" :size="18" />
                        <input type="search" name="q" value="{{ $search }}" class="drip-input" placeholder="Search applications..." autocomplete="off" aria-label="Search applications">
                    </label>

                    <select name="status" class="drip-select" aria-label="Filter by status" onchange="this.form.submit()">
                        <option value="">All statuses</option>
                        @foreach (['pending' => 'Pending', 'acknowledged' => 'Acknowledged', 'accepted' => 'Accepted', 'rejected' => 'Rejected'] as $value => $label)
                            <option value="{{ $value }}" @selected($status === $value)>{{ $label }}</option>
                        @endforeach
                    </select>

                    @if ($search !== '' || $status)
                        <a href="{{ route('admin.call_for_proposals.submissions', ['call_for_proposal' => $call_for_proposal->id]) }}" class="drip-clear">Clear</a>
                    @endif
                </form>
            </div>

            {{-- applications table --}}
            <div class="drip-card">
                <div class="drip-table-wrap">
                    <table class="drip-table">
                        <thead>
                            <tr>
                                <th>No.</th>
                                <th>Application</th>
                                <th>Principal investigator</th>
                                <th>Status</th>
                                <th>Reviews</th>
                                <th>Action</th>
                            </tr>
                        </thead>
                        <tbody>
                            @forelse ($applications as $application)
                                @php
                                    $assigned = $application->reviews->count();
                                    $completed = $application->reviews->filter(fn ($review) => $review->is_reviewed)->count();
                                    $applicant = $application->owner
                                        ? trim($application->owner->surname.' '.$application->owner->firstname)
                                        : 'Unknown applicant';
                                @endphp
                                <tr>
                                    <td class="drip-num">{{ str_pad($applications->firstItem() + $loop->index, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        <a class="drip-app-title" href="{{ route('admin.call_for_proposals.proposal_application', ['call_for_proposal' => $call_for_proposal->id, 'proposal_application' => $application->id]) }}">{{ $application->proposal_title }}</a>
                                        <div class="drip-app-meta">Applicant: {{ $applicant }}</div>
                                        <div class="drip-doc-links">
                                            <a class="drip-doc-link" target="_blank" href="{{ route('documents.application', ['uuid' => $application->uuid, 'type' => 'title']) }}"><x-icon name="file-text" :size="14" /> Proposal title</a>
                                            <a class="drip-doc-link" target="_blank" href="{{ route('documents.application', ['uuid' => $application->uuid, 'type' => 'proposal']) }}"><x-icon name="file-text" :size="14" /> Document</a>
                                            <a class="drip-doc-link" target="_blank" href="{{ route('documents.application', ['uuid' => $application->uuid, 'type' => 'college_review']) }}"><x-icon name="file" :size="14" /> College review</a>
                                        </div>
                                    </td>
                                    <td>{{ $application->principal_investigator }}</td>
                                    <td><x-status-badge :status="$application->status" /></td>
                                    <td>
                                        @if ($assigned === 0)
                                            <span class="drip-reviews"><x-icon name="minus" :size="16" /> Not sent</span>
                                        @else
                                            <span class="drip-reviews {{ $completed >= $assigned ? 'drip-reviews--done' : 'drip-reviews--active' }}">
                                                <x-icon name="users" :size="16" /> {{ $completed }} / {{ $assigned }}
                                            </span>
                                        @endif
                                    </td>
                                    <td>
                                        <a href="{{ route('admin.call_for_proposals.proposal_application.send_to_reviewer', ['proposal_application' => $application->id]) }}" class="drip-btn drip-btn--outline drip-btn--sm">
                                            <x-icon name="send" :size="16" /> Send to reviewer
                                        </a>
                                    </td>
                                </tr>
                            @empty
                                <tr>
                                    <td colspan="6" class="drip-empty">
                                        @if ($stats['total'] === 0)
                                            <strong>No applications yet</strong>
                                            Applications appear here as staff submit them for this call.
                                        @else
                                            <strong>No applications match your search</strong>
                                            Try a different name or title, or <a href="{{ route('admin.call_for_proposals.submissions', ['call_for_proposal' => $call_for_proposal->id]) }}" class="drip-clear">clear the filters</a>.
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
                        $pages = collect([1, $last])
                                    ->merge(range(max(1, $current - 1), min($last, $current + 1)))
                                    ->unique()->sort()->values();
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
                                @if ($page - $previous_page > 1)
                                    <span>&hellip;</span>
                                @endif
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

            <div class="drip-note">
                <x-icon name="lock" :size="16" />
                Application information is confidential and restricted to authorised personnel.
            </div>

        </div>
    </div>
</x-admin-layout>
