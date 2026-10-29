@props(['user' => null, 'profile' => null, 'small' => false])
@php
    // A person's photo, or their initials when they have not uploaded one.
    $photo = optional($profile)->avatar;
    $initials = strtoupper(mb_substr(optional($user)->firstname ?? '', 0, 1).mb_substr(optional($user)->surname ?? '', 0, 1)) ?: '?';
@endphp
<div {{ $attributes->merge(['class' => 'drip-photo'.($small ? ' drip-photo--sm' : '')]) }}>
    @if (filled($photo))
        <img src="{{ asset('storage/'.$photo) }}" alt="Photo of {{ trim(optional($user)->firstname.' '.optional($user)->surname) }}">
    @else
        <span aria-hidden="true">{{ $initials }}</span>
    @endif
</div>
