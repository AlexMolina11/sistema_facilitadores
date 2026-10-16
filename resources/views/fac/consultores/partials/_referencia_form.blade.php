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

    <div class="mb-3">
        <div class="fw-semibold">{{ $esEdicion ? 'Editar referencia' : 'Nueva referencia' }}</div>
        <p class="small text-muted mb-0">Completa los datos de contacto. Los campos con <span class="text-danger">*</span> son obligatorios.</p>
    </div>
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Nombre completo <span class="text-danger">*</span></label>
            <input type="text" name="nombre" value="{{ $valorReferencia('nombre', $referencia->nombre ?? '') }}" class="form-control @if($recuperarReferencia) @error('nombre') is-invalid @enderror @endif" maxlength="150" required>
            @if($recuperarReferencia) @error('nombre') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror @endif
        </div>

        @if($esPersonal)
            <div class="col-md-6">
                <label class="form-label">Relación <span class="text-danger">*</span></label>
                <div class="fac-ref-combobox" data-fac-ref-combobox data-required-message="Selecciona una relación de la lista.">
                    <div class="fac-ref-combobox-control @if($recuperarReferencia) @error('id_tipo_relacion') is-invalid @enderror @endif">
                        <i class="fas fa-search fac-ref-combobox-icon" aria-hidden="true"></i>
                        <input type="text" class="fac-ref-combobox-input" autocomplete="off" role="combobox" aria-autocomplete="list" aria-expanded="false" aria-label="Buscar relación" title="Escribe para buscar o despliega las relaciones disponibles" placeholder="Buscar o seleccionar relación...">
                        <button type="button" class="fac-ref-combobox-toggle" aria-label="Mostrar relaciones" tabindex="-1"><i class="fas fa-chevron-down"></i></button>
                    </div>
                    <div class="fac-ref-combobox-menu" role="listbox" hidden></div>
                    <select name="id_tipo_relacion" class="fac-ref-native-select @if($recuperarReferencia) @error('id_tipo_relacion') is-invalid @enderror @endif" tabindex="-1" aria-hidden="true" required>
                    <option value="">Seleccione</option>
                    @foreach($catalogos['tiposRelacion'] as $relacion)
                        <option value="{{ $relacion->id_tipo_relacion }}" {{ $valorReferencia('id_tipo_relacion', $referencia->id_tipo_relacion ?? '') == $relacion->id_tipo_relacion ? 'selected' : '' }}>
                            {{ $relacion->nombre }}
                        </option>
                    @endforeach
                    </select>
                </div>

                @if($recuperarReferencia) @error('id_tipo_relacion') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror @endif
            </div>
        @endif

        @if($esProfesional)
            <div class="col-md-6">
                <label class="form-label">Cargo <span class="text-danger">*</span></label>
                <input type="text" name="cargo" value="{{ $valorReferencia('cargo', $referencia->cargo ?? '') }}" class="form-control @if($recuperarReferencia) @error('cargo') is-invalid @enderror @endif" maxlength="100" required>
            @if($recuperarReferencia) @error('cargo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror @endif
            </div>

            <div class="col-md-6">
                <label class="form-label">Empresa u organización <span class="text-danger">*</span></label>
                <input type="text" name="empresa" value="{{ $valorReferencia('empresa', $referencia->empresa ?? '') }}" class="form-control @if($recuperarReferencia) @error('empresa') is-invalid @enderror @endif" maxlength="150" required>
            @if($recuperarReferencia) @error('empresa') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror @endif
            </div>
        @endif

        <div class="col-md-6">
            <label class="form-label">Teléfono</label>
            <input type="text" name="telefono" autocomplete="tel" inputmode="tel" data-referencia-telefono value="{{ $valorReferencia('telefono', $referencia->telefono ?? '') }}" class="form-control @if($recuperarReferencia) @error('telefono') is-invalid @enderror @endif" maxlength="20" placeholder="+503 7777 7777">
            @if($recuperarReferencia) @error('telefono') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror @endif
        </div>

        <div class="col-md-6">
            <label class="form-label">Correo electrónico</label>
            <input type="email" name="correo" autocomplete="email" value="{{ $valorReferencia('correo', $referencia->correo ?? '') }}" class="form-control @if($recuperarReferencia) @error('correo') is-invalid @enderror @endif" placeholder="nombre@dominio.com" maxlength="120">
            @if($recuperarReferencia) @error('correo') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror @endif
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
