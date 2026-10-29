@props(['call', 'type' => 'ibr'])
@php
    // One call on the public pages. $type says which kind it is:
    //   'ibr'        a Call for IBR Proposals          (App\Models\CallForProposal)
    //   'conference' a Call for Conference/Workshop     (App\Models\Conference\ConferenceCall)
    $is_conference = $type === 'conference';
    $category = $is_conference ? 'Conference/Workshop' : 'IBR Proposal';
    $category_icon = $is_conference ? 'graduation-cap' : 'book-open';

    $status = $call->computed_status;
    $pill = match ($status) {
        'Open' => ['drip-pill--success', 'circle-check'],
        'Upcoming' => ['drip-pill--warning', 'clock'],
        default => ['drip-pill--muted', 'lock'],
    };
    $days = $status === 'Open' ? (int) now()->startOfDay()->diffInDays($call->close_date) : null;

    // where a signed-in staff member goes to apply
    $apply_url = $is_conference
        ? route('staff.conference.calls.show', ['call' => $call->uuid])
        : route('staff.call_for_proposals.application', ['uuid' => $call->uuid]);

    $who = null;
    if ($is_conference) {
        $who = collect([$call->allow_teaching ? 'teaching staff' : null, $call->allow_non_teaching ? 'non-teaching staff' : null])->filter()->implode(' and ');
    }
@endphp
<article class="drip-call">
    <div class="drip-call__type drip-call__type--{{ $is_conference ? 'conference' : 'ibr' }}"><x-icon :name="$category_icon" :size="14" /> {{ $category }}</div>

    <div class="drip-call__head">
        <h3 class="drip-call__title">{{ $call->title }}</h3>
        <span class="drip-pill {{ $pill[0] }}"><x-icon :name="$pill[1]" :size="14" />{{ $status }}</span>
    </div>

    @if (filled($call->description))
        <p class="drip-call__desc">{{ \Illuminate\Support\Str::limit($call->description, 420) }}</p>
    @endif

    <div class="drip-call__meta">
        <span><x-icon name="calendar" :size="15" /> Opens <strong>{{ $call->open_date->format('M j, Y') }}</strong></span>
        <span><x-icon name="clock" :size="15" /> Closes <strong>{{ $call->close_date->format('M j, Y') }}</strong></span>
        @if (! is_null($days))
            <span><strong>{{ $days === 0 ? 'Closes today' : $days.' '.\Illuminate\Support\Str::plural('day', $days).' left' }}</strong></span>
        @endif
    </div>

    @if ($is_conference)
        <div class="drip-call__meta" style="margin-top:8px;">
            @if ($who)<span><x-icon name="users" :size="15" /> Open to <strong>{{ $who }}</strong></span>@endif
            @if ($call->earliest_conference_date)<span><x-icon name="calendar" :size="15" /> Events from <strong>{{ $call->earliest_conference_date->format('M j, Y') }}</strong></span>@endif
            @if ($call->max_duration_days)<span>Up to <strong>{{ $call->max_duration_days }} {{ \Illuminate\Support\Str::plural('day', $call->max_duration_days) }}</strong></span>@endif
        </div>
    @endif

    <div class="drip-call__foot">
        <div>
            @if ($call->advert)
                <a href="{{ asset('storage/'.$call->advert) }}" target="_blank" rel="noopener" class="drip-doc-link"><x-icon name="file-text" :size="15" /> View advert</a>
            @endif
        </div>
        <div>
            @auth
                @if (auth()->user()->role !== 'admin' && $status === 'Open')
                    <a href="{{ $apply_url }}" class="drip-btn drip-btn--primary drip-btn--sm"><x-icon name="plus" :size="15" /> Apply</a>
                @endif
            @else
                @if ($status === 'Open')
                    <a href="{{ route('welcome') }}#signin" class="drip-btn drip-btn--primary drip-btn--sm"><x-icon name="log-in" :size="15" /> Sign in to apply</a>
                @elseif ($status === 'Upcoming')
                    <span class="drip-muted" style="font-size:.875rem;">Opens {{ $call->open_date->format('F j, Y') }}</span>
                @else
                    <span class="drip-muted" style="font-size:.875rem;">Applications have closed</span>
                @endif
            @endauth
        </div>
    </div>
</article>
