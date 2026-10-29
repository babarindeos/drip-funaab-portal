<x-guest-layout>
    <div class="drip-page">
        <div class="drip-container">
            <div class="drip-auth">
                <div class="drip-auth__head">
                    <div class="drip-auth__badge"><x-icon name="key" :size="26" /></div>
                    <h1 class="drip-auth__title">Forgot your password?</h1>
                    <p class="drip-auth__sub">Enter the email address of your account and we will email you a link to set a new password.</p>
                </div>

                <div class="drip-card">
                    <div class="drip-card__body">
                        @if (session('status'))
                            <div class="drip-alert drip-alert--success" style="margin-top:0;"><x-icon name="circle-check" :size="18" /><span>{{ session('status') }}</span></div>
                        @endif

                        <form action="{{ route('password.email') }}" method="POST" class="drip-auth__body" style="margin-top:{{ session('status') ? '20px' : '0' }};" novalidate>
                            @csrf

                            <x-form-field name="email" label="Email address" type="email" required autocomplete="email" autofocus />

                            <button type="submit" class="drip-btn drip-btn--primary drip-btn--block"><x-icon name="mail" :size="18" /> Email me a link</button>
                        </form>
                    </div>
                    <div class="drip-auth__foot">
                        Remembered it? <a href="{{ route('welcome') }}#signin" class="drip-link">Back to sign in</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
