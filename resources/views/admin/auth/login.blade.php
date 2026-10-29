<x-guest-layout>
    <div class="drip-page">
        <div class="drip-container">
            <div class="drip-auth">
                <div class="drip-auth__head">
                    <div class="drip-auth__badge"><x-icon name="lock" :size="26" /></div>
                    <h1 class="drip-auth__title">Administrator sign in</h1>
                    <p class="drip-auth__sub">For DRIP officers who manage calls, reviews and decisions.</p>
                </div>

                <div class="drip-card">
                    <div class="drip-card__body">
                        <x-flash />
                        @if (session('status'))
                            <div class="drip-alert drip-alert--success"><x-icon name="circle-check" :size="18" /><span>{{ session('status') }}</span></div>
                        @endif

                        <form action="{{ route('admin.auth.login') }}" method="POST" class="drip-auth__body" style="margin-top:4px;" novalidate>
                            @csrf

                            <x-form-field name="email" label="Email address" type="email" required autocomplete="username" autofocus />

                            <div>
                                <label class="drip-label" for="password">Password <span class="req">*</span></label>
                                <input id="password" name="password" type="password" required autocomplete="current-password" class="drip-control {{ $errors->has('password') ? 'is-invalid' : '' }}">
                                @error('password')<div class="drip-error">{{ $message }}</div>@enderror
                            </div>

                            <div class="drip-auth__row">
                                <label class="drip-check" style="font-size:.875rem;"><input type="checkbox" name="remember" value="1" @checked(old('remember'))> Keep me signed in</label>
                                <a href="{{ route('password.request') }}" class="drip-link">Forgot password?</a>
                            </div>

                            <button type="submit" class="drip-btn drip-btn--primary drip-btn--block"><x-icon name="log-in" :size="18" /> Sign in</button>
                        </form>
                    </div>
                    <div class="drip-auth__foot">
                        Not an administrator? <a href="{{ route('welcome') }}#signin" class="drip-link">Staff sign in</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
