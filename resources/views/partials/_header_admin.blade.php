@auth
    @php
        $header_user = Auth::user();
        $header_name = trim(($header_user->firstname ?? '').' '.($header_user->surname ?? '')) ?: 'Administrator';
        $header_initials = strtoupper(mb_substr($header_user->firstname ?? '', 0, 1).mb_substr($header_user->surname ?? '', 0, 1))
                            ?: strtoupper(mb_substr($header_user->email ?? 'AD', 0, 2));

        $header_links = [
            ['Dashboard', 'admin.dashboard.index', 'dashboard', ['admin.dashboard.*']],
            ['Call for Proposals', 'admin.call_for_proposals.index', 'copy', ['admin.call_for_proposals.*']],
            ['Conference Grants', 'admin.conference.calls.index', 'graduation-cap', ['admin.conference.*']],
            ['Reviewers', 'admin.reviewers.index', 'users', ['admin.reviewers.*']],
            ['Analytics', 'admin.analytics.index', 'bar-chart', ['admin.analytics.*']],
        ];
    @endphp
    <div class="drip-header">
        <div class="drip-header__inner">
            <a href="{{ route('admin.dashboard.index') }}" class="drip-brand">
                <span class="drip-brand__logo"><img src="{{ asset('images/funaab-logo.png') }}" alt="FUNAAB crest" class="is-wide"></span>
                <span>
                    <span class="drip-brand__name" style="display:block;">DRIP</span>
                    <span class="drip-brand__tag" style="display:block;">Directorate of Research, Innovations and Partnerships</span>
                </span>
            </a>

            <nav class="drip-nav" aria-label="Main">
                @foreach ($header_links as [$label, $route_name, $icon, $patterns])
                    <a href="{{ route($route_name) }}" class="{{ request()->routeIs(...$patterns) ? 'is-active' : '' }}">
                        <x-icon :name="$icon" :size="18" /> {{ $label }}
                    </a>
                @endforeach
            </nav>

            <div class="drip-header__right">
                <details class="drip-user">
                    <summary>
                        <span class="drip-avatar">{{ $header_initials }}</span>
                        <span class="drip-user__text">
                            <span class="drip-user__name" style="display:block;">{{ $header_name }}</span>
                            <span class="drip-user__sub" style="display:block;">{{ config('drip.office_label') }}</span>
                        </span>
                        <x-icon name="chevron-down" :size="18" class="drip-chevron" />
                    </summary>
                    <div class="drip-user__menu">
                        <a href="{{ route('admin.maintenance.index') }}"><x-icon name="key" :size="16" /> Maintenance</a>
                        <form action="{{ route('admin.auth.logout') }}" method="POST">
                            @csrf
                            <button type="submit"><x-icon name="log-out" :size="16" /> Sign out</button>
                        </form>
                    </div>
                </details>
                <button type="button" class="drip-burger" id="drip-burger" aria-label="Menu" aria-expanded="false"><x-icon name="menu" :size="24" /></button>
            </div>
        </div>

        <div class="drip-mobile" id="drip-mobile">
            @foreach ($header_links as [$label, $route_name, $icon, $patterns])
                <a href="{{ route($route_name) }}" class="{{ request()->routeIs(...$patterns) ? 'is-active' : '' }}"><x-icon :name="$icon" :size="18" /> {{ $label }}</a>
            @endforeach
            <form action="{{ route('admin.auth.logout') }}" method="POST">
                @csrf
                <button type="submit"><x-icon name="log-out" :size="18" /> Sign out</button>
            </form>
        </div>
    </div>
    <script>
        (function () {
            var burger = document.getElementById('drip-burger');
            var mobile = document.getElementById('drip-mobile');
            if (burger && mobile) {
                burger.addEventListener('click', function () {
                    var open = mobile.classList.toggle('is-open');
                    burger.setAttribute('aria-expanded', open ? 'true' : 'false');
                });
            }
            document.addEventListener('click', function (event) {
                document.querySelectorAll('details.drip-user[open]').forEach(function (menu) {
                    if (!menu.contains(event.target)) { menu.removeAttribute('open'); }
                });
            });
        })();
    </script>
@endauth
