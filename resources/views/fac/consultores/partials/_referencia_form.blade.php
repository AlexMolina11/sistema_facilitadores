@php
    $esEdicion = isset($referencia) && $referencia;
    $idTipoReferenciaActual = $tipoReferencia->id_tipo_referencia;
    $nombreTipo = strtolower($tipoReferencia->nombre ?? '');
    $esPersonal = str_contains($nombreTipo, 'personal');
    $esProfesional = str_contains($nombreTipo, 'laboral') || str_contains($nombreTipo, 'profesional');
@endphp

<form
    method="POST"
    action="{{ $esEdicion
        ? route('fac.consultores.referencias.update', [$consultor, $referencia])
        : route('fac.consultores.referencias.store', $consultor)
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
            <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
            <input type="text" name="nombre" value="{{ old('nombre', $referencia->nombre ?? '') }}" class="form-control" maxlength="150" required>
        </div>

        @if($esPersonal)
            <div class="col-md-4">
                <label class="form-label">Relación / relacion <span class="text-danger">*</span></label>
                <select name="id_tipo_relacion" class="form-select" required>
                    <option value="">Seleccione</option>
                    @foreach($catalogos['tiposRelacion'] as $relacion)
                        <option value="{{ $relacion->id_tipo_relacion }}" {{ old('id_tipo_relacion', $referencia->id_tipo_relacion ?? '') == $relacion->id_tipo_relacion ? 'selected' : '' }}>
                            {{ $relacion->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        @if($esProfesional)
            <div class="col-md-4">
                <label class="form-label">Cargo <span class="text-danger">*</span></label>
                <input type="text" name="cargo" value="{{ old('cargo', $referencia->cargo ?? '') }}" class="form-control" maxlength="100" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Empresa u organización <span class="text-danger">*</span></label>
                <input type="text" name="empresa" value="{{ old('empresa', $referencia->empresa ?? '') }}" class="form-control" maxlength="150" required>
            </div>
        @endif

        <div class="col-md-4">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefono" value="{{ old('telefono', $referencia->telefono ?? '') }}" class="form-control" maxlength="20" placeholder="7777-7777">
        </div>

        <div class="col-md-4">
            <label class="form-label">Correo electrónico</label>
            <input type="email" name="correo" value="{{ old('correo', $referencia->correo ?? '') }}" class="form-control" placeholder="nombre@dominio.com" maxlength="120">
        </div>

        <div class="col-12 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" onclick="cerrarFormulariosPerfil()">Cancelar</button>
            <button type="submit" class="btn btn-fepade">{{ $esEdicion ? 'Actualizar referencia' : 'Guardar referencia' }}</button>
        </div>
    </div>
</form>
