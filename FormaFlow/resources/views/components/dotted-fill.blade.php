@props([
    'value' => null,
    'wide' => false,
    'width' => null,
])

@php
    $displayValue = is_string($value) ? trim($value) : $value;
    $blankStyle = ($width && !$wide) ? "width: {$width}; min-width: {$width};" : null;
@endphp

@if(blank($displayValue))
    <span
        class="dotted-fill{{ $wide ? ' wide' : '' }}"
        @if($blankStyle) style="{{ $blankStyle }}" @endif
    >&nbsp;</span>
@else
    <span class="filled-value">{{ $displayValue }}</span>
@endif
