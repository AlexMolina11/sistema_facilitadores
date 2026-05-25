@php
    $esEdicion = isset($referencia) && $referencia;
    $idTipoReferenciaActual = $tipoReferencia->id_tipo_referencia;
@endphp

<form
    method="POST"
    action="{{ $esEdicion
        ? route('fac.consultores.experiencia.referencias.update', [$consultor, $referencia])
        : route('fac.consultores.experiencia.referencias.store', $consultor)
    }}"
    class="border rounded p-3 mb-3 bg-light"
>
    @csrf

    @if($esEdicion)
        @method('PUT')
    @endif

    <input type="hidden" name="id_tipo_referencia" value="{{ $idTipoReferenciaActual }}">

    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Nombre <span class="text-danger">*</span></label>
            <input type="text" name="nombre" value="{{ old('nombre', $referencia->nombre ?? '') }}" class="form-control" maxlength="150">
        </div>

        <div class="col-md-4">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefono" value="{{ old('telefono', $referencia->telefono ?? '') }}" class="form-control" maxlength="20">
        </div>

        <div class="col-md-4">
            <label class="form-label">Correo</label>
            <input type="email" name="correo" value="{{ old('correo', $referencia->correo ?? '') }}" class="form-control" placeholder="nombre@dominio.com" maxlength="120">
        </div>

        <div class="col-md-4">
            <label class="form-label">Empresa</label>
            <input type="text" name="empresa" value="{{ old('empresa', $referencia->empresa ?? '') }}" class="form-control" maxlength="150">
        </div>

        <div class="col-md-4">
            <label class="form-label">Cargo</label>
            <input type="text" name="cargo" value="{{ old('cargo', $referencia->cargo ?? '') }}" class="form-control" maxlength="100">
        </div>

        <div class="col-md-4 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" onclick="cerrarFormulariosExperiencia()">Cancelar</button>
            <button type="submit" class="btn btn-fepade">{{ $esEdicion ? 'Actualizar referencia' : 'Guardar referencia' }}</button>
        </div>
    </div>
</form>
