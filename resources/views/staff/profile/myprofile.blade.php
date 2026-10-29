<x-staff-layout>
    @php
        $full_name = trim($user->surname.' '.$user->firstname.' '.$user->middlename);
    @endphp

    <div class="drip-page">
        <div class="drip-container">

            @include('staff.profile._head', ['active' => 'profile', 'title' => 'My profile', 'subtitle' => 'Your details, as DRIP sees them on your applications'])

            <div class="drip-profile-grid">

                {{-- photo --}}
                <div class="drip-card drip-profile-card">
                    <div class="drip-card__body">
                        <x-profile-photo :user="$user" :profile="$profile" />
                        <div class="drip-profile-card__name">{{ $full_name }}</div>
                        <div class="drip-profile-card__sub">{{ $profile->designation }}</div>

                        <form action="{{ route('staff.profile.myprofile.update_avatar') }}" method="POST" enctype="multipart/form-data" style="margin-top:24px; text-align:left;">
                            @csrf
                            <label class="drip-label" for="photo">{{ filled($profile->avatar) ? 'Change photo' : 'Add a photo' }}</label>
                            <input id="photo" name="photo" type="file" accept=".jpg,.jpeg,.png" required class="drip-control {{ $errors->has('photo') ? 'is-invalid' : '' }}">
                            <div class="drip-help">JPG or PNG, up to 2 MB, at least 100 x 100 pixels.</div>
                            @error('photo')<div class="drip-error">{{ $message }}</div>@enderror
                            <button type="submit" class="drip-btn drip-btn--outline drip-btn--sm drip-btn--block" style="margin-top:12px;"><x-icon name="upload" :size="15" /> Upload photo</button>
                        </form>
                    </div>
                </div>

                {{-- details --}}
                <div class="drip-card">
                    <div class="drip-card__body">
                        <div class="drip-section-head" style="margin:0 0 20px;">
                            <div>
                                <h2 class="drip-form-section__title">Personal details</h2>
                                <p class="drip-form-section__sub" style="margin:0;">Your name and email come from your account. Designation and phone can be edited.</p>
                            </div>
                            <a href="{{ route('staff.profile.myprofile.edit') }}" class="drip-btn drip-btn--outline drip-btn--sm"><x-icon name="pencil" :size="15" /> Edit details</a>
                        </div>

                        <dl class="drip-dl">
                            <dt>Full name</dt>
                            <dd>{{ $full_name }}</dd>

                            <dt>Email address</dt>
                            <dd>{{ $user->email }}</dd>

                            <dt>Designation</dt>
                            <dd>{{ $profile->designation ?: '—' }}</dd>

                            <dt>Phone number</dt>
                            <dd>{{ $profile->phone ?: '—' }}</dd>

                            <dt>Member since</dt>
                            <dd>{{ optional($user->created_at)->format('F j, Y') ?? '—' }}</dd>
                        </dl>

                        <div class="drip-form-section" style="margin-top:28px; padding-top:28px; border-top:1px solid var(--border);">
                            <h2 class="drip-form-section__title">Sign-in and security</h2>
                            <p class="drip-form-section__sub">You sign in with {{ $user->email }}.</p>
                            <a href="{{ route('staff.profile.change_password') }}" class="drip-btn drip-btn--outline drip-btn--sm"><x-icon name="key" :size="15" /> Change password</a>
                        </div>

                        <p class="drip-note"><x-icon name="info" :size="16" /> <span>Your name or email is wrong? Contact DRIP to have your account corrected.</span></p>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-staff-layout>
