@php
    $esEdicion = isset($referencia) && $referencia;
    $idTipoReferenciaActual = $tipoReferencia->id_tipo_referencia;
    $nombreTipo = strtolower($tipoReferencia->nombre ?? '');
    $esPersonal = str_contains($nombreTipo, 'personal');
    $esProfesional = str_contains($nombreTipo, 'laboral') || str_contains($nombreTipo, 'profesional');
    $idFormularioReferencia = $esEdicion ? 'editar-referencia-' . $referencia->id_referencia : 'crear-referencia-' . $idTipoReferenciaActual;
    $recuperarReferencia = $errors->any() && old('_referencia_form_id') === $idFormularioReferencia;
    $valorReferencia = fn ($campo, $predeterminado = '') => $recuperarReferencia
        ? old($campo, $predeterminado)
        : $predeterminado;
@endphp

<form
    method="POST"
    action="{{ $esEdicion
        ? route('fac.consultores.referencias.update', [$consultor, $referencia])
        : route('fac.consultores.referencias.store', $consultor)
    }}"
    class="border rounded p-3 mb-3 bg-light referencia-form"
>
    @csrf

    @if($esEdicion)
        @method('PUT')
    @endif

    <input type="hidden" name="_referencia_form_id" value="{{ $idFormularioReferencia }}">
    <input type="hidden" name="id_tipo_referencia" value="{{ $idTipoReferenciaActual }}">

    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
            <input type="text" name="nombre" value="{{ $valorReferencia('nombre', $referencia->nombre ?? '') }}" class="form-control" maxlength="150" required>
        </div>

        @if($esPersonal)
            <div class="col-md-4">
                <label class="form-label">Relación <span class="text-danger">*</span></label>
                <select name="id_tipo_relacion" class="form-select" required>
                    <option value="">Seleccione</option>
                    @foreach($catalogos['tiposRelacion'] as $relacion)
                        <option value="{{ $relacion->id_tipo_relacion }}" {{ $valorReferencia('id_tipo_relacion', $referencia->id_tipo_relacion ?? '') == $relacion->id_tipo_relacion ? 'selected' : '' }}>
                            {{ $relacion->nombre }}
                        </option>
                    @endforeach
                </select>
            </div>
        @endif

        @if($esProfesional)
            <div class="col-md-4">
                <label class="form-label">Cargo <span class="text-danger">*</span></label>
                <input type="text" name="cargo" value="{{ $valorReferencia('cargo', $referencia->cargo ?? '') }}" class="form-control" maxlength="100" required>
            </div>

            <div class="col-md-4">
                <label class="form-label">Empresa u organización <span class="text-danger">*</span></label>
                <input type="text" name="empresa" value="{{ $valorReferencia('empresa', $referencia->empresa ?? '') }}" class="form-control" maxlength="150" required>
            </div>
        @endif

        <div class="col-md-4">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefono" autocomplete="tel" inputmode="tel" data-referencia-telefono value="{{ $valorReferencia('telefono', $referencia->telefono ?? '') }}" class="form-control" maxlength="20" placeholder="+503 7777 7777">
        </div>

        <div class="col-md-4">
            <label class="form-label">Correo electrónico</label>
            <input type="email" name="correo" autocomplete="email" value="{{ $valorReferencia('correo', $referencia->correo ?? '') }}" class="form-control" placeholder="nombre@dominio.com" maxlength="120">
        </div>

        @if($recuperarReferencia && $errors->any())
            <div class="col-12">
                <div class="alert alert-danger" role="alert">
                    <strong>Revisa los datos de esta referencia:</strong>
                    <ul class="mb-0">
                        @foreach($errors->all() as $mensaje)
                            <li>{{ $mensaje }}</li>
                        @endforeach
                    </ul>
                </div>
            </div>
        @endif
        <div class="col-12 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" onclick="cerrarFormulariosPerfil()">Cancelar</button>
            <button type="submit" class="btn btn-fepade">{{ $esEdicion ? 'Actualizar referencia' : 'Guardar referencia' }}</button>
        </div>
    </div>
</form>
