<x-staff-layout>
    <div class="drip-page">
        <div class="drip-container">

            @include('staff.profile._head', ['active' => 'password', 'title' => 'Change password'])

            <form action="{{ route('staff.profile.update_password') }}" method="POST" novalidate>
                @csrf

                <div class="drip-card" style="margin-top:28px; max-width:720px;">
                    <div class="drip-card__body">
                        <h2 class="drip-form-section__title">Choose a new password</h2>
                        <p class="drip-form-section__sub">Use at least 8 characters. If you were emailed a password when you registered, change it here.</p>

                        <div class="drip-stack">
                            <div>
                                <label class="drip-label" for="current_password">Current password <span class="req">*</span></label>
                                <input id="current_password" name="current_password" type="password" required autocomplete="current-password" class="drip-control {{ $errors->has('current_password') ? 'is-invalid' : '' }}">
                                @error('current_password')<div class="drip-error">{{ $message }}</div>@enderror
                            </div>

                            <div class="drip-form-grid">
                                <div>
                                    <label class="drip-label" for="new_password">New password <span class="req">*</span></label>
                                    <input id="new_password" name="new_password" type="password" required minlength="8" autocomplete="new-password" class="drip-control {{ $errors->has('new_password') ? 'is-invalid' : '' }}">
                                    @error('new_password')<div class="drip-error">{{ $message }}</div>@enderror
                                </div>
                                <div>
                                    <label class="drip-label" for="new_password_confirmation">Type it again <span class="req">*</span></label>
                                    <input id="new_password_confirmation" name="new_password_confirmation" type="password" required autocomplete="new-password" class="drip-control">
                                </div>
                            </div>
                        </div>

                        <p class="drip-note"><x-icon name="info" :size="16" /> <span>Forgotten your current password? Sign out and use <strong>Forgot password?</strong> on the sign-in page.</span></p>
                    </div>

                    <div class="drip-form-actions">
                        <a href="{{ route('staff.profile.myprofile') }}" class="drip-btn drip-btn--outline">Cancel</a>
                        <button type="submit" class="drip-btn drip-btn--primary"><x-icon name="key" :size="18" /> Change password</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</x-staff-layout>
