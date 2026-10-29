{{-- Breadcrumb, title and tabs shared by the "My profile" pages. Needs $active and $title. --}}
@php
    $tabs = [
        'profile' => ['My profile', 'staff.profile.myprofile', 'user'],
        'edit' => ['Edit details', 'staff.profile.myprofile.edit', 'pencil'],
        'password' => ['Change password', 'staff.profile.change_password', 'key'],
    ];
@endphp

<nav class="drip-breadcrumb" aria-label="Breadcrumb">
    <a href="{{ route('staff.dashboard.index') }}">Dashboard</a>
    <x-icon name="chevron-right" :size="14" />
    @if ($active === 'profile')
        <span aria-current="page">My profile</span>
    @else
        <a href="{{ route('staff.profile.myprofile') }}">My profile</a>
        <x-icon name="chevron-right" :size="14" />
        <span aria-current="page">{{ $title }}</span>
    @endif
</nav>

<div class="drip-page-head">
    <div>
        <h1 class="drip-h1">{{ $title }}</h1>
        @isset($subtitle)
            <div class="drip-subtitle"><x-icon name="user" :size="20" /> <span>{{ $subtitle }}</span></div>
        @endisset
    </div>
</div>

<nav class="drip-tabs" aria-label="Profile sections">
    @foreach ($tabs as $key => [$label, $route_name, $icon])
        <a href="{{ route($route_name) }}" class="{{ $active === $key ? 'is-active' : '' }}" @if ($active === $key) aria-current="page" @endif>
            <x-icon :name="$icon" :size="16" /> {{ $label }}
        </a>
    @endforeach
</nav>

<x-flash />
