@php
    $esEdicion = isset($formacion) && $formacion;
    $idTipoFormacionActual = $tipoFormacion->id_tipo_formacion;
@endphp

<form 
    method="POST" 
    action="{{ $esEdicion 
        ? route('fac.consultores.formacion.atestados.update', [$consultor, $formacion]) 
        : route('fac.consultores.formacion.store', $consultor) 
    }}" 
    enctype="multipart/form-data"
    class="border rounded p-3 mb-3 bg-light"
>
    @csrf

    @if($esEdicion)
        @method('PUT')
    @endif

    <input type="hidden" name="id_tipo_formacion" value="{{ $idTipoFormacionActual }}">

    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Tipo de atestado <span class="text-danger">*</span></label>
            <select name="id_tipo_atestado" class="form-select">
                <option value="">Seleccione</option>
                @foreach($catalogos['tiposAtestado']->where('id_tipo_formacion', $idTipoFormacionActual) as $tipoAtestado)
                    <option 
                        value="{{ $tipoAtestado->id_tipo_atestado }}"
                        {{ old('id_tipo_atestado', $formacion->id_tipo_atestado ?? '') == $tipoAtestado->id_tipo_atestado ? 'selected' : '' }}
                    >
                        {{ $tipoAtestado->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Nivel académico <span class="text-danger">*</span></label>
            <select name="id_nivel_academico" class="form-select">
                <option value="">Seleccione</option>
                @foreach($catalogos['nivelesAcademicos'] as $nivel)
                    <option 
                        value="{{ $nivel->id_nivel_academico }}"
                        {{ old('id_nivel_academico', $formacion->id_nivel_academico ?? '') == $nivel->id_nivel_academico ? 'selected' : '' }}
                    >
                        {{ $nivel->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">País</label>
            <select name="id_pais" class="form-select">
                <option value="">Seleccione</option>
                @foreach($catalogos['paises'] as $pais)
                    <option 
                        value="{{ $pais->id_pais }}"
                        {{ old('id_pais', $formacion->id_pais ?? '') == $pais->id_pais ? 'selected' : '' }}
                    >
                        {{ $pais->nombre_pais }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Institución <span class="text-danger">*</span></label>
            <input 
                type="text" 
                name="institucion" 
                value="{{ old('institucion', $formacion->institucion ?? '') }}" 
                class="form-control"
                maxlength="250"
            >
        </div>

        <div class="col-md-4">
            <label class="form-label">Descripción / título obtenido <span class="text-danger">*</span></label>
            <input 
                type="text" 
                name="descripcion" 
                value="{{ old('descripcion', $formacion->descripcion ?? '') }}" 
                class="form-control"
                maxlength="250"
            >
        </div>

        <div class="col-md-2">
            <label class="form-label">Fecha inicio</label>
            <input 
                type="date" 
                name="fecha_inicio" 
                value="{{ old('fecha_inicio', isset($formacion) && $formacion->fecha_inicio ? $formacion->fecha_inicio->format('Y-m-d') : '') }}" 
                class="form-control"
            >
        </div>

        <div class="col-md-2">
            <label class="form-label">Fecha fin</label>
            <input 
                type="date" 
                name="fecha_fin" 
                value="{{ old('fecha_fin', isset($formacion) && $formacion->fecha_fin ? $formacion->fecha_fin->format('Y-m-d') : '') }}" 
                class="form-control"
            >
        </div>

        <div class="col-md-8">
            <label class="form-label">
                Comprobante / atestado 
                @if(!$esEdicion)
                    <span class="text-danger">*</span>
                @endif
            </label>

            <input 
                type="file" 
                name="archivo_atestado" 
                class="form-control"
                accept=".pdf,.jpg,.jpeg,.png,.webp"
            >

            <div class="form-text">
                Formatos permitidos: PDF, JPG, JPEG, PNG, WEBP. Máximo 5 MB.
            </div>

            @if($esEdicion && $formacion->url)
                <div class="mt-2">
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($formacion->url) }}" target="_blank">
                        Ver archivo actual
                    </a>
                </div>
            @endif
        </div>

        <div class="col-md-4 d-flex align-items-end justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" onclick="cerrarFormularios()">
                Cancelar
            </button>

            <button type="submit" class="btn btn-fepade">
                {{ $esEdicion ? 'Actualizar atestado' : 'Guardar atestado' }}
            </button>
        </div>
    </div>
</form>