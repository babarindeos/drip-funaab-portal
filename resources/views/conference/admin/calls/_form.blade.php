@php
    // an unticked checkbox is missing from the request, so after a failed
    // validation fall back to the old input only if there was any
    $teaching = $errors->any() ? (bool) old('allow_teaching') : (bool) $call->allow_teaching;
    $non_teaching = $errors->any() ? (bool) old('allow_non_teaching') : (bool) $call->allow_non_teaching;
@endphp

<div class="drip-card">
    <div class="drip-card__body">

        @if ($errors->any())
            <div class="drip-alert drip-alert--error" style="margin: 0 0 24px;">
                <x-icon name="circle-x" :size="18" />
                <span>Please correct the highlighted fields and try again.</span>
            </div>
        @endif

        {{-- call details --}}
        <div class="drip-form-section">
            <h2 class="drip-form-section__title">Call details</h2>
            <p class="drip-form-section__sub">What staff will see when they browse the call.</p>

            <div class="drip-form-grid">
                <div class="is-full">
                    <label class="drip-label" for="title">Title <span class="req">*</span></label>
                    <input id="title" name="title" type="text" value="{{ old('title', $call->title) }}" class="drip-control @error('title') is-invalid @enderror" placeholder="e.g. Conference/Workshop TETFund Attendance Grant 2026" required>
                    @error('title')<div class="drip-error">{{ $message }}</div>@enderror
                </div>

                <div class="is-full">
                    <label class="drip-label" for="description">Description</label>
                    <textarea id="description" name="description" class="drip-control @error('description') is-invalid @enderror" placeholder="A short summary of the grant and who it is for">{{ old('description', $call->description) }}</textarea>
                    @error('description')<div class="drip-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- dates and rules --}}
        <div class="drip-form-section">
            <h2 class="drip-form-section__title">Dates and rules</h2>
            <p class="drip-form-section__sub">Applications are checked against these automatically, so nothing needs to be typed into the form by hand.</p>

            <div class="drip-form-grid">
                <div>
                    <label class="drip-label" for="open_date">Opening date <span class="req">*</span></label>
                    <input id="open_date" name="open_date" type="date" value="{{ old('open_date', optional($call->open_date)->format('Y-m-d')) }}" class="drip-control @error('open_date') is-invalid @enderror" required>
                    @error('open_date')<div class="drip-error">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="drip-label" for="close_date">Application deadline <span class="req">*</span></label>
                    <input id="close_date" name="close_date" type="date" value="{{ old('close_date', optional($call->close_date)->format('Y-m-d')) }}" class="drip-control @error('close_date') is-invalid @enderror" required>
                    <div class="drip-help">Applications close at the end of this day.</div>
                    @error('close_date')<div class="drip-error">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="drip-label" for="earliest_conference_date">Earliest eligible conference date <span class="req">*</span></label>
                    <input id="earliest_conference_date" name="earliest_conference_date" type="date" value="{{ old('earliest_conference_date', optional($call->earliest_conference_date)->format('Y-m-d')) }}" class="drip-control @error('earliest_conference_date') is-invalid @enderror" required>
                    <div class="drip-help">A conference or workshop starting before this date is not eligible.</div>
                    @error('earliest_conference_date')<div class="drip-error">{{ $message }}</div>@enderror
                </div>

                <div>
                    <label class="drip-label" for="max_duration_days">Maximum duration (days) <span class="req">*</span></label>
                    <input id="max_duration_days" name="max_duration_days" type="number" min="1" max="30" value="{{ old('max_duration_days', $call->max_duration_days) }}" class="drip-control @error('max_duration_days') is-invalid @enderror" required>
                    <div class="drip-help">The longest conference or workshop that can be supported.</div>
                    @error('max_duration_days')<div class="drip-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- who can apply --}}
        <div class="drip-form-section">
            <h2 class="drip-form-section__title">Who can apply</h2>
            <p class="drip-form-section__sub">Teaching staff apply for conferences and non-teaching staff for workshops.</p>

            <div style="display:flex; flex-direction:column; gap:14px;">
                <label class="drip-check">
                    <input type="checkbox" name="allow_teaching" value="1" @checked($teaching)>
                    <span>Teaching staff</span>
                </label>
                <label class="drip-check">
                    <input type="checkbox" name="allow_non_teaching" value="1" @checked($non_teaching)>
                    <span>Non-teaching staff</span>
                </label>
            </div>
            @error('allow_teaching')<div class="drip-error">{{ $message }}</div>@enderror
        </div>

        {{-- advert and instructions --}}
        <div class="drip-form-section">
            <h2 class="drip-form-section__title">Advert and instructions</h2>
            <p class="drip-form-section__sub">Optional, but helpful.</p>

            <div class="drip-form-grid">
                <div class="is-full">
                    <label class="drip-label" for="advert">Advert or circular</label>
                    <input id="advert" name="advert" type="file" accept=".pdf,.jpg,.jpeg,.png,.doc,.docx" class="drip-control @error('advert') is-invalid @enderror">
                    <div class="drip-help">
                        PDF, image or Word file, up to 10 MB.
                        @if ($call->advert)
                            Current file: <a href="{{ asset('storage/'.$call->advert) }}" target="_blank" class="drip-clear">view advert</a>. Choosing a new file replaces it.
                        @endif
                    </div>
                    @error('advert')<div class="drip-error">{{ $message }}</div>@enderror
                </div>

                <div class="is-full">
                    <label class="drip-label" for="approval_instructions">Instructions for approved applicants</label>
                    <textarea id="approval_instructions" name="approval_instructions" class="drip-control @error('approval_instructions') is-invalid @enderror" placeholder="e.g. Proceed to the TETFund portal at https://... and complete your registration">{{ old('approval_instructions', $call->approval_instructions) }}</textarea>
                    <div class="drip-help">Shown to an applicant once their application is approved, for example the TETFund portal link and what to do next.</div>
                    @error('approval_instructions')<div class="drip-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        {{-- publishing --}}
        <div class="drip-form-section">
            <h2 class="drip-form-section__title">Publishing</h2>
            <p class="drip-form-section__sub">Choose whether staff can see this call.</p>

            <div class="drip-form-grid">
                <div>
                    <label class="drip-label" for="status">Visibility</label>
                    <select id="status" name="status" class="drip-control @error('status') is-invalid @enderror">
                        <option value="" @selected(old('status', $call->status) === null || old('status', $call->status) === '')>Published (opens and closes on the dates above)</option>
                        <option value="draft" @selected(old('status', $call->status) === 'draft')>Draft (hidden from staff while you prepare it)</option>
                        <option value="archived" @selected(old('status', $call->status) === 'archived')>Archived (kept for records, hidden from staff)</option>
                    </select>
                    @error('status')<div class="drip-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

    </div>

    <div class="drip-form-actions">
        <a href="{{ route('admin.conference.calls.index') }}" class="drip-btn drip-btn--outline">Cancel</a>
        <button type="submit" class="drip-btn drip-btn--primary"><x-icon name="check" :size="18" /> {{ $submit_label }}</button>
    </div>
</div>
