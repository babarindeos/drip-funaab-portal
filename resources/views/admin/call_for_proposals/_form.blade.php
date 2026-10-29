{{-- Shared by create and edit. $call is a CallForProposal (new and empty when creating). --}}
<div class="drip-card">
    <div class="drip-card__body">

        <x-flash />

        @if ($errors->any())
            <div class="drip-alert drip-alert--error" style="margin: 0 0 24px;">
                <x-icon name="circle-x" :size="18" />
                <span>Please correct the highlighted fields and try again.</span>
            </div>
        @endif

        <div class="drip-form-section">
            <h2 class="drip-form-section__title">Call details</h2>
            <p class="drip-form-section__sub">What staff will see when they browse the call.</p>
            <div class="drip-form-grid">
                <x-form-field name="title" label="Title" :value="$call->title" :required="true" :full="true" placeholder="e.g. Institution Based Research Grant 2026" />
                <x-form-field name="description" label="Description" type="textarea" :rows="5" :value="$call->description" :full="true" placeholder="A short summary of the call and who it is for" />
            </div>
        </div>

        <div class="drip-form-section">
            <h2 class="drip-form-section__title">Dates</h2>
            <p class="drip-form-section__sub">The call opens and closes automatically on these dates.</p>
            <div class="drip-form-grid">
                <x-form-field name="open_date" label="Opening date" type="date" :value="optional($call->open_date)->format('Y-m-d')" :required="true" />
                <x-form-field name="close_date" label="Closing date" type="date" :value="optional($call->close_date)->format('Y-m-d')" :required="true" help="Applications close at the end of this day." />
            </div>
        </div>

        <div class="drip-form-section">
            <h2 class="drip-form-section__title">Advert</h2>
            <p class="drip-form-section__sub">Optional. Shown to staff and on the public page.</p>
            <div class="drip-form-grid">
                <div class="is-full">
                    <label class="drip-label" for="advert">Advert (image or PDF)</label>
                    <input id="advert" name="advert" type="file" accept=".jpg,.jpeg,.png,.pdf" class="drip-control @error('advert') is-invalid @enderror">
                    <div class="drip-help">
                        JPG, PNG or PDF, up to 10 MB.
                        @if ($call->advert)
                            Current file: <a href="{{ asset('storage/'.$call->advert) }}" target="_blank" class="drip-clear">view advert</a>. Choosing a new file replaces it.
                        @endif
                    </div>
                    @error('advert')<div class="drip-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>

        <div class="drip-form-section">
            <h2 class="drip-form-section__title">Publishing</h2>
            <p class="drip-form-section__sub">Choose whether staff can see this call.</p>
            <div class="drip-form-grid">
                <div>
                    <label class="drip-label" for="status">Visibility</label>
                    <select id="status" name="status" class="drip-control @error('status') is-invalid @enderror">
                        <option value="" @selected(blank(old('status', $call->status)))>Published (opens and closes on the dates above)</option>
                        <option value="draft" @selected(old('status', $call->status) === 'draft')>Draft (hidden while you prepare it)</option>
                        @if ($call->exists)
                            <option value="archived" @selected(old('status', $call->status) === 'archived')>Archived (kept for records, hidden)</option>
                        @endif
                    </select>
                    @error('status')<div class="drip-error">{{ $message }}</div>@enderror
                </div>
            </div>
        </div>
    </div>

    <div class="drip-form-actions">
        <a href="{{ route('admin.call_for_proposals.index') }}" class="drip-btn drip-btn--outline">Cancel</a>
        <button type="submit" class="drip-btn drip-btn--primary"><x-icon name="check" :size="18" /> {{ $submit_label }}</button>
    </div>
</div>
