<x-guest-layout>
    <div class="drip-page">
        <div class="drip-container">

            <div class="drip-page-head" style="margin-top:0;">
                <div>
                    <h1 class="drip-h1" style="font-size:2rem;">Research calls and grants</h1>
                    <div class="drip-subtitle"><x-icon name="book-open" :size="20" /> <span>Calls for IBR proposals and Conference/Workshop sponsorship from the Directorate of Research, Innovations and Partnerships</span></div>
                </div>
            </div>

            <div class="drip-home">

                {{-- latest calls --}}
                <section>
                    <div class="drip-section-head" style="margin-top:0;">
                        <div>
                            <h2 class="drip-section-title">Latest calls for Research Proposals and Conferences/Workshops</h2>
                            <div class="drip-section-sub">Each call is marked IBR Proposal or Conference/Workshop. Sign in to apply to a call that is open.</div>
                        </div>
                        <div class="drip-call-links">
                            <a href="{{ route('guest.call_for_proposals.index') }}" class="drip-btn drip-btn--outline drip-btn--sm"><x-icon name="book-open" :size="15" /> Call for IBR Proposals</a>
                            <a href="{{ route('guest.conference_calls.index') }}" class="drip-btn drip-btn--outline drip-btn--sm"><x-icon name="graduation-cap" :size="15" /> Call for Conference/Workshop</a>
                        </div>
                    </div>

                    <div class="drip-card">
                        @forelse ($latest_calls as $item)
                            <x-public-call :call="$item['call']" :type="$item['type']" />
                        @empty
                            <div class="drip-empty">
                                <strong>No calls have been published yet</strong>
                                New calls will appear here as soon as they are announced.
                            </div>
                        @endforelse
                    </div>

                    @if ($latest_calls->count())
                        <p class="drip-note"><x-icon name="info" :size="16" /> <span>Showing the {{ $latest_calls->count() > 1 ? $latest_calls->count().' most recent calls' : 'most recent call' }}. Use the two buttons above to see every call of each kind.</span></p>
                    @endif
                </section>

                {{-- sign in --}}
                <aside class="drip-home__aside" id="signin">
                    <div class="drip-card">
                        <div class="drip-card__body">
                            <h2 class="drip-card__title">Staff sign in</h2>
                            <p class="drip-form-section__sub">Use the email address and password of your DRIP account.</p>

                            <x-flash />
                            @if (session('status'))
                                <div class="drip-alert drip-alert--success"><x-icon name="circle-check" :size="18" /><span>{{ session('status') }}</span></div>
                            @endif

                            <form action="{{ route('staff.auth.login') }}" method="POST" class="drip-auth__body" style="margin-top:20px;" novalidate>
                                @csrf

                                <x-form-field name="email" label="Email address" type="email" required autocomplete="username" autofocus placeholder="you@funaab.edu.ng" />
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
                            New to the portal? <a href="{{ route('guest.auth.register') }}" class="drip-link">Create an account</a>
                        </div>
                    </div>

                </aside>
            </div>

        </div>
    </div>
</x-guest-layout>
