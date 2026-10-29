<x-staff-layout>
    @php
        $dash = fn ($value) => ($value === null || $value === '') ? '-' : $value;
        $date = fn ($value, $format = 'F j, Y') => $value ? $value->format($format) : '-';
        $money = fn ($value) => is_null($value) ? '-' : $application->cost_currency.' '.number_format((float) $value, 2);
        $labels = ['teaching' => 'Teaching Staff', 'non_teaching' => 'Non-Teaching Staff'];
        $milestones = ['created' => 'Draft started', 'submitted' => 'Submitted', 'approved' => 'Approved', 'not_approved' => 'Not approved'];
        $documents = config('conference.document_types');
    @endphp

    <div class="drip-page">
        <div class="drip-container">

            <nav class="drip-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('staff.conference.calls.index') }}">Conference Grants</a>
                <x-icon name="chevron-right" :size="14" />
                <a href="{{ route('staff.conference.calls.show', ['call' => $call->uuid]) }}">{{ \Illuminate\Support\Str::limit($call->title, 40) }}</a>
                <x-icon name="chevron-right" :size="14" />
                <span aria-current="page">Your application</span>
            </nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1">Your application <x-status-badge :status="$application->status" /></h1>
                    <div class="drip-subtitle"><x-icon name="graduation-cap" :size="20" /> <span>{{ $call->title }}</span></div>
                </div>
                <div class="drip-actions">
                    <a href="{{ route('conference.files.form', ['application' => $application->uuid]) }}" target="_blank" class="drip-btn drip-btn--outline"><x-icon name="download" :size="18" /> Nomination form (PDF)</a>
                    <a href="{{ route('staff.conference.calls.index') }}" class="drip-btn drip-btn--outline"><x-icon name="arrow-left" :size="18" /> Conference Grants</a>
                </div>
            </div>

            @if (session('success'))
                <div class="drip-alert drip-alert--success"><x-icon name="circle-check" :size="18" /> <span>{{ session('success') }}</span></div>
            @endif
            @if (session('error'))
                <div class="drip-alert drip-alert--error"><x-icon name="circle-x" :size="18" /> <span>{{ session('error') }}</span></div>
            @endif

            @if ($application->status === 'submitted')
                <div class="drip-alert drip-alert--success"><x-icon name="clock" :size="18" /> <span>Submitted on {{ $date($application->submitted_at, 'F j, Y') }}. The committee will review your application and you will be told the outcome.</span></div>
            @elseif ($application->status === 'approved')
                <div class="drip-alert drip-alert--success" style="display:block;">
                    <strong>Your application has been approved.</strong>
                    @if ($call->approval_instructions)
                        <div style="margin-top:8px; white-space:pre-line;">{{ $call->approval_instructions }}</div>
                    @endif
                    @if ($application->decision_remark_shared && $application->decision_remark)
                        <div style="margin-top:12px; white-space:pre-line;"><strong>Message from DRIP:</strong> {{ $application->decision_remark }}</div>
                    @endif
                </div>
            @elseif ($application->status === 'not_approved')
                <div class="drip-alert drip-alert--error" style="display:block;">
                    <strong>Your application was not approved on this occasion.</strong>
                    @if ($application->decision_remark_shared && $application->decision_remark)
                        <div style="margin-top:8px; white-space:pre-line;"><strong>Message from DRIP:</strong> {{ $application->decision_remark }}</div>
                    @endif
                </div>
            @endif

            <div class="drip-two-col">
                <div class="drip-stack">
                    <div class="drip-card"><div class="drip-card__body">
                        <h2 class="drip-form-section__title">Candidate</h2>
                        <dl class="drip-dl" style="margin-top:16px;">
                            <dt>Institution</dt><dd>{{ $dash($application->institution) }}</dd>
                            <dt>Name</dt><dd>{{ $dash($application->candidate_name) }}</dd>
                            <dt>Gender</dt><dd>{{ $dash(ucfirst((string) $application->gender)) }}</dd>
                            <dt>Date of birth</dt><dd>{{ $date($application->date_of_birth) }}</dd>
                            <dt>Staff category</dt><dd>{{ $labels[$application->staff_category] ?? '-' }}</dd>
                            <dt>Present position</dt><dd>{{ $dash($application->present_position) }}</dd>
                            <dt>Phone</dt><dd>{{ $dash($application->phone) }}</dd>
                            <dt>FUNAAB email</dt><dd>{{ $dash($application->funaab_email) }}</dd>
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
                        <h2 class="drip-form-section__title">Salary account</h2>
                        <dl class="drip-dl" style="margin-top:16px;">
                            <dt>Bank</dt><dd>{{ $dash($application->bank_name) }}, {{ $dash($application->bank_branch) }}</dd>
                            <dt>Account name</dt><dd>{{ $dash($application->account_name) }}</dd>
                            <dt>Account number</dt><dd>{{ $application->masked_account_number ?: '-' }}</dd>
                        </dl>
                    </div></div>
                </div>

                <div class="drip-stack">
                    <div class="drip-card"><div class="drip-card__body">
                        <h2 class="drip-form-section__title">{{ $application->event_noun }} to be attended</h2>
                        <dl class="drip-dl" style="margin-top:16px;">
                            <dt>Title</dt><dd>{{ $dash($application->conference_title) }}</dd>
                            <dt>Organiser(s)</dt><dd>{{ $dash($application->conference_organiser) }}</dd>
                            <dt>Venue</dt><dd>{{ $dash($application->conference_venue) }}</dd>
                            <dt>Type</dt><dd>{{ ucfirst((string) $application->conference_scope) }}, {{ str_replace('_', ' ', (string) $application->conference_mode) }}</dd>
                            <dt>Dates</dt><dd>{{ $date($application->conference_start_date) }} to {{ $date($application->conference_end_date) }} ({{ $dash($application->duration_days) }} days)</dd>
                            <dt>Registration fee</dt><dd>{{ $money($application->registration_fee) }}</dd>
                            <dt>Flight</dt><dd>{{ $money($application->flight_cost) }}</dd>
                            <dt>Accommodation</dt><dd>{{ $money($application->accommodation_cost) }}</dd>
                            <dt>Feeding</dt><dd>{{ $money($application->feeding_cost) }}</dd>
                            <dt>Excursion</dt><dd>{{ $money($application->excursion_cost) }}</dd>
                            <dt><strong>Total</strong></dt><dd><strong>{{ $application->cost_currency }} {{ number_format($application->total_cost, 2) }}</strong></dd>
                        </dl>
                    </div></div>

                    @if ($application->previously_sponsored)
                        <div class="drip-card"><div class="drip-card__body">
                            <h2 class="drip-form-section__title">Previous TETFund sponsorship</h2>
                            <dl class="drip-dl" style="margin-top:16px;">
                                <dt>Last conference</dt><dd>{{ $dash($application->last_conference_title) }}</dd>
                                <dt>Date and venue</dt><dd>{{ $date($application->last_conference_date) }}, {{ $dash($application->last_conference_venue) }}</dd>
                                <dt>Publication</dt><dd style="white-space:pre-line;">{{ $dash($application->last_conference_publication) }}</dd>
                                <dt>Evidence offered</dt><dd style="white-space:pre-line;">{{ $dash($application->publication_evidence_note) }}</dd>
                            </dl>
                        </div></div>
                    @endif

                    <div class="drip-card"><div class="drip-card__body">
                        <h2 class="drip-form-section__title">Documents</h2>
                        <ul class="drip-checklist" style="margin-top:16px;">
                            @foreach ($application->documents as $document)
                                <li class="is-pass">
                                    <x-icon name="file-text" :size="18" />
                                    <span>{{ $document->label }}<small><a href="{{ route('conference.files.document', ['application' => $application->uuid, 'type' => $document->type]) }}" target="_blank" class="drip-clear">{{ $document->original_name }}</a></small></span>
                                </li>
                            @endforeach
                        </ul>
                    </div></div>

                    <div class="drip-card"><div class="drip-card__body">
                        <h2 class="drip-form-section__title">Progress</h2>
                        <ul class="drip-checklist" style="margin-top:16px;">
                            @foreach ($application->events->filter(fn ($event) => isset($milestones[$event->event])) as $event)
                                <li class="is-pass"><x-icon name="circle-check" :size="18" /> <span>{{ $milestones[$event->event] }}<small>{{ $event->created_at->format('F j, Y, g:i A') }}</small></span></li>
                            @endforeach
                        </ul>
                    </div></div>
                </div>
            </div>

        </div>
    </div>
</x-staff-layout>
