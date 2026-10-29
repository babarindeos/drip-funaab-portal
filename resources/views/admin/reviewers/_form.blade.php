<div class="drip-card">
    <div class="drip-card__body">
        @if ($errors->any())
            <div class="drip-alert drip-alert--error" style="margin: 0 0 24px;">
                <x-icon name="circle-x" :size="18" /> <span>Please correct the highlighted fields and try again.</span>
            </div>
        @endif

        <div class="drip-form-grid">
            <x-form-field name="name" label="Full name" :value="$reviewer->name" :required="true" placeholder="e.g. Prof. Ada Obi" />
            <x-form-field name="expertise_area" label="Area of expertise" :value="$reviewer->expertise_area" :required="true" placeholder="e.g. Plant breeding" />
            <x-form-field name="email" label="Email address" type="email" :value="$reviewer->email" :required="true" :full="true" help="The review link for each proposal is emailed here." />
        </div>
    </div>

    <div class="drip-form-actions">
        <a href="{{ route('admin.reviewers.index') }}" class="drip-btn drip-btn--outline">Cancel</a>
        <button type="submit" class="drip-btn drip-btn--primary"><x-icon name="check" :size="18" /> {{ $submit_label }}</button>
    </div>
</div>
