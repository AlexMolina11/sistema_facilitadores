@props([
    'name',
    'value',
    'checked' => false,
    'title',
    'code' => null,
    'description' => null,
])

<label {{ $attributes->merge(['class' => 'form-check']) }}>
    <input
        class="form-check-input"
        type="checkbox"
        name="{{ $name }}"
        value="{{ $value }}"
        @checked($checked)
    >

    <span class="form-check-label">
        <strong>{{ $title }}</strong>

        @if($code)
            <div class="mt-1">
                <code class="fepade-code">{{ $code }}</code>
            </div>
        @endif

        @if($description)
            <div class="text-muted small mt-1">
                {{ $description }}
            </div>
        @endif
    </span>
</label>