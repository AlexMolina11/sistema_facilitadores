@props([
    'label',
    'value',
    'description' => null,
    'icon' => null,
    'variant' => 'primary',
])

@php
    $iconClass = match($variant) {
        'success' => 'badge-success-soft',
        'warning' => 'badge-warning-soft',
        'danger' => 'badge-danger-soft',
        'info' => 'badge-info-soft',
        default => 'badge-primary-soft',
    };
@endphp

<div {{ $attributes->merge(['class' => 'stat-card h-100']) }}>
    <div class="d-flex justify-content-between align-items-start gap-3">
        <div>
            <div class="stat-label">{{ $label }}</div>
            <div class="stat-value">{{ $value }}</div>

            @if($description)
                <div class="stat-description">{{ $description }}</div>
            @endif
        </div>

        @if($icon)
            <div class="badge {{ $iconClass }} rounded-circle d-inline-flex align-items-center justify-content-center"
                 style="width: 42px; height: 42px;">
                <i class="fa-solid {{ $icon }}"></i>
            </div>
        @endif
    </div>
</div>