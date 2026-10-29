<x-admin-layout>
    @php
        $call = $proposal_application->call_for_proposal;
        $assigned_ids = $proposal_application->reviews->pluck('reviewer_id')->all();
        $available = $reviewers->reject(fn ($reviewer) => in_array($reviewer->id, $assigned_ids));
        $applicant = $proposal_application->owner ? trim($proposal_application->owner->surname.' '.$proposal_application->owner->firstname) : 'Unknown applicant';
    @endphp

    <div class="drip-page">
        <div class="drip-container">

            <nav class="drip-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('admin.call_for_proposals.index') }}">Call for Proposals</a>
                <x-icon name="chevron-right" :size="14" />
                <a href="{{ route('admin.call_for_proposals.submissions', ['call_for_proposal' => $call->id]) }}">{{ \Illuminate\Support\Str::limit($call->title, 36) }}</a>
                <x-icon name="chevron-right" :size="14" />
                <span aria-current="page">Send to reviewer</span>
            </nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1" style="font-size:2rem;">Send to reviewer</h1>
                    <div class="drip-subtitle"><x-icon name="file-text" :size="20" /> <span>{{ $proposal_application->proposal_title }}</span></div>
                </div>
                <div class="drip-actions">
                    <a href="{{ route('admin.call_for_proposals.submissions', ['call_for_proposal' => $call->id]) }}" class="drip-btn drip-btn--outline"><x-icon name="arrow-left" :size="18" /> Applications</a>
                    <a href="{{ route('admin.call_for_proposals.proposal_application', ['call_for_proposal' => $call->id, 'proposal_application' => $proposal_application->id]) }}" class="drip-btn drip-btn--outline">Decision page</a>
                </div>
            </div>

            <x-flash />

            <div class="drip-two-col">
                {{-- the proposal --}}
                <div class="drip-card"><div class="drip-card__body">
                    <h2 class="drip-form-section__title">Proposal</h2>
                    <dl class="drip-dl" style="margin-top:16px;">
                        <dt>Title</dt><dd>{{ $proposal_application->proposal_title }}</dd>
                        <dt>Principal investigator</dt><dd>{{ $proposal_application->principal_investigator }}</dd>
                        <dt>Applicant</dt><dd>{{ $applicant }}</dd>
                        <dt>Status</dt><dd><x-status-badge :status="$proposal_application->status" /></dd>
                        <dt>Documents</dt>
                        <dd>
                            <div class="drip-doc-links" style="margin-top:0;">
                                <a class="drip-doc-link" target="_blank" href="{{ route('documents.application', ['uuid' => $proposal_application->uuid, 'type' => 'title']) }}"><x-icon name="file-text" :size="14" /> Title page</a>
                                <a class="drip-doc-link" target="_blank" href="{{ route('documents.application', ['uuid' => $proposal_application->uuid, 'type' => 'proposal']) }}"><x-icon name="file-text" :size="14" /> Proposal</a>
                                <a class="drip-doc-link" target="_blank" href="{{ route('documents.application', ['uuid' => $proposal_application->uuid, 'type' => 'college_review']) }}"><x-icon name="file" :size="14" /> College review</a>
                            </div>
                        </dd>
                    </dl>
                </div></div>

                {{-- send --}}
                <div class="drip-card"><div class="drip-card__body">
                    <h2 class="drip-form-section__title">Send to a reviewer</h2>
                    <p class="drip-form-section__sub">The reviewer is emailed a private link to the proposal and the scoring form. They do not need to sign in.</p>

                    @if ($available->isEmpty())
                        <div class="drip-callout">
                            @if ($reviewers->isEmpty())
                                There are no reviewers yet. <a href="{{ route('admin.reviewers.create') }}" class="drip-clear">Add a reviewer</a> first.
                            @else
                                This proposal has already been sent to every reviewer. <a href="{{ route('admin.reviewers.create') }}" class="drip-clear">Add another reviewer</a> to send it to someone else.
                            @endif
                        </div>
                    @else
                        <form method="POST" action="{{ route('admin.call_for_proposals.proposal_application.send_to_reviewer.store', ['proposal_application' => $proposal_application->id]) }}">
                            @csrf
                            <label class="drip-label" for="reviewer_id">Reviewer <span class="req">*</span></label>
                            <select id="reviewer_id" name="reviewer_id" class="drip-control @error('reviewer_id') is-invalid @enderror" required>
                                <option value="">Choose a reviewer...</option>
                                @foreach ($available as $reviewer)
                                    <option value="{{ $reviewer->id }}" @selected((string) old('reviewer_id') === (string) $reviewer->id)>{{ $reviewer->name }}@if ($reviewer->expertise_area) - {{ $reviewer->expertise_area }}@endif ({{ $reviewer->email }})</option>
                                @endforeach
                            </select>
                            @error('reviewer_id')<div class="drip-error">{{ $message }}</div>@enderror

                            <button type="submit" class="drip-btn drip-btn--primary" style="margin-top:18px;"><x-icon name="send" :size="18" /> Send to reviewer</button>
                        </form>
                    @endif
                </div></div>
            </div>

            {{-- who it has been sent to --}}
            <div class="drip-section-head">
                <div>
                    <h2 class="drip-section-title">Reviewers <span class="drip-count">{{ str_pad($proposal_application->reviews->count(), 2, '0', STR_PAD_LEFT) }}</span></h2>
                    <div class="drip-section-sub">The link below is what the reviewer was sent. Copy it if you need to send it yourself.</div>
                </div>
            </div>

            <div class="drip-card">
                <div class="drip-table-wrap">
                    <table class="drip-table" style="min-width:900px;">
                        <thead>
                            <tr><th>No.</th><th>Reviewer</th><th>Status</th><th>Review link</th><th>Action</th></tr>
                        </thead>
                        <tbody>
                            @forelse ($proposal_application->reviews as $review)
                                @php $link = $review->review_link ?: $review->buildReviewLink(); @endphp
                                <tr>
                                    <td class="drip-num">{{ str_pad($loop->iteration, 2, '0', STR_PAD_LEFT) }}</td>
                                    <td>
                                        <div style="font-weight:600;">{{ $review->reviewer->name }}</div>
                                        <div class="drip-app-meta">{{ $review->reviewer->email }}</div>
                                    </td>
                                    <td>
                                        @if ($review->is_reviewed)
                                            <span class="drip-pill drip-pill--solid"><x-icon name="circle-check" :size="14" />Review done</span>
                                        @else
                                            <span class="drip-pill drip-pill--warning"><x-icon name="clock" :size="14" />Awaiting review</span>
                                        @endif
                                        <div class="drip-app-meta" style="margin-top:8px;">
                                            @if ($review->emailed_at)
                                                <span style="color:var(--success);">Emailed {{ $review->emailed_at->format('M j, Y g:i A') }}</span>
                                            @elseif ($review->email_error)
                                                <span style="color:var(--destructive);" title="{{ $review->email_error }}">Email failed. Send the link yourself.</span>
                                            @else
                                                Not emailed yet
                                            @endif
                                        </div>
                                    </td>
                                    <td style="min-width:300px;">
                                        <input type="text" readonly value="{{ $link }}" id="link-{{ $review->id }}" onclick="this.select()" class="drip-control" style="height:38px; font-size:.8125rem;">
                                        <button type="button" onclick="copyLink('link-{{ $review->id }}', this)" class="drip-btn drip-btn--outline drip-btn--sm" style="margin-top:8px;"><x-icon name="copy" :size="15" /> Copy link</button>
                                    </td>
                                    <td>
                                        @if ($review->is_reviewed)
                                            <span class="drip-app-meta">Locked: the review has been submitted.</span>
                                        @else
                                            <div class="drip-row-actions" style="flex-wrap:wrap;">
                                                <form method="POST" action="{{ route('admin.call_for_proposals.proposal_application.proposal_reviewer.resend', ['proposal_reviewer' => $review->id]) }}">
                                                    @csrf
                                                    <button type="submit" class="drip-btn drip-btn--outline drip-btn--sm"><x-icon name="send" :size="15" /> Resend email</button>
                                                </form>
                                                <form method="POST" action="{{ route('admin.call_for_proposals.proposal_application.proposal_reviewer.destroy', ['proposal_reviewer' => $review->id]) }}" onsubmit="return confirm('Remove this reviewer from the proposal?');">
                                                    @csrf
                                                    @method('DELETE')
                                                    <button type="submit" class="drip-btn drip-btn--danger drip-btn--sm"><x-icon name="trash" :size="15" /> Remove</button>
                                                </form>
                                            </div>
                                        @endif
                                    </td>
                                </tr>
                            @empty
                                <tr><td colspan="5" class="drip-empty"><strong>Not sent to anyone yet</strong>Choose a reviewer above to send this proposal for scoring.</td></tr>
                            @endforelse
                        </tbody>
                    </table>
                </div>
            </div>
        </div>
    </div>

    <script>
        function copyLink(inputId, button) {
            var input = document.getElementById(inputId);
            input.select();
            input.setSelectionRange(0, 99999);
            var done = function () {
                var original = button.innerHTML;
                button.textContent = 'Copied!';
                setTimeout(function () { button.innerHTML = original; }, 1500);
            };
            if (navigator.clipboard && window.isSecureContext) {
                navigator.clipboard.writeText(input.value).then(done);
            } else {
                document.execCommand('copy');
                done();
            }
        }
    </script>
</x-admin-layout>
