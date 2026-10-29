<x-staff-layout>
    <div class="drip-page">
        <div class="drip-container">

            @include('staff.profile._head', ['active' => 'signature', 'title' => 'My signature'])

            <div class="drip-card" style="margin-top:28px; max-width:720px;">
                <div class="drip-card__body">
                    <h2 class="drip-form-section__title">Your signature</h2>
                    <p class="drip-form-section__sub">Used where documents in the workflow are signed. Upload a PNG with a clear or white background.</p>

                    <div class="drip-signature">
                        @if ($signature && filled($signature->signature))
                            <img src="{{ asset('storage/'.$signature->signature) }}" alt="Your signature">
                        @else
                            <span class="drip-muted">No signature uploaded yet</span>
                        @endif
                    </div>

                    <form action="{{ route($signature ? 'staff.profile.update_signature' : 'staff.profile.upload_signature') }}" method="POST" enctype="multipart/form-data" style="margin-top:24px;" novalidate>
                        @csrf
                        <label class="drip-label" for="signature">{{ $signature ? 'Replace signature' : 'Upload signature' }} <span class="req">*</span></label>
                        <div class="drip-upload">
                            <input id="signature" name="signature" type="file" accept=".png" required class="drip-control {{ $errors->has('signature') ? 'is-invalid' : '' }}">
                            <button type="submit" class="drip-btn drip-btn--primary"><x-icon name="upload" :size="16" /> {{ $signature ? 'Replace' : 'Upload' }}</button>
                        </div>
                        <div class="drip-help">PNG only, up to 1 MB.</div>
                        @error('signature')<div class="drip-error">{{ $message }}</div>@enderror
                    </form>
                </div>
            </div>

        </div>
    </div>
</x-staff-layout>
