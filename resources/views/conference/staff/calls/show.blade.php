<x-staff-layout>
    @php
        $call_status = $call->computed_status;
        $pill = match ($call_status) { 'Open' => 'drip-pill--success', 'Upcoming' => 'drip-pill--warning', default => 'drip-pill--muted' };
        $pill_icon = match ($call_status) { 'Open' => 'circle-check', 'Upcoming' => 'clock', default => 'minus' };
        $documents = config('conference.document_types');
        $who = ['all' => 'Everyone', 'teaching' => 'Teaching staff only', 'previous_beneficiary' => 'If you have been sponsored by TETFund before', 'confirmed' => 'Confirmed staff'];
    @endphp

    <div class="drip-page">
        <div class="drip-container">

            <nav class="drip-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('staff.conference.calls.index') }}">Conference Grants</a>
                <x-icon name="chevron-right" :size="14" />
                <span aria-current="page">{{ \Illuminate\Support\Str::limit($call->title, 48) }}</span>
            </nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1" style="font-size:1.875rem;">{{ $call->title }}</h1>
                    <div style="margin-top:14px;"><span class="drip-pill {{ $pill }}"><x-icon :name="$pill_icon" :size="14" />{{ $call_status }}</span></div>
                </div>

                <div class="drip-actions">
                    @if ($mine && $mine->isEditable() && $call->isOpen())
                        <a href="{{ route('staff.conference.applications.edit', ['application' => $mine->uuid]) }}" class="drip-btn drip-btn--primary"><x-icon name="pencil" :size="18" /> Continue your application</a>
                    @elseif ($mine)
                        <a href="{{ route('staff.conference.applications.show', ['application' => $mine->uuid]) }}" class="drip-btn drip-btn--primary"><x-icon name="eye" :size="18" /> View your application</a>
                    @elseif (! $mine && $call->isOpen())
                        <form method="POST" action="{{ route('staff.conference.calls.apply', ['call' => $call->uuid]) }}">
                            @csrf
                            <button type="submit" class="drip-btn drip-btn--primary"><x-icon name="plus" :size="18" /> Apply now</button>
                        </form>
                    @endif
                </div>
            </div>

            @if (session('success'))
                <div class="drip-alert drip-alert--success"><x-icon name="circle-check" :size="18" /> <span>{{ session('success') }}</span></div>
            @endif
            @if (session('error'))
                <div class="drip-alert drip-alert--error"><x-icon name="circle-x" :size="18" /> <span>{{ session('error') }}</span></div>
            @endif

            @if ($mine)
                <div class="drip-alert drip-alert--success" style="background:var(--muted); color:var(--foreground); border-color:var(--border);">
                    <x-icon name="info" :size="18" />
                    <span>Your application for this call: <x-status-badge :status="$mine->status" /></span>
                </div>
            @elseif (! $call->isOpen())
                <div class="drip-alert drip-alert--error" style="background:var(--muted); color:var(--foreground); border-color:var(--border);">
                    <x-icon name="info" :size="18" />
                    <span>{{ $call_status === 'Upcoming' ? 'This call opens on '.$call->open_date->format('F j, Y').'.' : 'This call is closed for applications.' }}</span>
                </div>
            @endif

            <div class="drip-two-col">
                <div class="drip-card">
                    <div class="drip-card__body">
                        <h2 class="drip-form-section__title">About this call</h2>
                        @if ($call->description)
                            <p style="margin:12px 0 20px; line-height:1.6; white-space:pre-line;">{{ $call->description }}</p>
                        @endif

                        <dl class="drip-dl" style="margin-top:16px;">
                            <dt>Opens</dt><dd>{{ $call->open_date->format('l, F j, Y') }}</dd>
                            <dt>Application deadline</dt><dd>{{ $call->close_date->format('l, F j, Y') }}</dd>
                            <dt>Earliest eligible conference date</dt><dd>{{ $call->earliest_conference_date->format('F j, Y') }}</dd>
                            <dt>Maximum duration</dt><dd>{{ $call->max_duration_days }} {{ \Illuminate\Support\Str::plural('day', $call->max_duration_days) }}</dd>
                            <dt>Who can apply</dt>
                            <dd>
                                <div class="drip-chips">
                                    @if ($call->allow_teaching)<span class="drip-chip">Teaching staff (conferences)</span>@endif
                                    @if ($call->allow_non_teaching)<span class="drip-chip">Non-teaching staff (workshops)</span>@endif
                                </div>
                            </dd>
                        </dl>

                        @if ($call->advert)
                            <p style="margin-top:20px;"><a href="{{ asset('storage/'.$call->advert) }}" target="_blank" class="drip-clear">View the advert / circular</a></p>
                        @endif
                    </div>
                </div>

                <div class="drip-stack">
                    <div class="drip-card">
                        <div class="drip-card__body">
                            <h2 class="drip-form-section__title">Rules</h2>
                            <p class="drip-form-section__sub">Your application is checked against these as you fill it in.</p>
                            <ul class="drip-checklist">
                                <li class="is-pass"><x-icon name="check" :size="18" /> <span>You must be a confirmed member of staff.</span></li>
                                <li class="is-pass"><x-icon name="check" :size="18" /> <span>You must have served FUNAAB for more than {{ config('conference.min_service_years', 3) - 1 }} {{ config('conference.min_service_years', 3) - 1 === 1 ? 'year' : 'years' }}.</span></li>
                                <li class="is-pass"><x-icon name="check" :size="18" /> <span>The event must be held in person (not online or virtual).</span></li>
                                <li class="is-pass"><x-icon name="check" :size="18" /> <span>It must start on or after {{ $call->earliest_conference_date->format('F j, Y') }}.</span></li>
                                <li class="is-pass"><x-icon name="check" :size="18" /> <span>It must last no more than {{ $call->max_duration_days }} {{ \Illuminate\Support\Str::plural('day', $call->max_duration_days) }}.</span></li>
                                <li class="is-pass"><x-icon name="check" :size="18" /> <span>Papers must be presented orally (no poster presentations).</span></li>
                                <li class="is-pass"><x-icon name="check" :size="18" /> <span>First-time beneficiaries are given preference. If TETFund has sponsored you for an international conference before, you must show evidence of one Q1 or two Q2 articles.</span></li>
                            </ul>
                        </div>
                    </div>

                    <div class="drip-card">
                        <div class="drip-card__body">
                            <h2 class="drip-form-section__title">What you will need</h2>
                            <p class="drip-form-section__sub">Have these ready before you start.</p>
                            <ul class="drip-checklist">
                                @foreach ($documents as $type => $definition)
                                    <li class="is-pending"><x-icon name="file-text" :size="18" /> <span>{{ $definition['label'] }}<small>{{ $who[$definition['required']] ?? '' }}</small></span></li>
                                @endforeach
                                <li class="is-pending"><x-icon name="user" :size="18" /> <span>A passport photograph<small>Everyone</small></span></li>
                                <li class="is-pending"><x-icon name="lock" :size="18" /> <span>Your salary account details<small>Stored encrypted</small></span></li>
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

        </div>
    </div>
</x-staff-layout>
