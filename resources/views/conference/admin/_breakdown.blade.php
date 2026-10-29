{{-- A card of horizontal bars: how the submitted applications split across a few groups. --}}
<div class="drip-card">
    <div class="drip-card__body">
        <h2 class="drip-form-section__title">{{ $title }}</h2>
        <p class="drip-form-section__sub">Submitted applications, and how many of them were approved.</p>

        @forelse ($rows as $row)
            @php $pct = $total > 0 ? (int) round($row['submitted'] / $total * 100) : 0; @endphp
            <div class="drip-bar-row">
                <div class="drip-bar-row__head">
                    <span>{{ $row['label'] }}</span>
                    <span>{{ $row['submitted'] }} submitted &middot; {{ $row['approved'] }} approved &middot; {{ $pct }}%</span>
                </div>
                <div class="drip-progress"><span style="width: {{ $pct }}%;"></span></div>
            </div>
        @empty
            <p class="drip-help">Nothing to show yet.</p>
        @endforelse
    </div>
</div>
