<form method="POST" action="{{ $action }}" class="fepade-card">
    @csrf

    @if($method !== 'POST')
        @method($method)
    @endif

    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Código</label>
            <input 
                type="text" 
                name="codigo" 
                value="{{ old('codigo', $plantilla->codigo) }}" 
                class="form-control @error('codigo') is-invalid @enderror"
                placeholder="profesional"
                required
            >
            @error('codigo')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
            <small class="text-muted">
                Se usará para resolver la vista: <code>fac.cv.pdf.codigo</code>
            </small>
        </div>

        <div class="col-md-8">
            <label class="form-label">Nombre</label>
            <input 
                type="text" 
                name="nombre" 
                value="{{ old('nombre', $plantilla->nombre) }}" 
                class="form-control @error('nombre') is-invalid @enderror"
                required
            >
            @error('nombre')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-12">
            <label class="form-label">Descripción</label>
            <textarea 
                name="descripcion" 
                rows="3" 
                class="form-control @error('descripcion') is-invalid @enderror"
            >{{ old('descripcion', $plantilla->descripcion) }}</textarea>
            @error('descripcion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">Tamaño de papel</label>
            <select name="tamanio_papel" class="form-select @error('tamanio_papel') is-invalid @enderror">
                <option value="letter" @selected(old('tamanio_papel', $plantilla->tamanio_papel) === 'letter')>Carta</option>
                <option value="a4" @selected(old('tamanio_papel', $plantilla->tamanio_papel) === 'a4')>A4</option>
                <option value="legal" @selected(old('tamanio_papel', $plantilla->tamanio_papel) === 'legal')>Legal</option>
            </select>
            @error('tamanio_papel')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">Orientación</label>
            <select name="orientacion" class="form-select @error('orientacion') is-invalid @enderror">
                <option value="portrait" @selected(old('orientacion', $plantilla->orientacion) === 'portrait')>Vertical</option>
                <option value="landscape" @selected(old('orientacion', $plantilla->orientacion) === 'landscape')>Horizontal</option>
            </select>
            @error('orientacion')
                <div class="invalid-feedback">{{ $message }}</div>
            @enderror
        </div>

        <div class="col-md-4">
            <label class="form-label">Orden</label>
            <input 
                type="number" 
                name="orden" 
                value="{{ old('orden', $plantilla->orden ?? 1) }}" 
                class="form-control"
                @if(! $plantilla->exists) readonly @endif
            >
            <small class="text-muted">
                @if(! $plantilla->exists)
                    Se asigna automáticamente al crear la plantilla.
                @else
                    Puede ajustarse manualmente.
                @endif
            </small>
        </div>

        <div class="col-12">
            <div class="form-check">
                <input 
                    class="form-check-input" 
                    type="checkbox" 
                    name="activa" 
                    value="1" 
                    id="activa"
                    @checked(old('activa', $plantilla->activa ?? true))
                >
                <label class="form-check-label" for="activa">
                    Plantilla activa
                </label>
            </div>
        </div>

        <div class="col-12">
            <div class="alert alert-info mb-0">
                <strong>Vista generada automáticamente:</strong>
                <code id="vistaBladePreview">fac.cv.pdf.{{ old('codigo', $plantilla->codigo ?: 'codigo') }}</code>
            </div>
        </div>
    </div>

    <div class="fepade-form-actions">
        <a href="{{ route('fac.catalogos.cv-plantillas.index') }}" class="btn btn-outline-secondary">
            Cancelar
        </a>

        <button type="submit" class="btn btn-fepade">
            Guardar plantilla
        </button>
    </div>
</form>

<script>
    document.addEventListener('DOMContentLoaded', function () {
        const input = document.querySelector('input[name="codigo"]');
        const preview = document.getElementById('vistaBladePreview');

        function slugCode(value) {
            return value
                .toLowerCase()
                .normalize('NFD').replace(/[\u0300-\u036f]/g, '')
                .replace(/[^a-z0-9]+/g, '_')
                .replace(/^_+|_+$/g, '');
        }

        input.addEventListener('input', function () {
            preview.textContent = 'fac.cv.pdf.' + (slugCode(input.value) || 'codigo');
        });
    });
</script>