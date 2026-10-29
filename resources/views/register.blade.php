<x-guest-layout>
    <div class="drip-page">
        <div class="drip-container">
            <div class="drip-auth" style="max-width:560px;">
                <div class="drip-auth__head">
                    <div class="drip-auth__badge"><x-icon name="user-plus" :size="26" /></div>
                    <h1 class="drip-auth__title">Create your account</h1>
                    <p class="drip-auth__sub">For FUNAAB staff applying to DRIP calls and grants.</p>
                </div>

                <div class="drip-card">
                    <div class="drip-card__body">
                        <x-flash />
                        @error('error')
                            <div class="drip-alert drip-alert--error"><x-icon name="circle-x" :size="18" /><span>{{ $message }}</span></div>
                        @enderror

                        @if (session('success'))
                            <p class="drip-help" style="margin-top:14px;">Did not get it after a few minutes? Check your spam folder, or <a href="{{ route('password.request') }}" class="drip-link">ask for a new password</a>.</p>
                        @endif

                        <form action="{{ route('guest.auth.register.store') }}" method="POST" style="margin-top:20px;" novalidate>
                            @csrf

                            <div class="drip-form-grid">
                                <x-form-field name="surname" label="Surname" required autocomplete="family-name" autofocus />
                                <x-form-field name="firstname" label="First name" required autocomplete="given-name" />
                                <x-form-field name="middlename" label="Middle name" autocomplete="additional-name" full />
                                <x-form-field name="email" label="Email address" type="email" required autocomplete="email" full
                                              placeholder="you@funaab.edu.ng"
                                              help="Your sign-in details are sent to this address, so use one you can open now." />
                            </div>

                            <button type="submit" class="drip-btn drip-btn--primary drip-btn--block" style="margin-top:24px;"><x-icon name="user-plus" :size="18" /> Create account</button>
                        </form>
                    </div>
                    <div class="drip-auth__foot">
                        Already have an account? <a href="{{ route('welcome') }}#signin" class="drip-link">Sign in</a>
                    </div>
                </div>

                <p class="drip-note" style="justify-content:center;"><x-icon name="mail" :size="15" /> <span>We email you a password. You can change it from your profile after signing in.</span></p>
            </div>
        </div>
    </div>
</x-guest-layout>
