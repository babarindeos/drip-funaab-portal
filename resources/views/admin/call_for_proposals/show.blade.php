<x-admin-layout>
    @php
        $call = $call_for_proposal;
        $call_status = $call->computed_status;
        $pill = match ($call_status) { 'Open' => 'drip-pill--success', 'Upcoming' => 'drip-pill--warning', default => 'drip-pill--muted' };
        $pill_icon = match ($call_status) { 'Open' => 'circle-check', 'Upcoming' => 'clock', default => 'minus' };
        $pad = fn ($n) => str_pad($n, 2, '0', STR_PAD_LEFT);
        $advert_is_image = $call->advert && preg_match('/\.(jpe?g|png)$/i', $call->advert);
    @endphp

    <div class="drip-page">
        <div class="drip-container">
            <nav class="drip-breadcrumb" aria-label="Breadcrumb">
                <a href="{{ route('admin.call_for_proposals.index') }}">Call for Proposals</a>
                <x-icon name="chevron-right" :size="14" />
                <span aria-current="page">{{ \Illuminate\Support\Str::limit($call->title, 48) }}</span>
            </nav>

            <div class="drip-page-head">
                <div>
                    <h1 class="drip-h1" style="font-size:1.875rem;">{{ $call->title }}</h1>
                    <div style="margin-top:14px;"><span class="drip-pill {{ $pill }}"><x-icon :name="$pill_icon" :size="14" />{{ $call_status }}</span></div>
                </div>
                <div class="drip-actions">
                    <a href="{{ route('admin.call_for_proposals.submissions', ['call_for_proposal' => $call->id]) }}" class="drip-btn drip-btn--primary"><x-icon name="users" :size="18" /> Applications</a>
                    <a href="{{ route('admin.call_for_proposals.results', ['call_for_proposal' => $call->id]) }}" class="drip-btn drip-btn--outline"><x-icon name="bar-chart" :size="18" /> Results</a>
                    <a href="{{ route('admin.call_for_proposals.edit', ['call_for_proposal' => $call->id]) }}" class="drip-btn drip-btn--outline"><x-icon name="pencil" :size="18" /> Edit</a>
                </div>
            </div>

            <x-flash />

            <div class="drip-stats">
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="copy" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($counts['applications']) }}</div><div class="drip-stat__label">Applications</div></div></div>
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="send" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($counts['sent_for_review']) }}</div><div class="drip-stat__label">Sent for review</div></div></div>
                <div class="drip-stat"><span class="drip-stat__icon"><x-icon name="circle-check" :size="22" /></span><div><div class="drip-stat__value">{{ $pad($counts['reviews_done']) }}</div><div class="drip-stat__label">Reviews completed</div></div></div>
            </div>

            <div class="drip-two-col">
                <div class="drip-card"><div class="drip-card__body">
                    <h2 class="drip-form-section__title">About this call</h2>
                    @if ($call->description)
                        <p style="margin:12px 0 20px; line-height:1.6; white-space:pre-line;">{{ $call->description }}</p>
                    @endif
                    <dl class="drip-dl" style="margin-top:16px;">
                        <dt>Opens</dt><dd>{{ $call->open_date->format('l, F j, Y') }}</dd>
                        <dt>Closes</dt><dd>{{ $call->close_date->format('l, F j, Y') }}</dd>
                        <dt>Status</dt><dd>{{ $call_status }}</dd>
                        <dt>Created</dt><dd>{{ optional($call->created_at)->format('F j, Y') }}</dd>
                    </dl>
                </div></div>

                <div class="drip-card"><div class="drip-card__body">
                    <h2 class="drip-form-section__title">Advert</h2>
                    @if ($call->advert)
                        @if ($advert_is_image)
                            <a href="{{ asset('storage/'.$call->advert) }}" target="_blank"><img src="{{ asset('storage/'.$call->advert) }}" alt="Advert" style="max-width:100%; margin-top:16px; border-radius:8px; border:1px solid var(--border);"></a>
                        @else
                            <p style="margin-top:16px;"><a href="{{ asset('storage/'.$call->advert) }}" target="_blank" class="drip-btn drip-btn--outline"><x-icon name="download" :size="18" /> Open the advert</a></p>
                        @endif
                    @else
                        <p class="drip-help" style="margin-top:12px;">No advert has been uploaded for this call.</p>
                    @endif
                </div></div>
            </div>
        </div>
    </div>
</x-admin-layout>
