<div class="contacto-card item-experiencia">
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Empresa</label>
            <input 
                type="text" 
                name="experiencias[{{ $index }}][empresa]" 
                value="{{ old("experiencias.$index.empresa", $experiencia->empresa ?? '') }}" 
                class="form-control"
                maxlength="150"
            >
        </div>

        <div class="col-md-4">
            <label class="form-label">Cargo</label>
            <input 
                type="text" 
                name="experiencias[{{ $index }}][cargo]" 
                value="{{ old("experiencias.$index.cargo", $experiencia->cargo ?? '') }}" 
                class="form-control"
                maxlength="100"
            >
        </div>

        <div class="col-md-4 d-flex align-items-end">
            <div class="form-check form-switch mb-2">
                <input 
                    class="form-check-input" 
                    type="checkbox" 
                    name="experiencias[{{ $index }}][trabajo_actual]" 
                    value="1"
                    id="trabajo_actual_{{ $index }}"
                    {{ old("experiencias.$index.trabajo_actual", $experiencia->trabajo_actual ?? false) ? 'checked' : '' }}
                >
                <label class="form-check-label" for="trabajo_actual_{{ $index }}">
                    Trabajo actual
                </label>
            </div>
        </div>

        <div class="col-md-3">
            <label class="form-label">Fecha desde</label>
            <input 
                type="date" 
                name="experiencias[{{ $index }}][desde]" 
                value="{{ old("experiencias.$index.desde", isset($experiencia) && $experiencia?->desde ? $experiencia->desde->format('Y-m-d') : '') }}" 
                class="form-control"
            >
        </div>

        <div class="col-md-3">
            <label class="form-label">Fecha hasta</label>
            <input 
                type="date" 
                name="experiencias[{{ $index }}][hasta]" 
                value="{{ old("experiencias.$index.hasta", isset($experiencia) && $experiencia?->hasta ? $experiencia->hasta->format('Y-m-d') : '') }}" 
                class="form-control"
            >
        </div>

        <div class="col-md-6">
            <label class="form-label">Evidencia laboral</label>
            <input 
                type="file" 
                name="experiencias[{{ $index }}][evidencia]" 
                class="form-control"
                accept=".pdf,.jpg,.jpeg,.png,.webp"
            >
            <div class="form-text">PDF, JPG, JPEG, PNG o WEBP. Máximo 5 MB.</div>

            @if(isset($experiencia) && $experiencia?->url_evidencia)
                <div class="mt-2">
                    <a 
                        href="{{ \Illuminate\Support\Facades\Storage::url($experiencia->url_evidencia) }}" 
                        target="_blank"
                        class="formacion-document-link"
                    >
                        Ver evidencia actual
                    </a>
                </div>
            @endif
        </div>

        <div class="col-12">
            <label class="form-label">Descripción de funciones o experiencia</label>
            <textarea 
                name="experiencias[{{ $index }}][descripcion]" 
                rows="3" 
                class="form-control"
                maxlength="500"
            >{{ old("experiencias.$index.descripcion", $experiencia->descripcion ?? '') }}</textarea>
        </div>

        <div class="col-md-4">
            <label class="form-label">Jefe inmediato</label>
            <input 
                type="text" 
                name="experiencias[{{ $index }}][jefe_nombre]" 
                value="{{ old("experiencias.$index.jefe_nombre", $experiencia->jefe_nombre ?? '') }}" 
                class="form-control"
                maxlength="150"
            >
        </div>

        <div class="col-md-4">
            <label class="form-label">Correo del jefe inmediato</label>
            <input 
                type="email" 
                name="experiencias[{{ $index }}][jefe_email]" 
                value="{{ old("experiencias.$index.jefe_email", $experiencia->jefe_email ?? '') }}" 
                class="form-control"
                placeholder="nombre@dominio.com"
                maxlength="120"
            >
        </div>

        <div class="col-md-3">
            <label class="form-label">Teléfono del jefe inmediato</label>
            <input 
                type="text" 
                name="experiencias[{{ $index }}][jefe_telefono]" 
                value="{{ old("experiencias.$index.jefe_telefono", $experiencia->jefe_telefono ?? '') }}" 
                class="form-control"
                maxlength="20"
            >
        </div>

        <div class="col-md-1 d-flex align-items-end">
            <button 
                type="button" 
                class="btn btn-outline-danger w-100" 
                onclick="eliminarBloque(this, '.item-experiencia')"
            >
                X
            </button>
        </div>
    </div>
</div>