@php
    $esEdicion = isset($formacion) && $formacion;
    $idTipoFormacionActual = $tipoFormacion->id_tipo_formacion;
@endphp

<form 
    method="POST" 
    action="{{ $esEdicion 
        ? route('fac.consultores.trayectoria.atestados.update', [$consultor, $formacion]) 
        : route('fac.consultores.trayectoria.atestados.store', $consultor) 
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
            <label class="form-label">Nivel académico</label>
            <select name="id_nivel_academico" class="form-select">
                <option value="">No aplica / no registrado</option>
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

        <div class="col-md-6">
            <label class="form-label">Título / nombre del registro <span class="text-danger">*</span></label>
            <input 
                type="text" 
                name="titulo" 
                value="{{ old('titulo', $formacion->titulo ?? '') }}" 
                class="form-control"
                maxlength="250"
                placeholder="Ej. Maestría, Diplomado, acreditación, capacitación o consultoría realizada"
            >
        </div>

        <div class="col-md-6">
            <label class="form-label">Institución</label>
            <input 
                type="text" 
                name="institucion" 
                value="{{ old('institucion', $formacion->institucion ?? '') }}" 
                class="form-control"
                maxlength="250"
            >
        </div>

        <div class="col-md-12">
            <label class="form-label">Descripción</label>
            <textarea 
                name="descripcion" 
                class="form-control"
                rows="3"
                placeholder="Describe brevemente el alcance, contenido o resultado del registro."
            >{{ old('descripcion', $formacion->descripcion ?? '') }}</textarea>
        </div>

        <div class="col-md-4">
            <label class="form-label">Entidad acreditadora</label>
            <input 
                type="text" 
                name="entidad_acreditadora" 
                value="{{ old('entidad_acreditadora', $formacion->entidad_acreditadora ?? '') }}" 
                class="form-control"
                maxlength="250"
            >
        </div>

        <div class="col-md-4">
            <label class="form-label">Cliente / institución</label>
            <input 
                type="text" 
                name="cliente_institucion" 
                value="{{ old('cliente_institucion', $formacion->cliente_institucion ?? '') }}" 
                class="form-control"
                maxlength="250"
            >
        </div>

        <div class="col-md-4">
            <label class="form-label">Código de acreditación</label>
            <input 
                type="text" 
                name="codigo_acreditacion" 
                value="{{ old('codigo_acreditacion', $formacion->codigo_acreditacion ?? '') }}" 
                class="form-control"
                maxlength="100"
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

        <div class="col-md-2">
            <label class="form-label">Fecha emisión</label>
            <input 
                type="date" 
                name="fecha_emision" 
                value="{{ old('fecha_emision', isset($formacion) && $formacion->fecha_emision ? $formacion->fecha_emision->format('Y-m-d') : '') }}" 
                class="form-control"
            >
        </div>

        <div class="col-md-2">
            <label class="form-label">Fecha vencimiento</label>
            <input 
                type="date" 
                name="fecha_vencimiento" 
                value="{{ old('fecha_vencimiento', isset($formacion) && $formacion->fecha_vencimiento ? $formacion->fecha_vencimiento->format('Y-m-d') : '') }}" 
                class="form-control"
            >
        </div>

        <div class="col-md-2">
            <label class="form-label">Horas</label>
            <input 
                type="number" 
                name="horas" 
                value="{{ old('horas', $formacion->horas ?? '') }}" 
                class="form-control"
                min="0"
                max="9999"
            >
        </div>

        <div class="col-md-12">
            <label class="form-label">Evidencia / archivo</label>

            <input 
                type="file" 
                name="archivo_atestado" 
                class="form-control"
                accept=".pdf,.jpg,.jpeg,.png,.webp"
            >

            <div class="form-text">
                Formatos permitidos: PDF, JPG, JPEG, PNG, WEBP. Máximo 5 MB.
            </div>

            @if($esEdicion && $formacion->url_archivo)
                <div class="mt-2">
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($formacion->url_archivo) }}" target="_blank">
                        Ver archivo actual
                    </a>
                    @if($formacion->nombre_archivo_original)
                        <span class="text-muted small ms-2">{{ $formacion->nombre_archivo_original }}</span>
                    @endif
                </div>
            @endif
        </div>

        <div class="col-md-12 d-flex align-items-end justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" onclick="cerrarFormularios()">
                Cancelar
            </button>

            <button type="submit" class="btn btn-fepade">
                {{ $esEdicion ? 'Actualizar registro' : 'Guardar registro' }}
            </button>
        </div>
    </div>
</form>
