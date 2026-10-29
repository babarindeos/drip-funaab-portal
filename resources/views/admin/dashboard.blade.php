<x-admin-layout>
    @php
        $pad = fn ($n) => str_pad($n, 2, '0', STR_PAD_LEFT);
        $open_calls = $proposals['open'] + ($conference['available'] ? $conference['open'] : 0);
        $awaiting = $proposals['awaiting'] + ($conference['available'] ? $conference['awaiting'] : 0);
        $first_name = auth()->user()->firstname ?: 'Administrator';
    @endphp

    <div class="drip-page">
        <div class="drip-container">

            <nav class="drip-breadcrumb" aria-label="Breadcrumb"><span aria-current="page">Dashboard</span></nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1" style="font-size:2rem;">Welcome back, {{ $first_name }}</h1>
                    <div class="drip-subtitle"><x-icon name="dashboard" :size="20" /> <span>What is open, and what is waiting for you</span></div>
                </div>
            </div>

            <x-flash />

            <div class="drip-stats drip-stats--4">
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="calendar" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($open_calls) }}</div><div class="drip-stat__label">Calls open now</div></div></div>
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="clock" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($awaiting) }}</div><div class="drip-stat__label">Awaiting your decision</div></div></div>
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="send" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($proposals['reviews_outstanding']) }}</div><div class="drip-stat__label">Reviews outstanding</div></div></div>
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="users" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($proposals['reviewers']) }}</div><div class="drip-stat__label">Reviewers</div></div></div>
            </div>

            {{-- the two modules --}}
            <div class="drip-two-col">
                <div class="drip-card"><div class="drip-card__body">
                    <h2 class="drip-form-section__title">Call for Proposals</h2>
                    <p class="drip-form-section__sub">Research proposals, reviewed and scored by external reviewers.</p>
                    <dl class="drip-dl" style="grid-template-columns: 1fr auto; gap:10px 16px;">
                        <dt>Calls</dt><dd style="text-align:right;"><strong>{{ $proposals['calls'] }}</strong> <span class="drip-app-meta" style="display:inline;">({{ $proposals['open'] }} open)</span></dd>
                        <dt>Applications</dt><dd style="text-align:right;"><strong>{{ $proposals['applications'] }}</strong></dd>
                        <dt>Awaiting a decision</dt><dd style="text-align:right;"><strong>{{ $proposals['awaiting'] }}</strong></dd>
                    </dl>
                    <div style="display:flex; flex-wrap:wrap; gap:10px; margin-top:20px;">
                        <a href="{{ route('admin.call_for_proposals.index') }}" class="drip-btn drip-btn--primary drip-btn--sm"><x-icon name="copy" :size="15" /> Calls</a>
                        <a href="{{ route('admin.reviewers.index') }}" class="drip-btn drip-btn--outline drip-btn--sm"><x-icon name="users" :size="15" /> Reviewers</a>
                        <a href="{{ route('admin.analytics.index') }}" class="drip-btn drip-btn--outline drip-btn--sm"><x-icon name="bar-chart" :size="15" /> Analytics</a>
                    </div>
                </div></div>

                <div class="drip-card"><div class="drip-card__body">
                    <h2 class="drip-form-section__title">Conference Grants</h2>
                    <p class="drip-form-section__sub">Conference and workshop attendance grants, decided by the committee.</p>
                    @if ($conference['available'])
                        <dl class="drip-dl" style="grid-template-columns: 1fr auto; gap:10px 16px;">
                            <dt>Calls</dt><dd style="text-align:right;"><strong>{{ $conference['calls'] }}</strong> <span class="drip-app-meta" style="display:inline;">({{ $conference['open'] }} open)</span></dd>
                            <dt>Applications submitted</dt><dd style="text-align:right;"><strong>{{ $conference['submitted'] }}</strong></dd>
                            <dt>Awaiting a decision</dt><dd style="text-align:right;"><strong>{{ $conference['awaiting'] }}</strong></dd>
                        </dl>
                        <div style="display:flex; flex-wrap:wrap; gap:10px; margin-top:20px;">
                            <a href="{{ route('admin.conference.calls.index') }}" class="drip-btn drip-btn--primary drip-btn--sm"><x-icon name="graduation-cap" :size="15" /> Calls</a>
                            <a href="{{ route('admin.conference.analytics') }}" class="drip-btn drip-btn--outline drip-btn--sm"><x-icon name="bar-chart" :size="15" /> Analytics</a>
                        </div>
                    @else
                        <div class="drip-callout">The conference figures are not available right now. Check that the conference database is set up (<code>php artisan conference:check</code>).</div>
                    @endif
                </div></div>
            </div>

            {{-- what needs attention --}}
            <div class="drip-section-head">
                <div>
                    <h2 class="drip-section-title">Waiting for a decision</h2>
                    <div class="drip-section-sub">The oldest first, so nothing is left waiting</div>
                </div>
            </div>

            <div class="drip-two-col" style="margin-top:0;">
                <div class="drip-card"><div class="drip-card__body">
                    <h3 class="drip-form-section__title" style="font-size:1rem;">Proposals</h3>
                    <ul class="drip-list" style="margin-top:14px;">
                        @forelse ($recent_proposals as $application)
                            <li class="drip-list__row">
                                <div style="min-width:0;">
                                    <a class="drip-list__title" href="{{ route('admin.call_for_proposals.proposal_application', ['call_for_proposal' => $application->call_for_proposal_id, 'proposal_application' => $application->id]) }}">{{ $application->proposal_title }}</a>
                                    <div class="drip-list__meta">{{ $application->principal_investigator }} &middot; submitted {{ optional($application->created_at)->format('M j, Y') }}</div>
                                </div>
                                <x-status-badge :status="$application->status" />
                            </li>
                        @empty
                            <li class="drip-help">Nothing is waiting. All proposals have been decided.</li>
                        @endforelse
                    </ul>
                </div></div>

                <div class="drip-card"><div class="drip-card__body">
                    <h3 class="drip-form-section__title" style="font-size:1rem;">Conference applications</h3>
                    <ul class="drip-list" style="margin-top:14px;">
                        @forelse ($recent_conference as $application)
                            <li class="drip-list__row">
                                <div style="min-width:0;">
                                    <a class="drip-list__title" href="{{ route('admin.conference.applications.show', ['application' => $application->uuid]) }}">{{ $application->candidate_name ?: 'Unnamed applicant' }}</a>
                                    <div class="drip-list__meta">{{ \Illuminate\Support\Str::limit(optional($application->call)->title, 40) }} &middot; submitted {{ optional($application->submitted_at)->format('M j, Y') }}</div>
                                </div>
                                @if ($application->previously_sponsored)<span class="drip-pill drip-pill--warning">Previous</span>@else<span class="drip-pill drip-pill--success">First-time</span>@endif
                            </li>
                        @empty
                            <li class="drip-help">{{ $conference['available'] ? 'Nothing is waiting. All applications have been decided.' : 'Not available right now.' }}</li>
                        @endforelse
                    </ul>
                </div></div>
            </div>

        </div>
    </div>
</x-admin-layout>
