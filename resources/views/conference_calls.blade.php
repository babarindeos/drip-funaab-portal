<x-guest-layout>
    <div class="drip-page">
        <div class="drip-container">

            <nav class="drip-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('welcome') }}">Home</a>
                <x-icon name="chevron-right" :size="14" />
                <span aria-current="page">Call for Conference/Workshop</span>
            </nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1">Call for Conference/Workshop @if ($conference_calls->total())<span class="drip-count">{{ $conference_calls->total() }}</span>@endif</h1>
                    <div class="drip-subtitle"><x-icon name="graduation-cap" :size="20" /> <span>TETFund sponsorship to attend conferences (teaching staff) and workshops (non-teaching staff)</span></div>
                </div>
                <div class="drip-actions">
                    <a href="{{ route('welcome') }}#signin" class="drip-btn drip-btn--primary"><x-icon name="log-in" :size="18" /> Sign in to apply</a>
                </div>
            </div>

            <div class="drip-card" style="margin-top:28px;">
                @if (! $conference_available)
                    <div class="drip-empty">
                        <strong>These calls cannot be shown right now</strong>
                        Please try again later.
                    </div>
                @else
                    @forelse ($conference_calls as $conference_call)
                        <x-public-call :call="$conference_call" type="conference" />
                    @empty
                        <div class="drip-empty">
                            <strong>No conference or workshop calls have been published yet</strong>
                            New calls will appear here as soon as they are announced.
                        </div>
                    @endforelse

                    <x-pager :paginator="$conference_calls" noun="calls" />
                @endif
            </div>

            <p class="drip-note"><x-icon name="book-open" :size="16" /> <span>Looking for research funding? See the <a href="{{ route('guest.call_for_proposals.index') }}" class="drip-link">Call for IBR Proposals</a>.</span></p>
            <p class="drip-note" style="margin-top:8px;"><x-icon name="info" :size="16" /> <span>You need a DRIP account to apply. <a href="{{ route('guest.auth.register') }}" class="drip-link">Create one here</a>; your password is emailed to you.</span></p>
        </div>
    </div>
</x-guest-layout>
