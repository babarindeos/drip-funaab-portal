<x-admin-layout>
    @php
        $results = session('maintenance_results', []);
        $main = array_slice($actions, 0, 2, true);
        $single = array_slice($actions, 2, null, true);
    @endphp

    <div class="drip-page">
        <div class="drip-container">

            <nav class="drip-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('admin.dashboard.index') }}">Dashboard</a>
                <x-icon name="chevron-right" :size="14" />
                <span aria-current="page">Maintenance</span>
            </nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1">Maintenance</h1>
                    <div class="drip-subtitle"><x-icon name="info" :size="20" /> <span>Clear the portal's caches without a terminal</span></div>
                </div>
            </div>

            <x-flash />

            @if (! empty($results))
                <div class="drip-card" style="margin-top:20px;">
                    <div class="drip-card__body">
                        <h2 class="drip-form-section__title">What ran</h2>
                        <ul class="drip-checklist" style="margin-top:14px;">
                            @foreach ($results as $result)
                                <li class="{{ $result['ok'] ? 'is-pass' : 'is-fail' }}">
                                    <x-icon :name="$result['ok'] ? 'circle-check' : 'circle-x'" :size="18" />
                                    <div>
                                        <code>{{ $result['command'] }}</code>
                                        @if ($result['output'] !== '')<small>{{ \Illuminate\Support\Str::limit(preg_replace('/\s+/', ' ', $result['output']), 300) }}</small>@endif
                                    </div>
                                </li>
                            @endforeach
                        </ul>
                    </div>
                </div>
            @endif

            <div class="drip-two-col">
                @foreach ($main as $key => [$label, $help, $commands])
                    <div class="drip-card">
                        <div class="drip-card__body">
                            <h2 class="drip-form-section__title">{{ $label }}</h2>
                            <p class="drip-form-section__sub">{{ $help }}</p>
                            <div class="drip-chips" style="margin-bottom:18px;">
                                @foreach ($commands as $command)<span class="drip-chip">{{ $command }}</span>@endforeach
                            </div>
                            <form method="POST" action="{{ route('admin.maintenance.run') }}">
                                @csrf
                                <input type="hidden" name="action" value="{{ $key }}">
                                <button type="submit" class="drip-btn {{ $key === 'refresh' ? 'drip-btn--primary' : 'drip-btn--outline' }}">
                                    <x-icon name="check" :size="16" /> Run
                                </button>
                            </form>
                        </div>
                    </div>
                @endforeach
            </div>

            <div class="drip-section-head">
                <div>
                    <h2 class="drip-section-title">One at a time</h2>
                    <div class="drip-section-sub">The individual commands, if you only need one.</div>
                </div>
            </div>

            <div class="drip-card">
                <ul class="drip-list" style="padding:8px 28px;">
                    @foreach ($single as $key => [$label, $help, $commands])
                        <li class="drip-list__row">
                            <div>
                                <div class="drip-list__title">{{ $label }}</div>
                                <div class="drip-list__meta">{{ $help }}</div>
                            </div>
                            <form method="POST" action="{{ route('admin.maintenance.run') }}">
                                @csrf
                                <input type="hidden" name="action" value="{{ $key }}">
                                <button type="submit" class="drip-btn drip-btn--outline drip-btn--sm">Run</button>
                            </form>
                        </li>
                    @endforeach
                </ul>
            </div>

            <p class="drip-note"><x-icon name="info" :size="16" /> <span>Clearing caches is safe: nothing in the database is touched, and with the usual file sessions nobody is signed out. If this page itself will not open after an update, delete the files in <code>bootstrap/cache/</code> (keep <code>.gitignore</code>) with your host's file manager, then come back.</span></p>
        </div>
    </div>
</x-admin-layout>
