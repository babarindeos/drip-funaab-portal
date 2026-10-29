@props([
    'name', 'label', 'type' => 'text', 'value' => null, 'required' => false, 'help' => null,
    'options' => null, 'full' => false, 'placeholder' => null, 'rows' => 4, 'id' => null,
])
@php
    // One labelled form control in the DRIP style: label, input / select / textarea, help text and error.
    $id = $id ?? $name;
    $current = old($name, $value);
@endphp
<div class="{{ $full ? 'is-full' : '' }}">
    <label class="drip-label" for="{{ $id }}">{{ $label }}@if ($required) <span class="req">*</span>@endif</label>

    @if (! is_null($options))
        <select id="{{ $id }}" name="{{ $name }}" {{ $attributes->merge(['class' => 'drip-control'.($errors->has($name) ? ' is-invalid' : '')]) }}>
            <option value="">Select...</option>
            @foreach ($options as $option_value => $option_label)
                <option value="{{ $option_value }}" @selected((string) $current === (string) $option_value)>{{ $option_label }}</option>
            @endforeach
        </select>
    @elseif ($type === 'textarea')
        <textarea id="{{ $id }}" name="{{ $name }}" rows="{{ $rows }}" placeholder="{{ $placeholder }}" {{ $attributes->merge(['class' => 'drip-control'.($errors->has($name) ? ' is-invalid' : '')]) }}>{{ $current }}</textarea>
    @else
        <input id="{{ $id }}" name="{{ $name }}" type="{{ $type }}" value="{{ $current }}" placeholder="{{ $placeholder }}" {{ $attributes->merge(['class' => 'drip-control'.($errors->has($name) ? ' is-invalid' : '')]) }}>
    @endif

    @if ($help)<div class="drip-help">{{ $help }}</div>@endif
    @error($name)<div class="drip-error">{{ $message }}</div>@enderror
</div>
