@php
    $esEdicion = isset($experiencia) && $experiencia;
@endphp

<form
    method="POST"
    action="{{ $esEdicion
        ? route('fac.consultores.experiencia.laboral.update', [$consultor, $experiencia])
        : route('fac.consultores.experiencia.laboral.store', $consultor)
    }}"
    enctype="multipart/form-data"
    class="border rounded p-3 mb-3 bg-light"
>
    @csrf

    @if($esEdicion)
        @method('PUT')
    @endif

    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Empresa <span class="text-danger">*</span></label>
            <input type="text" name="empresa" value="{{ old('empresa', $experiencia->empresa ?? '') }}" class="form-control" maxlength="150">
        </div>

        <div class="col-md-4">
            <label class="form-label">Cargo <span class="text-danger">*</span></label>
            <input type="text" name="cargo" value="{{ old('cargo', $experiencia->cargo ?? '') }}" class="form-control" maxlength="100">
        </div>

        <div class="col-md-4 d-flex align-items-end">
            <div class="form-check form-switch mb-2">
                <input class="form-check-input" type="checkbox" name="trabajo_actual" value="1" id="trabajo_actual_{{ $esEdicion ? $experiencia->id_experiencia : 'nuevo' }}" {{ old('trabajo_actual', $experiencia->trabajo_actual ?? false) ? 'checked' : '' }}>
                <label class="form-check-label" for="trabajo_actual_{{ $esEdicion ? $experiencia->id_experiencia : 'nuevo' }}">Trabajo actual</label>
            </div>
        </div>

        <div class="col-md-3">
            <label class="form-label">Fecha desde</label>
            <input type="date" name="desde" value="{{ old('desde', isset($experiencia) && $experiencia?->desde ? $experiencia->desde->format('Y-m-d') : '') }}" class="form-control">
        </div>

        <div class="col-md-3">
            <label class="form-label">Fecha hasta</label>
            <input type="date" name="hasta" value="{{ old('hasta', isset($experiencia) && $experiencia?->hasta ? $experiencia->hasta->format('Y-m-d') : '') }}" class="form-control">
        </div>

        <div class="col-md-6">
            <label class="form-label">Evidencia laboral</label>
            <input type="file" name="evidencia" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
            <div class="form-text">PDF, JPG, JPEG, PNG o WEBP. Máximo 5 MB.</div>

            @if($esEdicion && $experiencia->url_evidencia)
                <div class="mt-2">
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($experiencia->url_evidencia) }}" target="_blank">Ver evidencia actual</a>
                </div>
            @endif
        </div>

        <div class="col-12">
            <label class="form-label">Descripción de funciones o experiencia</label>
            <textarea name="descripcion" rows="3" class="form-control" maxlength="500">{{ old('descripcion', $experiencia->descripcion ?? '') }}</textarea>
        </div>

        <div class="col-md-4">
            <label class="form-label">Jefe inmediato</label>
            <input type="text" name="jefe_nombre" value="{{ old('jefe_nombre', $experiencia->jefe_nombre ?? '') }}" class="form-control" maxlength="150">
        </div>

        <div class="col-md-4">
            <label class="form-label">Correo del jefe inmediato</label>
            <input type="email" name="jefe_email" value="{{ old('jefe_email', $experiencia->jefe_email ?? '') }}" class="form-control" placeholder="nombre@dominio.com" maxlength="120">
        </div>

        <div class="col-md-4">
            <label class="form-label">Teléfono del jefe inmediato</label>
            <input type="text" name="jefe_telefono" value="{{ old('jefe_telefono', $experiencia->jefe_telefono ?? '') }}" class="form-control" maxlength="20">
        </div>

        <div class="col-12 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" onclick="cerrarFormulariosPerfil()">Cancelar</button>
            <button type="submit" class="btn btn-fepade">{{ $esEdicion ? 'Actualizar experiencia' : 'Guardar experiencia' }}</button>
        </div>
    </div>
</form>
