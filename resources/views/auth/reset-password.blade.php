<x-guest-layout>
    <div class="drip-page">
        <div class="drip-container">
            <div class="drip-auth">
                <div class="drip-auth__head">
                    <div class="drip-auth__badge"><x-icon name="lock" :size="26" /></div>
                    <h1 class="drip-auth__title">Choose a new password</h1>
                    <p class="drip-auth__sub">Use at least 8 characters. You will sign in with it from now on.</p>
                </div>

                <div class="drip-card">
                    <div class="drip-card__body">
                        <form action="{{ route('password.store') }}" method="POST" class="drip-auth__body" novalidate>
                            @csrf
                            <input type="hidden" name="token" value="{{ $request->route('token') }}">

                            <x-form-field name="email" label="Email address" type="email" required autocomplete="username" :value="$request->email" />

                            <div>
                                <label class="drip-label" for="password">New password <span class="req">*</span></label>
                                <input id="password" name="password" type="password" required minlength="8" autocomplete="new-password" autofocus class="drip-control {{ $errors->has('password') ? 'is-invalid' : '' }}">
                                @error('password')<div class="drip-error">{{ $message }}</div>@enderror
                            </div>

                            <div>
                                <label class="drip-label" for="password_confirmation">Type it again <span class="req">*</span></label>
                                <input id="password_confirmation" name="password_confirmation" type="password" required autocomplete="new-password" class="drip-control">
                            </div>

                            <button type="submit" class="drip-btn drip-btn--primary drip-btn--block"><x-icon name="check" :size="18" /> Save new password</button>
                        </form>
                    </div>
                    <div class="drip-auth__foot">
                        Link expired? <a href="{{ route('password.request') }}" class="drip-link">Ask for a new one</a>
                    </div>
                </div>
            </div>
        </div>
    </div>
</x-guest-layout>
