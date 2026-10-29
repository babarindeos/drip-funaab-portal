<x-staff-layout>
    @php
        $has_errors = $errors->any();
        // the button below only appears once every required field is filled in and saved
        $form_complete = count($progress['missing']) === 0 && $entry['passed'];
        $category = old('staff_category', $application->staff_category);
        $is_teaching = $category === 'teaching';
        // unticked checkboxes are missing from the request, so only trust old() after a failed save
        $previous = $has_errors ? (bool) old('previously_sponsored') : (bool) $application->previously_sponsored;
        $declared = $has_errors ? (bool) old('declaration_accepted') : (bool) $application->declaration_accepted;
        $category_options = array_filter([
            'teaching' => $call->allow_teaching ? 'Teaching Staff' : null,
            'non_teaching' => $call->allow_non_teaching ? 'Non-Teaching Staff' : null,
        ]);
        $date = fn ($value) => optional($value)->format('Y-m-d');
        $money = fn ($value) => is_null($value) ? null : $value;
    @endphp

    <div class="drip-page">
        <div class="drip-container">

            <nav class="drip-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('staff.conference.calls.index') }}">Conference Grants</a>
                <x-icon name="chevron-right" :size="14" />
                <a href="{{ route('staff.conference.calls.show', ['call' => $call->uuid]) }}">{{ \Illuminate\Support\Str::limit($call->title, 40) }}</a>
                <x-icon name="chevron-right" :size="14" />
                <span aria-current="page">Nomination form</span>
            </nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1">Nomination form <x-status-badge :status="$application->status" /></h1>
                    <div class="drip-subtitle">
                        <x-icon name="graduation-cap" :size="20" />
                        <span>{{ $call->title }}</span>
                    </div>
                </div>
                <div class="drip-actions">
                    <a href="{{ route('staff.conference.calls.index') }}" class="drip-btn drip-btn--outline"><x-icon name="arrow-left" :size="18" /> Conference Grants</a>
                    @if ($form_complete)
                        <a href="{{ route('staff.conference.applications.documents', ['application' => $application->uuid]) }}" class="drip-btn drip-btn--primary">Documents and submission <x-icon name="chevron-right" :size="18" /></a>
                    @endif
                </div>
            </div>

            @if (session('success'))
                <div class="drip-alert drip-alert--success"><x-icon name="circle-check" :size="18" /> <span>{{ session('success') }}</span></div>
            @endif
            @if (session('error'))
                <div class="drip-alert drip-alert--error"><x-icon name="circle-x" :size="18" /> <span>{{ session('error') }}</span></div>
            @endif
            @if ($has_errors)
                <div class="drip-alert drip-alert--error">
                    <x-icon name="circle-x" :size="18" />
                    <span>Your draft was not saved. Please correct the highlighted fields below.</span>
                </div>
            @endif

            {{-- eligibility requirements: answered and met before the rest of the form opens --}}
            @php
                $bag = $errors->getBag('eligibility');
                $answer_date = old('first_appointment_date', $date($application->first_appointment_date));
                $answer_confirmed = old('is_confirmed_staff', is_null($application->is_confirmed_staff) ? '' : ($application->is_confirmed_staff ? 'yes' : 'no'));
                $answer_confirmed_on = old('confirmation_date', $date($application->confirmation_date));
                $service_years = \App\Services\Conference\ConferenceEligibility::serviceYears($application);
                $min_years = \App\Services\Conference\ConferenceEligibility::minYears();
            @endphp
            <div class="drip-card" style="margin-top:28px;">
                <div class="drip-card__body">
                    <h2 class="drip-form-section__title">Eligibility requirements</h2>
                    <p class="drip-form-section__sub">To apply you must be a confirmed member of staff with more than {{ $min_years - 1 }} {{ $min_years - 1 === 1 ? 'year' : 'years' }} of service at FUNAAB. Answer these first: the rest of the form opens when you qualify.</p>

                    <form method="POST" action="{{ route('staff.conference.applications.eligibility', ['application' => $application->uuid]) }}" novalidate>
                        @csrf
                        @method('PUT')

                        <div class="drip-form-grid">
                            <div>
                                <label class="drip-label" for="first_appointment_date">Date of FUNAAB appointment <span class="req">*</span></label>
                                <input id="first_appointment_date" name="first_appointment_date" type="date" value="{{ $answer_date }}" class="drip-control {{ $bag->has('first_appointment_date') ? 'is-invalid' : '' }}">
                                @if ($bag->has('first_appointment_date'))<div class="drip-error">{{ $bag->first('first_appointment_date') }}</div>@endif
                            </div>

                            <div>
                                <span class="drip-label">Number of years in FUNAAB</span>
                                <div class="drip-control" id="years-out" style="display:flex; align-items:center; background:var(--muted);">{{ is_null($service_years) ? '-' : $service_years }}</div>
                                <div class="drip-help">Worked out from your date of appointment (completed years).</div>
                            </div>

                            <div>
                                <span class="drip-label">Are you a confirmed member of staff? <span class="req">*</span></span>
                                <div style="display:flex; gap:28px; margin-top:6px;">
                                    <label class="drip-check"><input type="radio" name="is_confirmed_staff" value="yes" id="confirmed_yes" @checked($answer_confirmed === 'yes')> <span>Yes</span></label>
                                    <label class="drip-check"><input type="radio" name="is_confirmed_staff" value="no" id="confirmed_no" @checked($answer_confirmed === 'no')> <span>No</span></label>
                                </div>
                                @if ($bag->has('is_confirmed_staff'))<div class="drip-error">{{ $bag->first('is_confirmed_staff') }}</div>@endif
                            </div>

                            <div id="confirmation-wrap" style="{{ $answer_confirmed === 'yes' ? '' : 'display:none;' }}">
                                <label class="drip-label" for="confirmation_date">Date of confirmation <span class="req">*</span></label>
                                <input id="confirmation_date" name="confirmation_date" type="date" value="{{ $answer_confirmed_on }}" class="drip-control {{ $bag->has('confirmation_date') ? 'is-invalid' : '' }}">
                                <div class="drip-help">You will upload your letter of confirmation with your other documents.</div>
                                @if ($bag->has('confirmation_date'))<div class="drip-error">{{ $bag->first('confirmation_date') }}</div>@endif
                            </div>
                        </div>

                        @if ($entry['passed'])
                            <div class="drip-alert drip-alert--success" style="margin-top:20px;"><x-icon name="circle-check" :size="18" /> <span>You meet the eligibility requirements.</span></div>
                        @elseif (! empty($entry['reasons']))
                            <div class="drip-alert drip-alert--error" style="margin-top:20px; display:block;">
                                <strong>You do not meet the eligibility requirements, so you cannot apply for this call.</strong>
                                <ul style="margin:8px 0 0; padding-left:20px;">
                                    @foreach ($entry['reasons'] as $reason)<li>{{ $reason }}</li>@endforeach
                                </ul>
                            </div>
                        @endif

                        <div style="margin-top:20px;">
                            <button type="submit" class="drip-btn drip-btn--primary"><x-icon name="check" :size="18" /> Check my eligibility</button>
                        </div>
                    </form>
                </div>
            </div>

            @if ($entry['passed'])
            {{-- progress and eligibility --}}
            <div class="drip-two-col">
                <div class="drip-card">
                    <div class="drip-card__body">
                        <h2 class="drip-form-section__title">Form progress</h2>
                        <p class="drip-form-section__sub">Everything marked * must be filled in before you can submit.</p>

                        <div class="drip-progress-meta">
                            <span><strong>{{ $progress['filled'] }}</strong> of {{ $progress['total'] }} required fields</span>
                            <span><strong>{{ $progress['percent'] }}%</strong></span>
                        </div>
                        <div class="drip-progress"><span style="width: {{ $progress['percent'] }}%;"></span></div>

                        @if (count($progress['missing']) > 0)
                            <details class="drip-details-toggle">
                                <summary>What is still missing ({{ count($progress['missing']) }})</summary>
                                <ul>
                                    @foreach ($progress['missing'] as $missing_label)
                                        <li>{{ $missing_label }}</li>
                                    @endforeach
                                </ul>
                            </details>
                        @else
                            <p style="margin:14px 0 0; font-size:.9rem; color:var(--success); font-weight:550;">All required fields are filled in.</p>
                        @endif

                        <p class="drip-help" style="margin-top:14px;">@if ($form_complete)
                            Next: add your passport photograph and supporting documents on the Documents and submission page.
                        @else
                            When every required field is filled in and saved, the Documents and submission button appears here.
                        @endif</p>
                    </div>
                </div>

                <div class="drip-card">
                    <div class="drip-card__body">
                        <h2 class="drip-form-section__title">Eligibility check</h2>
                        <p class="drip-form-section__sub">Updated each time you save your draft.</p>

                        <ul class="drip-checklist">
                            @foreach ($checks as $check)
                                <li class="is-{{ $check['status'] }}">
                                    <x-icon :name="$check['status'] === 'pass' ? 'circle-check' : ($check['status'] === 'fail' ? 'circle-x' : 'clock')" :size="18" />
                                    <span>{{ $check['label'] }}@if (! empty($check['message']))<small>{{ $check['message'] }}</small>@endif</span>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            </div>

            <form method="POST" action="{{ route('staff.conference.applications.update', ['application' => $application->uuid]) }}" id="nomination-form" class="drip-stack" style="margin-top:24px;" novalidate>
                @csrf
                @method('PUT')

                {{-- I - vii : candidate --}}
                <div class="drip-card">
                    <div class="drip-card__body">
                        <h2 class="drip-form-section__title">Candidate</h2>
                        <p class="drip-form-section__sub">Items I to vii of the TETFund nomination form.</p>

                        <div class="drip-form-grid">
                            <div class="is-full">
                                <span class="drip-label">Name of beneficiary institution</span>
                                <div class="drip-control" style="display:flex; align-items:center; background:var(--muted);">{{ config('conference.institution') }}</div>
                            </div>

                            <x-form-field name="candidate_name" label="Name of candidate / staff" :value="$application->candidate_name" :required="true" :full="true" placeholder="Surname, first name and other names" />
                            <x-form-field name="gender" label="Gender" :value="$application->gender" :required="true" :options="['male' => 'Male', 'female' => 'Female']" />
                            <x-form-field name="date_of_birth" label="Date of birth" type="date" :value="$date($application->date_of_birth)" :required="true" />
                            <x-form-field name="staff_category" label="Staff category" :value="$application->staff_category" :required="true" :options="$category_options" />
                            <x-form-field name="present_position" label="Present position" :value="$application->present_position" help="For principal officers only. Leave blank otherwise." />
                            <x-form-field name="phone" label="Phone number" type="tel" :value="$application->phone" :required="true" placeholder="e.g. 0803 000 0000" />
                            <x-form-field name="funaab_email" label="FUNAAB email" type="email" :value="$application->funaab_email" :required="true" help="The call asks for your official FUNAAB email address." />
                        </div>
                    </div>
                </div>

                {{-- 1 - 7 : institutional record --}}
                <div class="drip-card">
                    <div class="drip-card__body">
                        <h2 class="drip-form-section__title">Candidate's data and institutional record</h2>
                        <p class="drip-form-section__sub">Items 1 to 7.</p>

                        <div class="drip-form-grid">
                            <x-form-field name="department" label="Department" :value="$application->department" :required="true" :full="true" />
                            <x-form-field name="highest_qualification" label="Highest qualification" :value="$application->highest_qualification" :required="true" placeholder="e.g. PhD" />
                            <x-form-field name="area_of_specialisation" label="Area of specialisation" :value="$application->area_of_specialisation" :required="true" />
                            <x-form-field name="staff_rank" label="Rank" :value="$application->staff_rank" :required="true" placeholder="e.g. Senior Lecturer" />
                            <x-form-field name="grade_level" label="Grade level" :value="$application->grade_level" :required="true" placeholder="e.g. CONUASS 5" />
                            <x-form-field name="work_experience_years" label="Duration of entire work experience (years)" type="number" step="1" inputmode="numeric" min="0" max="60" :value="$application->work_experience_years" :required="true" />
                        </div>
                    </div>
                </div>

                {{-- 8 : conference to be attended --}}
                <div class="drip-card">
                    <div class="drip-card__body">
                        <h2 class="drip-form-section__title">Conference or workshop to be attended</h2>
                        <p class="drip-form-section__sub">Item 8. Teaching staff apply for a conference, non-teaching staff for a workshop.</p>

                        <div class="drip-form-grid">
                            <x-form-field name="conference_organiser" label="Organiser(s)" :value="$application->conference_organiser" :required="true" :full="true" />
                            <x-form-field name="conference_title" label="Title" :value="$application->conference_title" :required="true" :full="true" />
                            <x-form-field name="conference_venue" label="Venue" :value="$application->conference_venue" :required="true" :full="true" help="Include the city and country." />

                            <x-form-field name="conference_scope" label="Local or overseas" :value="$application->conference_scope" :required="true" :options="['local' => 'Local', 'overseas' => 'Overseas']" />
                            <x-form-field name="conference_mode" label="How it is held" :value="$application->conference_mode" :required="true" :options="['in_person' => 'In person', 'online' => 'Online / virtual']" />

                            <div id="presentation-wrap" style="{{ $is_teaching ? '' : 'display:none;' }}">
                                <x-form-field name="presentation_type" label="How you will present your paper" :value="$application->presentation_type" :required="true" :options="['oral' => 'Oral presentation', 'poster' => 'Poster presentation']" />
                            </div>

                            <x-form-field name="conference_website" label="Website" type="url" :value="$application->conference_website" placeholder="https://" />
                            <x-form-field name="conference_email" label="Organiser's email" type="email" :value="$application->conference_email" />

                            <x-form-field name="conference_start_date" label="Start date" type="date" :value="$date($application->conference_start_date)" :required="true" />
                            <x-form-field name="conference_end_date" label="End date" type="date" :value="$date($application->conference_end_date)" :required="true" />

                            <div>
                                <span class="drip-label">Duration</span>
                                <div class="drip-control" id="duration-out" style="display:flex; align-items:center; background:var(--muted);">
                                    {{ $application->duration_days ? $application->duration_days.' '.\Illuminate\Support\Str::plural('day', $application->duration_days) : '-' }}
                                </div>
                                <div class="drip-help">Worked out from the dates. This call allows up to {{ $call->max_duration_days }} {{ \Illuminate\Support\Str::plural('day', $call->max_duration_days) }}.</div>
                            </div>
                        </div>

                        <div class="drip-form-section">
                            <h3 class="drip-form-section__title" style="font-size:1rem;">Costs</h3>
                            <p class="drip-form-section__sub">As shown in the conference announcement. Enter amounts in the currency you choose.</p>

                            <div class="drip-form-grid">
                                <x-form-field name="cost_currency" label="Currency" :value="$application->cost_currency" :required="true" :options="config('conference.currencies')" />
                                <div></div>
                                <x-form-field name="registration_fee" label="Registration fee" type="number" step="0.01" min="0" data-cost="1" :value="$money($application->registration_fee)" :required="true" />
                                <x-form-field name="flight_cost" label="Return flight" type="number" step="0.01" min="0" data-cost="1" :value="$money($application->flight_cost)" help="Where applicable." />
                                <x-form-field name="accommodation_cost" label="Accommodation" type="number" step="0.01" min="0" data-cost="1" :value="$money($application->accommodation_cost)" />
                                <x-form-field name="feeding_cost" label="Feeding" type="number" step="0.01" min="0" data-cost="1" :value="$money($application->feeding_cost)" />
                                <x-form-field name="excursion_cost" label="Excursion" type="number" step="0.01" min="0" data-cost="1" :value="$money($application->excursion_cost)" help="If any." />
                                <div>
                                    <span class="drip-label">Total</span>
                                    <div class="drip-control" id="total-cost" style="display:flex; align-items:center; background:var(--muted); font-weight:650;">0.00</div>
                                </div>
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 9 : attendance history --}}
                <div class="drip-card">
                    <div class="drip-card__body">
                        <h2 class="drip-form-section__title">Conference attendance history</h2>
                        <p class="drip-form-section__sub">Item 9. First-time beneficiaries are given preference.</p>

                        <label class="drip-check">
                            <input type="checkbox" id="previously_sponsored" name="previously_sponsored" value="1" @checked($previous)>
                            <span>I have been sponsored by TETFund to attend an international conference before.</span>
                        </label>

                        <div id="previous-wrap" style="{{ $previous ? '' : 'display:none;' }}">
                            <div class="drip-callout" style="margin:20px 0;">
                                Previous beneficiaries must show evidence of one Q1 or two Q2 articles. You will upload the evidence in the next step.
                            </div>

                            <div class="drip-form-grid">
                                <x-form-field name="last_conference_title" label="Title of the last conference attended" :value="$application->last_conference_title" :required="true" :full="true" />
                                <x-form-field name="last_conference_date" label="Date" type="date" :value="$date($application->last_conference_date)" :required="true" />
                                <x-form-field name="last_conference_venue" label="Venue" :value="$application->last_conference_venue" :required="true" />
                                <x-form-field name="last_conference_publication" label="Publication from the conference presentation (as cited)" type="textarea" :rows="3" :value="$application->last_conference_publication" :required="true" :full="true" />
                                <x-form-field name="publication_evidence_note" label="Q1 / Q2 articles you are offering as evidence" type="textarea" :rows="4" :value="$application->publication_evidence_note" :required="true" :full="true" help="For each article give the title, journal, year and whether it is Q1 or Q2." />
                            </div>
                        </div>
                    </div>
                </div>

                {{-- 10 : salary account --}}
                <div class="drip-card">
                    <div class="drip-card__body">
                        <h2 class="drip-form-section__title">Salary account details</h2>
                        <p class="drip-form-section__sub">Item 10.</p>

                        <div class="drip-callout" style="margin-bottom:20px; display:flex; gap:10px; align-items:flex-start;">
                            <x-icon name="lock" :size="18" />
                            <span>Your account number and sort code are stored encrypted and are visible only to authorised DRIP staff. They are never shown again on this page.</span>
                        </div>

                        <div class="drip-form-grid">
                            <x-form-field name="bank_name" label="Bank name" :value="$application->bank_name" :required="true" />
                            <x-form-field name="bank_branch" label="Branch" :value="$application->bank_branch" :required="true" />
                            <x-form-field name="account_name" label="Account name" :value="$application->account_name" :required="true" :full="true" />
                            <x-form-field name="account_number" label="Account number" :value="null" :required="true" inputmode="numeric" autocomplete="off" maxlength="10"
                                          :placeholder="$application->hasStoredSecret('account_number') ? $application->masked_account_number : '10-digit account number'"
                                          :help="$application->hasStoredSecret('account_number') ? 'A number is already saved. Leave blank to keep it, or type a new one to replace it.' : null" />
                            <x-form-field name="sort_code" label="Sort code" :value="null" :required="true" inputmode="numeric" autocomplete="off"
                                          :placeholder="$application->hasStoredSecret('sort_code') ? 'Saved' : 'e.g. 044150149'"
                                          :help="$application->hasStoredSecret('sort_code') ? 'A sort code is already saved. Leave blank to keep it.' : null" />
                        </div>
                    </div>
                </div>

                {{-- declaration --}}
                <div class="drip-card">
                    <div class="drip-card__body">
                        <h2 class="drip-form-section__title">Declaration</h2>
                        <label class="drip-check" style="margin-top:12px;">
                            <input type="checkbox" name="declaration_accepted" value="1" @checked($declared)>
                            <span>I declare that the information I have given is true and complete. I understand that a form that is not properly filled will be automatically disqualified.</span>
                        </label>
                    </div>
                </div>

                <div class="drip-sticky-bar">
                    <span class="drip-sticky-bar__note">Draft last saved {{ $application->updated_at->diffForHumans() }}. Saving does not submit your application.
                        @if (! $form_complete) Fill in every required field and save to unlock Documents and submission. @endif</span>
                    <div style="display:flex; gap:10px; flex-wrap:wrap;">
                        @if ($form_complete)
                            <a href="{{ route('staff.conference.applications.documents', ['application' => $application->uuid]) }}" class="drip-btn drip-btn--outline">Documents and submission <x-icon name="chevron-right" :size="18" /></a>
                        @endif
                        <button type="submit" class="drip-btn drip-btn--primary"><x-icon name="check" :size="18" /> Save draft</button>
                    </div>
                </div>
            </form>
            @endif

        </div>
    </div>

    <script>
        (function () {
            var get = function (id) { return document.getElementById(id); };
            var MAX_DAYS = {{ (int) $call->max_duration_days }};

            // show or hide the parts of the form that only apply to some people
            var category = get('staff_category'), presentationWrap = get('presentation-wrap');
            var previous = get('previously_sponsored'), previousWrap = get('previous-wrap');
            function toggle() {
                if (category && presentationWrap) { presentationWrap.style.display = category.value === 'teaching' ? '' : 'none'; }
                if (previous && previousWrap) { previousWrap.style.display = previous.checked ? '' : 'none'; }
            }
            if (category) { category.addEventListener('change', toggle); }
            if (previous) { previous.addEventListener('change', toggle); }
            toggle();

            // duration, worked out from the two dates
            var start = get('conference_start_date'), end = get('conference_end_date'), durationOut = get('duration-out');
            function duration() {
                if (!start || !end || !durationOut) { return; }
                if (!start.value || !end.value) { durationOut.textContent = '-'; durationOut.style.color = ''; return; }
                var days = Math.round((new Date(end.value) - new Date(start.value)) / 86400000) + 1;
                if (days < 1) {
                    durationOut.textContent = 'The end date is before the start date';
                } else {
                    durationOut.textContent = days + (days === 1 ? ' day' : ' days');
                }
                durationOut.style.color = (days < 1 || days > MAX_DAYS) ? 'var(--destructive)' : '';
            }
            if (start) { start.addEventListener('change', duration); }
            if (end) { end.addEventListener('change', duration); }
            duration();

            // eligibility card: show the confirmation date only for confirmed staff, and the years of service live
            var yes = get('confirmed_yes'), no = get('confirmed_no'), confirmationWrap = get('confirmation-wrap');
            function confirmationToggle() { if (confirmationWrap && yes) { confirmationWrap.style.display = yes.checked ? '' : 'none'; } }
            if (yes) { yes.addEventListener('change', confirmationToggle); }
            if (no) { no.addEventListener('change', confirmationToggle); }

            var appointed = get('first_appointment_date'), yearsOut = get('years-out');
            if (appointed && yearsOut) {
                appointed.addEventListener('change', function () {
                    if (!appointed.value) { yearsOut.textContent = '-'; return; }
                    var from = new Date(appointed.value), now = new Date();
                    var years = now.getFullYear() - from.getFullYear();
                    if (now.getMonth() < from.getMonth() || (now.getMonth() === from.getMonth() && now.getDate() < from.getDate())) { years--; }
                    yearsOut.textContent = years >= 0 ? years : '-';
                });
            }

            // running total of the costs
            var costs = document.querySelectorAll('[data-cost]'), totalOut = get('total-cost');
            function total() {
                var sum = 0;
                costs.forEach(function (input) { var n = parseFloat(input.value); if (!isNaN(n)) { sum += n; } });
                if (totalOut) { totalOut.textContent = sum.toLocaleString(undefined, { minimumFractionDigits: 2, maximumFractionDigits: 2 }); }
            }
            costs.forEach(function (input) { input.addEventListener('input', total); });
            total();
        })();
    </script>
</x-staff-layout>
