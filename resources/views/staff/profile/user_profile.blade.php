<x-staff-layout>
    @php
        $full_name = $person ? trim($person->surname.' '.$person->firstname.' '.$person->middlename) : null;
    @endphp

    <div class="drip-page">
        <div class="drip-container">

            <nav class="drip-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('staff.dashboard.index') }}">Dashboard</a>
                <x-icon name="chevron-right" :size="14" />
                <span aria-current="page">Staff profile</span>
            </nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1">{{ $full_name ?: 'Staff profile' }}</h1>
                    @if ($profile)
                        <div class="drip-subtitle"><x-icon name="briefcase" :size="20" /> <span>{{ $profile->designation }}</span></div>
                    @endif
                </div>
                <div class="drip-actions">
                    <a href="{{ url()->previous() }}" class="drip-btn drip-btn--outline"><x-icon name="arrow-left" :size="16" /> Back</a>
                </div>
            </div>

            @if (! $person)
                <div class="drip-card" style="margin-top:28px;">
                    <div class="drip-empty">
                        <strong>This person could not be found</strong>
                        The link may be out of date, or the account may have been removed.
                    </div>
                </div>
            @elseif (! $profile)
                <div class="drip-card" style="margin-top:28px;">
                    <div class="drip-empty">
                        <strong>{{ $full_name }} has not completed their profile yet</strong>
                        You can still reach them at <a href="mailto:{{ $person->email }}" class="drip-link">{{ $person->email }}</a>.
                    </div>
                </div>
            @else
                <div class="drip-profile-grid">
                    <div class="drip-card drip-profile-card">
                        <div class="drip-card__body">
                            <x-profile-photo :user="$person" :profile="$profile" />
                            <div class="drip-profile-card__name">{{ $full_name }}</div>
                            <div class="drip-profile-card__sub">{{ $profile->designation }}</div>
                            <a href="mailto:{{ $person->email }}" class="drip-btn drip-btn--outline drip-btn--sm drip-btn--block" style="margin-top:20px;"><x-icon name="mail" :size="15" /> Send an email</a>
                        </div>
                    </div>

                    <div class="drip-card">
                        <div class="drip-card__body">
                            <h2 class="drip-form-section__title" style="margin-bottom:20px;">Contact and office</h2>
                            <dl class="drip-dl">
                                <dt>Email address</dt>
                                <dd><a href="mailto:{{ $person->email }}" class="drip-link">{{ $person->email }}</a></dd>

                                <dt>Phone number</dt>
                                <dd>{{ $profile->phone ?: '—' }}</dd>

                                @if ($staff && filled($staff->fileno))
                                    <dt>File number</dt>
                                    <dd>{{ $staff->fileno }}</dd>
                                @endif

                                @if ($organ)
                                    <dt>Office</dt>
                                    <dd>{{ $organ->name }}@if (filled($organ->code ?? null)) ({{ $organ->code }})@endif</dd>
                                @endif

                                @if ($staff && optional($staff->segment)->name)
                                    <dt>Level</dt>
                                    <dd>{{ $staff->segment->name }}</dd>
                                @endif
                            </dl>
                        </div>
                    </div>
                </div>
            @endif

        </div>
    </div>
</x-staff-layout>
