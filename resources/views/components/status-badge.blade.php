@props(['status' => 'pending'])
@php
    // One look for application decisions across the whole portal (styles live in public/css/drip-theme.css).
    $key = strtolower($status ?: 'pending');
    $map = [
        'pending'      => ['drip-pill--warning', 'clock', 'Pending'],
        'acknowledged' => ['drip-pill--success', 'check', 'Acknowledged'],
        'accepted'     => ['drip-pill--solid', 'circle-check', 'Accepted'],
        'rejected'     => ['drip-pill--danger', 'circle-x', 'Rejected'],
        // conference grant applications
        'draft'        => ['drip-pill--muted', 'pencil', 'Draft'],
        'submitted'    => ['drip-pill--warning', 'clock', 'Submitted'],
        'approved'     => ['drip-pill--solid', 'circle-check', 'Approved'],
        'not_approved' => ['drip-pill--danger', 'circle-x', 'Not approved'],
    ];
    [$class, $icon, $label] = $map[$key] ?? ['drip-pill--muted', 'minus', ucfirst($key)];
@endphp
<span class="drip-pill {{ $class }}"><x-icon :name="$icon" :size="14" />{{ $label }}</span>
