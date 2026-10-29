<x-staff-layout>
    @php
        $call = $call_for_proposal;
        $call_status = $call->computed_status;
        $pill = match ($call_status) { 'Open' => 'drip-pill--success', 'Upcoming' => 'drip-pill--warning', default => 'drip-pill--muted' };
        $pill_icon = match ($call_status) { 'Open' => 'circle-check', 'Upcoming' => 'clock', default => 'minus' };
        $days_left = $call_status === 'Open' ? (int) now()->startOfDay()->diffInDays($call->close_date) : null;
        $accept = '.doc,.docx,.pdf,.odt';
    @endphp

    <div class="drip-page">
        <div class="drip-container">

            <nav class="drip-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('staff.call_for_proposals.index') }}">Call for Proposals</a>
                <x-icon name="chevron-right" :size="14" />
                <span aria-current="page">{{ \Illuminate\Support\Str::limit($call->title, 48) }}</span>
            </nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1" style="font-size:1.875rem;">{{ $call->title }}</h1>
                    <div style="margin-top:14px;"><span class="drip-pill {{ $pill }}"><x-icon :name="$pill_icon" :size="14" />{{ $call_status }}</span></div>
                </div>
                <div class="drip-actions">
                    <a href="{{ route('staff.call_for_proposals.index') }}" class="drip-btn drip-btn--outline"><x-icon name="arrow-left" :size="18" /> All calls</a>
                </div>
            </div>

            <x-flash />

            <div class="drip-card" style="margin-top:28px;"><div class="drip-card__body">
                <h2 class="drip-form-section__title">About this call</h2>
                @if ($call->description)
                    <p style="margin:12px 0 18px; line-height:1.6; white-space:pre-line;">{{ $call->description }}</p>
                @endif
                <dl class="drip-dl" style="margin-top:12px;">
                    <dt>Opens</dt><dd>{{ $call->open_date->format('l, F j, Y') }}</dd>
                    <dt>Deadline</dt>
                    <dd>{{ $call->close_date->format('l, F j, Y') }}@if (! is_null($days_left)) <span class="drip-app-meta" style="display:inline;">&middot; {{ $days_left === 0 ? 'closes today' : $days_left.' '.\Illuminate\Support\Str::plural('day', $days_left).' left' }}</span>@endif</dd>
                    @if ($call->advert)
                        <dt>Advert</dt><dd><a href="{{ asset('storage/'.$call->advert) }}" target="_blank" class="drip-clear">View the advert</a></dd>
                    @endif
                </dl>
            </div></div>

            @if ($has_submitted_application)
                {{-- already applied: show the application --}}
                <div class="drip-card" style="margin-top:24px;"><div class="drip-card__body">
                    <h2 class="drip-form-section__title">Your application</h2>
                    <p class="drip-form-section__sub">Thank you. Your application has been received. We will email you when there is an update.</p>

                    <dl class="drip-dl">
                        <dt>Status</dt><dd><x-status-badge :status="$my_application->status" /></dd>
                        <dt>Proposal</dt><dd>{{ $my_application->proposal_title }}</dd>
                        <dt>Principal investigator</dt><dd>{{ $my_application->principal_investigator }}</dd>
                        <dt>Submitted</dt><dd>{{ $my_application->created_at->format('l, F jS Y') }}</dd>
                        <dt>Your documents</dt>
                        <dd>
                            <div class="drip-doc-links" style="margin-top:0;">
                                <a class="drip-doc-link" target="_blank" href="{{ route('documents.application', ['uuid' => $my_application->uuid, 'type' => 'title']) }}"><x-icon name="file-text" :size="14" /> Title page</a>
                                <a class="drip-doc-link" target="_blank" href="{{ route('documents.application', ['uuid' => $my_application->uuid, 'type' => 'proposal']) }}"><x-icon name="file-text" :size="14" /> Proposal</a>
                                <a class="drip-doc-link" target="_blank" href="{{ route('documents.application', ['uuid' => $my_application->uuid, 'type' => 'college_review']) }}"><x-icon name="file" :size="14" /> College review</a>
                            </div>
                        </dd>
                    </dl>
                    <p class="drip-help" style="margin-top:16px;">A submitted application cannot be edited here. Contact DRIP if something needs to change.</p>
                </div></div>

            @elseif (! $call->isOpen())
                <div class="drip-alert drip-alert--error" style="margin-top:24px; background:var(--muted); color:var(--foreground); border-color:var(--border);">
                    <x-icon name="info" :size="18" />
                    <span>{{ $call_status === 'Upcoming' ? 'This call opens on '.$call->open_date->format('F j, Y').'. You can apply from then.' : 'This call is closed, so applications can no longer be submitted.' }}</span>
                </div>

            @else
                {{-- the application form --}}
                <form method="POST" action="{{ route('staff.call_for_proposals.application.store', ['uuid' => $call->uuid]) }}" enctype="multipart/form-data" style="margin-top:24px;"
                      onsubmit="return confirm('Submit your application? You will not be able to change it afterwards.');">
                    @csrf

                    <div class="drip-card">
                        <div class="drip-card__body">

                            @if ($errors->any())
                                <div class="drip-alert drip-alert--error" style="margin: 0 0 24px;">
                                    <x-icon name="circle-x" :size="18" />
                                    <span>Your application was not submitted. Please correct the highlighted fields. You will need to choose your files again.</span>
                                </div>
                            @endif

                            <div class="drip-form-section">
                                <h2 class="drip-form-section__title">Apply for this call</h2>
                                <p class="drip-form-section__sub">Fill in the details and upload your documents. Each file can be up to 20 MB (Word, PDF or OpenDocument).</p>

                                <div class="drip-form-grid">
                                    <x-form-field name="principal_investigator" label="Principal investigator" :required="true" :full="true" placeholder="Full name of the principal investigator" />
                                    <x-form-field name="proposal_title" label="Title of the proposal" :required="true" :full="true" />
                                    <x-form-field name="proposal_description" label="Description" type="textarea" :rows="5" :required="true" :full="true" help="A short description of the proposal (up to 5,000 characters)." />
                                </div>
                            </div>

                            <div class="drip-form-section">
                                <h2 class="drip-form-section__title">Documents</h2>
                                <p class="drip-form-section__sub">Reviewers see only the proposal file, without your names, so keep the names on the title page only.</p>

                                <div class="drip-form-grid">
                                    <x-form-field name="proposal_title_file" label="Title page of the proposal" type="file" :required="true" :full="true" :accept="$accept"
                                                  help="The page containing the names of the principal investigator and team members." />
                                    <x-form-field name="proposal_file" label="Proposal file" type="file" :required="true" :full="true" :accept="$accept"
                                                  help="The proposal excluding the title page, without the names of the principal investigator and team members." />
                                    <x-form-field name="college_review" label="Copy of the college review" type="file" :required="true" :full="true" :accept="$accept" />
                                </div>
                            </div>
                        </div>

                        <div class="drip-form-actions">
                            <a href="{{ route('staff.call_for_proposals.index') }}" class="drip-btn drip-btn--outline">Cancel</a>
                            <button type="submit" class="drip-btn drip-btn--primary"><x-icon name="send" :size="18" /> Submit application</button>
                        </div>
                    </div>
                </form>
            @endif

        </div>
    </div>
</x-staff-layout>
