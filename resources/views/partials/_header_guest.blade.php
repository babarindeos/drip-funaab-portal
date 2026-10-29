<div class="drip-header">
    <div class="drip-header__inner">
        <a href="{{ url('/') }}" class="drip-brand">
            <span class="drip-brand__logo"><img src="{{ asset('images/funaab_logo.png') }}" alt="FUNAAB crest"></span>
            <span>
                <span class="drip-brand__name" style="display:block;">DRIP</span>
                <span class="drip-brand__tag" style="display:block;">Directorate of Research, Innovations and Partnerships</span>
            </span>
        </a>

        <div class="drip-links">
            @auth
                {{-- A signed-in user can land on a public page (e.g. an admin opening a review link). --}}
                <a href="{{ Auth::user()->role === 'admin' ? route('admin.dashboard.index') : route('staff.dashboard.index') }}" class="is-solid">My dashboard</a>
            @else
                <a href="{{ route('guest.call_for_proposals.index') }}" class="{{ request()->routeIs('guest.call_for_proposals.*') ? 'is-current' : '' }}">Call for IBR Proposals</a>
                <a href="{{ route('guest.conference_calls.index') }}" class="{{ request()->routeIs('guest.conference_calls.*') ? 'is-current' : '' }}">Call for Conference/Workshop</a>
                <a href="{{ route('welcome') }}#signin" class="is-solid">Sign in</a>
            @endauth
        </div>
    </div>
</div>
