@props([
    'columns' => 4,
])

@php
    $class = match((int) $columns) {
        2 => 'row g-4 row-cols-1 row-cols-lg-2',
        3 => 'row g-4 row-cols-1 row-cols-md-2 row-cols-xl-3',
        default => 'row g-4 row-cols-1 row-cols-md-2 row-cols-xl-4',
    };
@endphp

<div {{ $attributes->merge(['class' => $class]) }}>
    {{ $slot }}
</div>