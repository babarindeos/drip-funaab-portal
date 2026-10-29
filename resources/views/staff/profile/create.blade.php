<x-staff-layout>
    @php
        $user = auth()->user();
        $is_new = $profile === null;
    @endphp

    <div class="drip-page">
        <div class="drip-container">
            <div class="drip-auth" style="max-width:640px;">
                <div class="drip-auth__head">
                    <div class="drip-auth__badge"><x-icon name="user" :size="26" /></div>
                    <h1 class="drip-auth__title">{{ $is_new ? 'Welcome, '.($user->firstname ?: 'colleague') : 'Your profile' }}</h1>
                    <p class="drip-auth__sub">{{ $is_new ? 'Before you start, tell us your designation and phone number. It takes a minute.' : 'Check your details, then continue to your dashboard.' }}</p>
                </div>

                <form action="{{ route('staff.profile.store') }}" method="POST" enctype="multipart/form-data" novalidate>
                    @csrf

                    <div class="drip-card">
                        <div class="drip-card__body">
                            <x-flash />

                            <section class="drip-form-section" style="margin-top:4px;">
                                <h2 class="drip-form-section__title">Your details</h2>
                                <p class="drip-form-section__sub">These are pre-filled on the applications you make.</p>

                                <div class="drip-form-grid">
                                    <div class="is-full">
                                        <span class="drip-label">Name and email</span>
                                        <input type="text" class="drip-control" value="{{ trim($user->surname.' '.$user->firstname.' '.$user->middlename) }} &middot; {{ $user->email }}" disabled>
                                    </div>
                                    <x-form-field name="designation" label="Designation" required :value="optional($profile)->designation"
                                                  placeholder="e.g. Lecturer II" autofocus />
                                    <x-form-field name="phone" label="Phone number" type="tel" required :value="optional($profile)->phone"
                                                  placeholder="0803 000 0000" autocomplete="tel" />
                                </div>
                            </section>

                            <section class="drip-form-section">
                                <h2 class="drip-form-section__title">Photo <span class="drip-muted" style="font-weight:500;">(optional)</span></h2>
                                <p class="drip-form-section__sub">Shown on your profile and in the header. You can add it later.</p>

                                <div style="display:flex; flex-wrap:wrap; align-items:center; gap:20px;">
                                    <x-profile-photo :user="$user" :profile="$profile" small style="margin:0;" />
                                    <div style="flex:1 1 280px;">
                                        <input id="avatar" name="avatar" type="file" accept=".jpg,.jpeg,.png" class="drip-control {{ $errors->has('avatar') ? 'is-invalid' : '' }}">
                                        <div class="drip-help">JPG or PNG, up to 2 MB, at least 100 x 100 pixels.</div>
                                        @error('avatar')<div class="drip-error">{{ $message }}</div>@enderror
                                    </div>
                                </div>
                            </section>
                        </div>

                        <div class="drip-form-actions">
                            @unless ($is_new)
                                <a href="{{ route('staff.dashboard.index') }}" class="drip-btn drip-btn--outline">Go to dashboard</a>
                            @endunless
                            <button type="submit" class="drip-btn drip-btn--primary"><x-icon name="check" :size="18" /> {{ $is_new ? 'Save and continue' : 'Save changes' }}</button>
                        </div>
                    </div>
                </form>
            </div>
        </div>
    </div>
</x-staff-layout>
