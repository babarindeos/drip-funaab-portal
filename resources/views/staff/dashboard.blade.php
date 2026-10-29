<x-staff-layout>
    @php
        $pad = fn ($n) => str_pad($n, 2, '0', STR_PAD_LEFT);
        $first_name = auth()->user()->firstname ?: 'there';
        $open_total = $open_proposal_calls->count() + $open_conference_calls->count();
        $days = fn ($call) => (int) now()->startOfDay()->diffInDays($call->close_date);
        $left = fn ($call) => $days($call) === 0 ? 'Closes today' : $days($call).' '.\Illuminate\Support\Str::plural('day', $days($call)).' left';
    @endphp

    <div class="drip-page">
        <div class="drip-container">

            <nav class="drip-breadcrumb" aria-label="Breadcrumb"><span aria-current="page">Dashboard</span></nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1" style="font-size:2rem;">Welcome, {{ $first_name }}</h1>
                    <div class="drip-subtitle"><x-icon name="dashboard" :size="20" /> <span>Calls you can apply to, and your applications</span></div>
                </div>
            </div>

            <x-flash />

            <div class="drip-stats">
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="calendar" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($open_total) }}</div><div class="drip-stat__label">Calls open now</div></div></div>
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="copy" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($proposal_application_count) }}</div><div class="drip-stat__label">My proposal applications</div></div></div>
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="graduation-cap" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($conference_application_count) }}</div><div class="drip-stat__label">My conference applications</div></div></div>
            </div>

            <div class="drip-two-col">

                {{-- open calls --}}
                <div class="drip-card"><div class="drip-card__body">
                    <h2 class="drip-form-section__title">Open for applications</h2>
                    <p class="drip-form-section__sub">Calls you can apply to right now.</p>

                    <div class="drip-group-label">Call for Proposals</div>
                    <ul class="drip-list">
                        @forelse ($open_proposal_calls as $call)
                            <li class="drip-list__row">
                                <div style="min-width:0;">
                                    <a class="drip-list__title" href="{{ route('staff.call_for_proposals.application', ['uuid' => $call->uuid]) }}">{{ $call->title }}</a>
                                    <div class="drip-list__meta">Deadline {{ $call->close_date->format('M j, Y') }} &middot; {{ $left($call) }}</div>
                                </div>
                                <a href="{{ route('staff.call_for_proposals.application', ['uuid' => $call->uuid]) }}" class="drip-btn drip-btn--primary drip-btn--sm">Apply</a>
                            </li>
                        @empty
                            <li class="drip-help">No proposal calls are open right now.</li>
                        @endforelse
                    </ul>

                    <div class="drip-group-label">Conference Grants</div>
                    <ul class="drip-list">
                        @forelse ($open_conference_calls as $call)
                            <li class="drip-list__row">
                                <div style="min-width:0;">
                                    <a class="drip-list__title" href="{{ route('staff.conference.calls.show', ['call' => $call->uuid]) }}">{{ $call->title }}</a>
                                    <div class="drip-list__meta">Deadline {{ $call->close_date->format('M j, Y') }} &middot; {{ $left($call) }}</div>
                                </div>
                                <a href="{{ route('staff.conference.calls.show', ['call' => $call->uuid]) }}" class="drip-btn drip-btn--primary drip-btn--sm">Details</a>
                            </li>
                        @empty
                            <li class="drip-help">{{ $conference_available ? 'No conference grants are open right now.' : 'Not available right now.' }}</li>
                        @endforelse
                    </ul>

                    <div style="display:flex; flex-wrap:wrap; gap:10px; margin-top:22px;">
                        <a href="{{ route('staff.call_for_proposals.index') }}" class="drip-btn drip-btn--outline drip-btn--sm">All proposal calls</a>
                        <a href="{{ route('staff.conference.calls.index') }}" class="drip-btn drip-btn--outline drip-btn--sm">All conference grants</a>
                    </div>
                </div></div>

                {{-- my applications --}}
                <div class="drip-card"><div class="drip-card__body">
                    <h2 class="drip-form-section__title">My applications</h2>
                    <p class="drip-form-section__sub">Where each of your applications stands.</p>

                    <div class="drip-group-label">Call for Proposals</div>
                    <ul class="drip-list">
                        @forelse ($my_proposal_applications as $application)
                            <li class="drip-list__row">
                                <div style="min-width:0;">
                                    <a class="drip-list__title" href="{{ route('staff.call_for_proposals.application', ['uuid' => $application->call_for_proposal->uuid]) }}">{{ $application->proposal_title }}</a>
                                    <div class="drip-list__meta">{{ \Illuminate\Support\Str::limit($application->call_for_proposal->title, 42) }} &middot; submitted {{ optional($application->created_at)->format('M j, Y') }}</div>
                                </div>
                                <x-status-badge :status="$application->status" />
                            </li>
                        @empty
                            <li class="drip-help">You have not applied to a call for proposals yet.</li>
                        @endforelse
                    </ul>

                    <div class="drip-group-label">Conference Grants</div>
                    <ul class="drip-list">
                        @forelse ($my_conference_applications as $application)
                            @php
                                $target = $application->isEditable() && optional($application->call)->isOpen()
                                    ? route('staff.conference.applications.edit', ['application' => $application->uuid])
                                    : route('staff.conference.applications.show', ['application' => $application->uuid]);
                            @endphp
                            <li class="drip-list__row">
                                <div style="min-width:0;">
                                    <a class="drip-list__title" href="{{ $target }}">{{ $application->conference_title ?: 'Application in progress' }}</a>
                                    <div class="drip-list__meta">{{ \Illuminate\Support\Str::limit(optional($application->call)->title, 42) }}</div>
                                </div>
                                <x-status-badge :status="$application->status" />
                            </li>
                        @empty
                            <li class="drip-help">{{ $conference_available ? 'You have not applied for a conference grant yet.' : 'Not available right now.' }}</li>
                        @endforelse
                    </ul>
                </div></div>
            </div>
        </div>
    </div>
</x-staff-layout>
