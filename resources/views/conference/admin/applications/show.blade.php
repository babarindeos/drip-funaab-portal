<x-admin-layout>
    @php
        $dash = fn ($value) => ($value === null || $value === '') ? '-' : $value;
        $date = fn ($value, $format = 'F j, Y') => $value ? $value->format($format) : '-';
        $money = fn ($value) => is_null($value) ? '-' : $application->cost_currency.' '.number_format((float) $value, 2);
        $categories = config('conference.staff_categories');
        $event_labels = [
            'created' => 'Draft started', 'document_uploaded' => 'Document uploaded', 'document_replaced' => 'Document replaced',
            'document_removed' => 'Document removed', 'submitted' => 'Submitted', 'approved' => 'Approved',
            'not_approved' => 'Not approved', 'reopened' => 'Reopened for a new decision',
            'applicant_notified' => 'Applicant emailed', 'notification_failed' => 'Email to the applicant failed',
        ];
        $person = fn ($user) => $user ? (trim($user->firstname.' '.$user->surname) ?: $user->email) : 'System';
        $back = route('admin.conference.applications.index', ['call' => $call->id]);
    @endphp

    <div class="drip-page">
        <div class="drip-container">

            <nav class="drip-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('admin.conference.calls.index') }}">Conference Grants</a>
                <x-icon name="chevron-right" :size="14" />
                <a href="{{ $back }}">{{ \Illuminate\Support\Str::limit($call->title, 36) }}</a>
                <x-icon name="chevron-right" :size="14" />
                <span aria-current="page">{{ \Illuminate\Support\Str::limit($application->candidate_name ?: 'Applicant', 32) }}</span>
            </nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1" style="font-size:2rem;">{{ $application->candidate_name ?: 'Unnamed applicant' }} <x-status-badge :status="$application->status" /></h1>
                    <div class="drip-subtitle"><x-icon name="graduation-cap" :size="20" /> <span>{{ $call->title }}</span></div>
                </div>
                <div class="drip-actions">
                    <a href="{{ route('conference.files.form', ['application' => $application->uuid]) }}" target="_blank" class="drip-btn drip-btn--outline"><x-icon name="download" :size="18" /> Nomination form (PDF)</a>
                    <a href="{{ $back }}" class="drip-btn drip-btn--outline"><x-icon name="arrow-left" :size="18" /> Applications</a>
                </div>
            </div>

            @if (session('success'))
                <div class="drip-alert drip-alert--success"><x-icon name="circle-check" :size="18" /> <span>{{ session('success') }}</span></div>
            @endif
            @if (session('error'))
                <div class="drip-alert drip-alert--error"><x-icon name="circle-x" :size="18" /> <span>{{ session('error') }}</span></div>
            @endif

            <div class="drip-two-col">

                {{-- ============ the application ============ --}}
                <div class="drip-stack">
                    <div class="drip-card"><div class="drip-card__body">
                        <h2 class="drip-form-section__title">Candidate</h2>
                        <dl class="drip-dl" style="margin-top:16px;">
                            <dt>Institution</dt><dd>{{ $dash($application->institution) }}</dd>
                            <dt>Name</dt><dd>{{ $dash($application->candidate_name) }}</dd>
                            <dt>Gender</dt><dd>{{ $dash(ucfirst((string) $application->gender)) }}</dd>
                            <dt>Date of birth</dt><dd>{{ $date($application->date_of_birth) }}</dd>
                            <dt>Staff category</dt><dd>{{ $categories[$application->staff_category] ?? '-' }}</dd>
                            <dt>Present position</dt><dd>{{ $dash($application->present_position) }}</dd>
                            <dt>Phone</dt><dd>{{ $dash($application->phone) }}</dd>
                            <dt>FUNAAB email</dt><dd>{{ $dash($application->funaab_email) }}</dd>
                            <dt>Account email</dt><dd>{{ $dash($application->account_email) }}</dd>
                        </dl>
                    </div></div>

                    <div class="drip-card"><div class="drip-card__body">
                        <h2 class="drip-form-section__title">Institutional record</h2>
                        <dl class="drip-dl" style="margin-top:16px;">
                            <dt>Department</dt><dd>{{ $dash($application->department) }}</dd>
                            <dt>Highest qualification</dt><dd>{{ $dash($application->highest_qualification) }}</dd>
                            <dt>Area of specialisation</dt><dd>{{ $dash($application->area_of_specialisation) }}</dd>
                            <dt>Rank</dt><dd>{{ $dash($application->staff_rank) }}</dd>
                            <dt>Grade level</dt><dd>{{ $dash($application->grade_level) }}</dd>
                            <dt>First appointment</dt><dd>{{ $date($application->first_appointment_date) }}</dd>
                            <dt>Work experience</dt><dd>{{ $dash($application->work_experience_years) }} years</dd>
                            <dt>Years in the institution</dt><dd>{{ $dash($application->years_in_institution) }}</dd>
                            <dt>Confirmed staff</dt><dd>{{ is_null($application->is_confirmed_staff) ? '-' : ($application->is_confirmed_staff ? 'Yes' : 'No') }}</dd>
                            <dt>Date of confirmation</dt><dd>{{ $date($application->confirmation_date) }}</dd>
                        </dl>
                    </div></div>

                    <div class="drip-card"><div class="drip-card__body">
                        <h2 class="drip-form-section__title">{{ $application->event_noun }} to be attended</h2>
                        <dl class="drip-dl" style="margin-top:16px;">
                            <dt>Title</dt><dd>{{ $dash($application->conference_title) }}</dd>
                            <dt>Organiser(s)</dt><dd>{{ $dash($application->conference_organiser) }}</dd>
                            <dt>Venue</dt><dd>{{ $dash($application->conference_venue) }}</dd>
                            <dt>Type</dt><dd>{{ ucfirst((string) $application->conference_scope) }}, {{ str_replace('_', ' ', (string) $application->conference_mode) }}@if ($application->staff_category === 'teaching'), {{ $application->presentation_type }} presentation @endif</dd>
                            <dt>Website</dt><dd>{{ $dash($application->conference_website) }}</dd>
                            <dt>Organiser's email</dt><dd>{{ $dash($application->conference_email) }}</dd>
                            <dt>Dates</dt><dd>{{ $date($application->conference_start_date) }} to {{ $date($application->conference_end_date) }} ({{ $dash($application->duration_days) }} days)</dd>
                            <dt>Registration fee</dt><dd>{{ $money($application->registration_fee) }}</dd>
                            <dt>Flight</dt><dd>{{ $money($application->flight_cost) }}</dd>
                            <dt>Accommodation</dt><dd>{{ $money($application->accommodation_cost) }}</dd>
                            <dt>Feeding</dt><dd>{{ $money($application->feeding_cost) }}</dd>
                            <dt>Excursion</dt><dd>{{ $money($application->excursion_cost) }}</dd>
                            <dt><strong>Total</strong></dt><dd><strong>{{ $application->cost_currency }} {{ number_format($application->total_cost, 2) }}</strong></dd>
                        </dl>
                    </div></div>

                    <div class="drip-card"><div class="drip-card__body">
                        <h2 class="drip-form-section__title">Attendance history</h2>
                        @if ($application->previously_sponsored)
                            <dl class="drip-dl" style="margin-top:16px;">
                                <dt>Previous TETFund beneficiary</dt><dd>Yes</dd>
                                <dt>Last conference</dt><dd>{{ $dash($application->last_conference_title) }}</dd>
                                <dt>Date and venue</dt><dd>{{ $date($application->last_conference_date) }}, {{ $dash($application->last_conference_venue) }}</dd>
                                <dt>Publication</dt><dd style="white-space:pre-line;">{{ $dash($application->last_conference_publication) }}</dd>
                                <dt>Evidence offered</dt><dd style="white-space:pre-line;">{{ $dash($application->publication_evidence_note) }}</dd>
                            </dl>
                        @else
                            <p style="margin:14px 0 0;">First-time beneficiary. Has not been sponsored by TETFund for an international conference before.</p>
                        @endif
                    </div></div>

                    <div class="drip-card"><div class="drip-card__body">
                        <h2 class="drip-form-section__title">Salary account</h2>
                        <div class="drip-callout" style="margin:14px 0; display:flex; gap:10px; align-items:flex-start;"><x-icon name="lock" :size="18" /> <span>Stored encrypted. Shown here to authorised staff only.</span></div>
                        <dl class="drip-dl">
                            <dt>Bank and branch</dt><dd>{{ $dash($application->bank_name) }}, {{ $dash($application->bank_branch) }}</dd>
                            <dt>Account name</dt><dd>{{ $dash($application->account_name) }}</dd>
                            <dt>Account number</dt><dd>{{ $dash($application->secret('account_number')) }}</dd>
                            <dt>Sort code</dt><dd>{{ $dash($application->secret('sort_code')) }}</dd>
                        </dl>
                    </div></div>
                </div>

                {{-- ============ decision, checks, documents, trail ============ --}}
                <div class="drip-stack">

                    <div class="drip-card"><div class="drip-card__body">
                        <h2 class="drip-form-section__title">Committee decision</h2>

                        @if ($application->status === 'submitted')
                            <p class="drip-form-section__sub">Submitted {{ $date($application->submitted_at) }}. Record the decision of the University Staff Development Committee.</p>

                            <form method="POST" action="{{ route('admin.conference.applications.decide', ['application' => $application->uuid]) }}">
                                @csrf
                                <label class="drip-label" for="remark">Remark</label>
                                <textarea id="remark" name="remark" rows="4" class="drip-control @error('remark') is-invalid @enderror" placeholder="Optional note kept with the decision">{{ old('remark') }}</textarea>
                                @error('remark')<div class="drip-error">{{ $message }}</div>@enderror
                                @error('decision')<div class="drip-error">{{ $message }}</div>@enderror

                                <label class="drip-check" style="margin-top:12px;">
                                    <input type="checkbox" name="share_remark" value="1" @checked(old('share_remark'))>
                                    <span>Show this remark to the applicant</span>
                                </label>
                                <div class="drip-help">Leave this unticked to keep the remark internal.</div>

                                <div style="display:flex; flex-wrap:wrap; gap:10px; margin-top:18px;">
                                    <button type="submit" name="decision" value="approved" class="drip-btn drip-btn--primary" onclick="return confirm('Approve this application?');"><x-icon name="check" :size="18" /> Approve</button>
                                    <button type="submit" name="decision" value="not_approved" class="drip-btn drip-btn--danger" onclick="return confirm('Mark this application as NOT approved?');"><x-icon name="circle-x" :size="18" /> Not approve</button>
                                </div>
                            </form>
                        @elseif (in_array($application->status, ['approved', 'not_approved'], true))
                            <div class="drip-alert {{ $application->status === 'approved' ? 'drip-alert--success' : 'drip-alert--error' }}" style="display:block; margin-top:14px;">
                                <strong>{{ $application->status_label }}</strong> on {{ $date($application->decided_at, 'F j, Y') }} by {{ $person($decided_by) }}.
                            </div>

                            @if ($application->decision_remark)
                                <p style="margin:16px 0 4px; white-space:pre-line;">{{ $application->decision_remark }}</p>
                                <div class="drip-help">{{ $application->decision_remark_shared ? 'This remark is shown to the applicant.' : 'Internal. The applicant cannot see this remark.' }}</div>
                            @endif

                            <form method="POST" action="{{ route('admin.conference.applications.reopen', ['application' => $application->uuid]) }}" style="margin-top:22px; padding-top:18px; border-top:1px solid var(--border);" onsubmit="return confirm('Reopen this decision?');">
                                @csrf
                                <label class="drip-label" for="reason">Made a mistake? Reopen it</label>
                                <input id="reason" name="reason" type="text" class="drip-control @error('reason') is-invalid @enderror" placeholder="Reason for reopening (kept in the audit trail)" value="{{ old('reason') }}">
                                @error('reason')<div class="drip-error">{{ $message }}</div>@enderror
                                <button type="submit" class="drip-btn drip-btn--outline drip-btn--sm" style="margin-top:12px;">Reopen for a new decision</button>
                            </form>
                        @else
                            <p class="drip-form-section__sub" style="margin-top:8px;">This application is still a draft. The applicant has not submitted it, so there is nothing to decide yet.</p>
                        @endif
                    </div></div>

                    <div class="drip-card"><div class="drip-card__body">
                        <h2 class="drip-form-section__title">Eligibility check</h2>
                        <p class="drip-form-section__sub">The call's rules, applied to this application.</p>
                        <ul class="drip-checklist">
                            @foreach ($checks as $check)
                                <li class="is-{{ $check['status'] }}">
                                    <x-icon :name="$check['status'] === 'pass' ? 'circle-check' : ($check['status'] === 'fail' ? 'circle-x' : 'clock')" :size="18" />
                                    <span>{{ $check['label'] }}@if (! empty($check['message']))<small>{{ $check['message'] }}</small>@endif</span>
                                </li>
                            @endforeach
                        </ul>
                    </div></div>

                    <div class="drip-card"><div class="drip-card__body">
                        <h2 class="drip-form-section__title">Documents</h2>
                        <ul class="drip-checklist" style="margin-top:16px;">
                            @foreach ($application->requiredDocumentTypes() as $type)
                                @php $document = $application->document($type); @endphp
                                <li class="{{ $document ? 'is-pass' : 'is-fail' }}">
                                    <x-icon :name="$document ? 'file-text' : 'circle-x'" :size="18" />
                                    <span>{{ config('conference.document_types.'.$type.'.label') }}
                                        @if ($document)
                                            <small><a href="{{ route('conference.files.document', ['application' => $application->uuid, 'type' => $type]) }}" target="_blank" class="drip-clear">{{ $document->original_name }}</a></small>
                                        @else
                                            <small>Not uploaded</small>
                                        @endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div></div>

                    <div class="drip-card"><div class="drip-card__body">
                        <h2 class="drip-form-section__title">Audit trail</h2>
                        <ul class="drip-checklist" style="margin-top:16px;">
                            @foreach ($application->events as $event)
                                <li class="is-pending">
                                    <x-icon name="clock" :size="18" />
                                    <span>{{ $event_labels[$event->event] ?? ucfirst(str_replace('_', ' ', $event->event)) }}
                                        <small>{{ $person($event->actor) }} &middot; {{ $event->created_at->format('M j, Y, g:i A') }}</small>
                                        @if ($event->note)<small style="white-space:pre-line;">{{ $event->note }}</small>@endif
                                    </span>
                                </li>
                            @endforeach
                        </ul>
                    </div></div>

                </div>
            </div>
        </div>
    </div>
</x-admin-layout>
