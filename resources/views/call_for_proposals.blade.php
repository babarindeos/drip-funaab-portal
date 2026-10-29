<x-guest-layout>
    <div class="drip-page">
        <div class="drip-container">

            <nav class="drip-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('welcome') }}">Home</a>
                <x-icon name="chevron-right" :size="14" />
                <span aria-current="page">Call for IBR Proposals</span>
            </nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1">Call for IBR Proposals @if ($call_for_proposals->total())<span class="drip-count">{{ $call_for_proposals->total() }}</span>@endif</h1>
                    <div class="drip-subtitle"><x-icon name="book-open" :size="20" /> <span>Institution-Based Research (IBR) calls published by DRIP, open and past</span></div>
                </div>
                <div class="drip-actions">
                    <a href="{{ route('welcome') }}#signin" class="drip-btn drip-btn--primary"><x-icon name="log-in" :size="18" /> Sign in to apply</a>
                </div>
            </div>

            <div class="drip-card" style="margin-top:28px;">
                @forelse ($call_for_proposals as $call_for_proposal)
                    <x-public-call :call="$call_for_proposal" type="ibr" />
                @empty
                    <div class="drip-empty">
                        <strong>No IBR calls have been published yet</strong>
                        New calls for IBR proposals will appear here as soon as they are announced.
                    </div>
                @endforelse

                <x-pager :paginator="$call_for_proposals" noun="calls" />
            </div>

            <p class="drip-note"><x-icon name="graduation-cap" :size="16" /> <span>Looking for conference or workshop sponsorship? See the <a href="{{ route('guest.conference_calls.index') }}" class="drip-link">Call for Conference/Workshop</a>.</span></p>
            <p class="drip-note" style="margin-top:8px;"><x-icon name="info" :size="16" /> <span>You need a DRIP account to apply. <a href="{{ route('guest.auth.register') }}" class="drip-link">Create one here</a>; your password is emailed to you.</span></p>
        </div>
    </div>
</x-guest-layout>
