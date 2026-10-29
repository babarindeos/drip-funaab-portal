<x-admin-layout>
    @php
        $applicant = $proposal_application->owner ? trim($proposal_application->owner->surname.' '.$proposal_application->owner->firstname) : 'Unknown applicant';
        $assigned = $proposal_application->reviewers_assigned_count;
        $completed = $proposal_application->reviewers_completed_count;
        $average = $proposal_application->average_score;
        $statuses = ['pending' => 'Pending', 'acknowledged' => 'Acknowledged', 'accepted' => 'Accepted', 'rejected' => 'Rejected'];
        $current_status = old('status', $proposal_application->status ?: 'pending');
    @endphp

    <div class="drip-page">
        <div class="drip-container">

            <nav class="drip-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('admin.call_for_proposals.index') }}">Call for Proposals</a>
                <x-icon name="chevron-right" :size="14" />
                <a href="{{ route('admin.call_for_proposals.submissions', ['call_for_proposal' => $call_for_proposal->id]) }}">{{ \Illuminate\Support\Str::limit($call_for_proposal->title, 34) }}</a>
                <x-icon name="chevron-right" :size="14" />
                <span aria-current="page">{{ \Illuminate\Support\Str::limit($proposal_application->proposal_title, 34) }}</span>
            </nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1" style="font-size:1.875rem;">{{ $proposal_application->proposal_title }} <x-status-badge :status="$proposal_application->status" /></h1>
                    <div class="drip-subtitle"><x-icon name="book-open" :size="20" /> <span>{{ $call_for_proposal->title }}</span></div>
                </div>
                <div class="drip-actions">
                    <a href="{{ route('admin.call_for_proposals.submissions', ['call_for_proposal' => $call_for_proposal->id]) }}" class="drip-btn drip-btn--outline"><x-icon name="arrow-left" :size="18" /> Applications</a>
                    <a href="{{ route('admin.call_for_proposals.proposal_application.send_to_reviewer', ['proposal_application' => $proposal_application->id]) }}" class="drip-btn drip-btn--primary"><x-icon name="send" :size="18" /> Send to reviewer</a>
                </div>
            </div>

            <x-flash />

            <div class="drip-two-col">

                <div class="drip-stack">
                    <div class="drip-card"><div class="drip-card__body">
                        <h2 class="drip-form-section__title">Proposal</h2>
                        <dl class="drip-dl" style="margin-top:16px;">
                            <dt>Title</dt><dd>{{ $proposal_application->proposal_title }}</dd>
                            <dt>Principal investigator</dt><dd>{{ $proposal_application->principal_investigator }}</dd>
                            <dt>Applicant</dt><dd>{{ $applicant }}@if ($proposal_application->owner)<div class="drip-app-meta">{{ $proposal_application->owner->email }}</div>@endif</dd>
                            <dt>Submitted</dt><dd>{{ optional($proposal_application->created_at)->format('l, F jS Y') }}</dd>
                            <dt>Description</dt><dd style="white-space:pre-line;">{{ $proposal_application->proposal_description ?: '-' }}</dd>
                        </dl>
                    </div></div>

                    <div class="drip-card"><div class="drip-card__body">
                        <h2 class="drip-form-section__title">Documents</h2>
                        <ul class="drip-checklist" style="margin-top:16px;">
                            @foreach (['title' => ['Title page', 'proposal_title_file'], 'proposal' => ['Proposal', 'proposal_file'], 'college_review' => ['College review', 'college_review']] as $type => [$label, $column])
                                <li class="{{ $proposal_application->{$column} ? 'is-pass' : 'is-fail' }}">
                                    <x-icon :name="$proposal_application->{$column} ? 'file-text' : 'circle-x'" :size="18" />
                                    <span>{{ $label }}
                                        @if ($proposal_application->{$column})
                                            <small><a class="drip-clear" target="_blank" href="{{ route('documents.application', ['uuid' => $proposal_application->uuid, 'type' => $type]) }}">Open document ({{ strtoupper(pathinfo($proposal_application->{$column}, PATHINFO_EXTENSION)) }})</a></small>
                                        @else
                                            <small>Not uploaded</small>
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div></div>
                </div>

                <div class="drip-stack">
                    <div class="drip-card"><div class="drip-card__body">
                        <h2 class="drip-form-section__title">Decision</h2>
                        <p class="drip-form-section__sub">For office use only. The applicant is emailed when the status changes.</p>

                        <form method="POST" action="{{ route('admin.call_for_proposals.proposal_application.status_update', ['proposal_application' => $proposal_application->id]) }}">
                            @csrf

                            <label class="drip-label" for="status">Status <span class="req">*</span></label>
                            <select id="status" name="status" class="drip-control @error('status') is-invalid @enderror" required>
                                @foreach ($statuses as $value => $label)
                                    <option value="{{ $value }}" @selected($current_status === $value)>{{ $label }}</option>
                                @endforeach
                            </select>
                            @error('status')<div class="drip-error">{{ $message }}</div>@enderror

                            <label class="drip-label" for="remark" style="margin-top:18px;">Remark</label>
                            <textarea id="remark" name="remark" rows="4" class="drip-control @error('remark') is-invalid @enderror" placeholder="Optional note kept with the decision">{{ old('remark', $proposal_application->remark) }}</textarea>
                            @error('remark')<div class="drip-error">{{ $message }}</div>@enderror

                            <label class="drip-check" style="margin-top:14px;">
                                <input type="checkbox" name="share_remark" value="1" @checked(old('share_remark'))>
                                <span>Include this remark in the email to the applicant</span>
                            </label>
                            <div class="drip-help">Leave this unticked to keep the remark internal.</div>

                            <button type="submit" class="drip-btn drip-btn--primary" style="margin-top:20px;"><x-icon name="check" :size="18" /> Save decision</button>
                        </form>
                    </div></div>

                    <div class="drip-card"><div class="drip-card__body">
                        <h2 class="drip-form-section__title">Reviews</h2>
                        <dl class="drip-dl" style="margin-top:16px;">
                            <dt>Sent to</dt><dd>{{ $assigned }} {{ \Illuminate\Support\Str::plural('reviewer', $assigned) }}</dd>
                            <dt>Completed</dt><dd>{{ $completed }} of {{ $assigned }}</dd>
                            <dt>Average score</dt><dd>@if (is_null($average))<span class="drip-app-meta">Not reviewed yet</span>@else<strong>{{ $average }}</strong>@endif</dd>
                        </dl>
                        <div style="margin-top:18px; display:flex; gap:10px; flex-wrap:wrap;">
                            <a href="{{ route('admin.call_for_proposals.proposal_application.send_to_reviewer', ['proposal_application' => $proposal_application->id]) }}" class="drip-btn drip-btn--outline drip-btn--sm">Manage reviewers</a>
                            <a href="{{ route('admin.call_for_proposals.results', ['call_for_proposal' => $call_for_proposal->id]) }}" class="drip-btn drip-btn--outline drip-btn--sm"><x-icon name="bar-chart" :size="15" /> Results</a>
                        </div>
                    </div></div>
                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
