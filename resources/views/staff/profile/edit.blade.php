<x-staff-layout>
    <div class="drip-page">
        <div class="drip-container">

            @include('staff.profile._head', ['active' => 'edit', 'title' => 'Edit details'])

            <form action="{{ route('staff.profile.myprofile.update') }}" method="POST" enctype="multipart/form-data" novalidate>
                @csrf

                <div class="drip-card" style="margin-top:28px;">
                    <div class="drip-card__body">
                        <section class="drip-form-section">
                            <h2 class="drip-form-section__title">Your details</h2>
                            <p class="drip-form-section__sub">These are pre-filled on your applications. Your name and email come from your account.</p>

                            <div class="drip-form-grid">
                                <div>
                                    <span class="drip-label">Full name</span>
                                    <input type="text" class="drip-control" value="{{ trim($user->surname.' '.$user->firstname.' '.$user->middlename) }}" disabled>
                                </div>
                                <div>
                                    <span class="drip-label">Email address</span>
                                    <input type="text" class="drip-control" value="{{ $user->email }}" disabled>
                                </div>

                                <x-form-field name="designation" label="Designation" required :value="$profile->designation"
                                              help="Your rank or job title, for example Lecturer II or Senior Technologist." />
                                <x-form-field name="phone" label="Phone number" type="tel" required :value="$profile->phone" autocomplete="tel"
                                              placeholder="0803 000 0000" />
                            </div>
                        </section>

                        <section class="drip-form-section">
                            <h2 class="drip-form-section__title">Photo</h2>
                            <p class="drip-form-section__sub">Optional. Leave this empty to keep your current photo.</p>

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
                        <a href="{{ route('staff.profile.myprofile') }}" class="drip-btn drip-btn--outline">Cancel</a>
                        <button type="submit" class="drip-btn drip-btn--primary"><x-icon name="check" :size="18" /> Save changes</button>
                    </div>
                </div>
            </form>

        </div>
    </div>
</x-staff-layout>
