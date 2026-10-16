@php
    $esEdicion = isset($formacion) && $formacion;
    $idTipoFormacionActual = $tipoFormacion->id_tipo_formacion;
    $claveFormulario = $idTipoFormacionActual . ':' . ($esEdicion ? $formacion->id_atestado : 'nuevo');
    $usarOld = session()->has('_old_input') && old('_fac_formacion_key') === $claveFormulario;
    $valor = fn ($campo, $predeterminado = '') => $usarOld ? old($campo, $predeterminado) : $predeterminado;
    $erroresFormulario = $usarOld && $errors->any();
@endphp

<form 
    method="POST" 
    action="{{ $esEdicion 
        ? route('fac.consultores.trayectoria.atestados.update', [$consultor, $formacion]) 
        : route('fac.consultores.trayectoria.atestados.store', $consultor) 
    }}" 
    enctype="multipart/form-data"
    class="fac-fase2-form fac-guided-form border rounded-3 p-3 mb-3 bg-white" data-fac-ux-form data-fac-guided-form data-fac-category="{{ mb_strtolower($tipoFormacion->nombre) }}" data-fac-error="{{ $erroresFormulario ? 1 : 0 }}"
>
    @csrf
    <input type="hidden" name="_fac_formacion_key" value="{{ $claveFormulario }}">
    @if($erroresFormulario)
        <div class="alert alert-danger fac-ux-error-summary" role="alert">
            <strong>Revisa los campos señalados.</strong>
            <ul class="mb-0 mt-2">@foreach($errors->all() as $mensaje)<li>{{ $mensaje }}</li>@endforeach</ul>
        </div>
    @endif

    @if($esEdicion)
        @method('PUT')
    @endif

    <input type="hidden" name="id_tipo_formacion" value="{{ $idTipoFormacionActual }}">

    <div class="fac-guide-heading mb-3">
        <div class="d-flex flex-wrap justify-content-between align-items-center gap-2">
            <div><h5 class="mb-1">{{ $esEdicion ? 'Editar registro' : 'Nuevo registro' }} · {{ $tipoFormacion->nombre }}</h5>
            <p class="text-muted small mb-0">Completa únicamente los datos que correspondan a tu documento.</p></div>
            <span class="badge text-bg-light border fac-guide-count">Paso 1 de 3</span>
        </div>
        <div class="fac-guide-progress mt-3" aria-label="Progreso del registro"><span class="fac-guide-progress-fill"></span></div>
        <div class="d-flex justify-content-between text-muted small mt-2"><span>1. Clasificación</span><span>2. Información</span><span>3. Respaldo</span></div>
    </div>
    <section class="fac-guide-step" data-fac-step="1" >
        <h6 class="fw-semibold mb-1">Selecciona el documento</h6>
        <p class="text-muted small mb-3">Los tipos disponibles corresponden a la categoría de formación actual.</p>
        <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Tipo de atestado</label>
            <select name="id_tipo_atestado" class="form-select{{ $erroresFormulario && $errors->has('id_tipo_atestado') ? ' is-invalid' : '' }}">
                <option value="">Seleccione</option>
                @foreach($catalogos['tiposAtestado']->where('id_tipo_formacion', $idTipoFormacionActual) as $tipoAtestado)
                    <option 
                        value="{{ $tipoAtestado->id_tipo_atestado }}"
                        {{ $valor('id_tipo_atestado', $formacion->id_tipo_atestado ?? '') == $tipoAtestado->id_tipo_atestado ? 'selected' : '' }}
                    >
                        {{ $tipoAtestado->nombre }}
                    </option>
                @endforeach
            </select>
            @if($erroresFormulario) @error('id_tipo_atestado') <div class="invalid-feedback d-block" role="alert">{{ $message }}</div> @enderror @endif
        </div>

        </div>
    </section>
    <div class="alert alert-info py-2 small mt-3 fac-guide-document-help" role="status">Selecciona el tipo de atestado para recibir indicaciones específicas.</div>
    <section class="fac-guide-step" data-fac-step="2" hidden>
        <h6 class="fw-semibold mb-1">Información del registro</h6>
        <p class="text-muted small mb-3">Los campos y sus etiquetas se adaptan a lo que estás registrando.</p>
        <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label">Título / nombre del registro <span class="text-danger">*</span></label>
            <input 
                type="text" 
                name="titulo" required 
                value="{{ $valor('titulo', $formacion->titulo ?? '') }}" 
                class="form-control{{ $erroresFormulario && $errors->has('titulo') ? ' is-invalid' : '' }}"
                maxlength="250"
                placeholder="Ej. Maestría, Diplomado, acreditación, capacitación o consultoría realizada"
            >
            @if($erroresFormulario) @error('titulo') <div class="invalid-feedback d-block" role="alert">{{ $message }}</div> @enderror @endif
        </div>

        <div class="col-md-6">
            <label class="form-label">Institución</label>
            <input 
                type="text" 
                name="institucion" 
                value="{{ $valor('institucion', $formacion->institucion ?? '') }}" 
                class="form-control{{ $erroresFormulario && $errors->has('institucion') ? ' is-invalid' : '' }}"
                maxlength="250"
            >
            @if($erroresFormulario) @error('institucion') <div class="invalid-feedback d-block" role="alert">{{ $message }}</div> @enderror @endif
        </div>

        <div class="col-md-4">
            <label class="form-label">Nivel académico</label>
            <select name="id_nivel_academico" class="form-select{{ $erroresFormulario && $errors->has('id_nivel_academico') ? ' is-invalid' : '' }}">
                <option value="">No aplica / no registrado</option>
                @foreach($catalogos['nivelesAcademicos'] as $nivel)
                    <option 
                        value="{{ $nivel->id_nivel_academico }}"
                        {{ $valor('id_nivel_academico', $formacion->id_nivel_academico ?? '') == $nivel->id_nivel_academico ? 'selected' : '' }}
                    >
                        {{ $nivel->nombre }}
                    </option>
                @endforeach
            </select>
            @if($erroresFormulario) @error('id_nivel_academico') <div class="invalid-feedback d-block" role="alert">{{ $message }}</div> @enderror @endif
        </div>

        <div class="col-md-4">
            <label class="form-label">País</label>
            <select name="id_pais" class="form-select{{ $erroresFormulario && $errors->has('id_pais') ? ' is-invalid' : '' }}">
                <option value="">Seleccione</option>
                @foreach($catalogos['paises'] as $pais)
                    <option 
                        value="{{ $pais->id_pais }}"
                        {{ $valor('id_pais', $formacion->id_pais ?? '') == $pais->id_pais ? 'selected' : '' }}
                    >
                        {{ $pais->nombre_pais }}
                    </option>
                @endforeach
            </select>
            @if($erroresFormulario) @error('id_pais') <div class="invalid-feedback d-block" role="alert">{{ $message }}</div> @enderror @endif
        </div>

        <div class="col-md-4">
            <label class="form-label">Entidad acreditadora</label>
            <input 
                type="text" 
                name="entidad_acreditadora" 
                value="{{ $valor('entidad_acreditadora', $formacion->entidad_acreditadora ?? '') }}" 
                class="form-control{{ $erroresFormulario && $errors->has('entidad_acreditadora') ? ' is-invalid' : '' }}"
                maxlength="250"
            >
            @if($erroresFormulario) @error('entidad_acreditadora') <div class="invalid-feedback d-block" role="alert">{{ $message }}</div> @enderror @endif
        </div>

        <div class="col-md-4">
            <label class="form-label">Cliente / institución</label>
            <input 
                type="text" 
                name="cliente_institucion" 
                value="{{ $valor('cliente_institucion', $formacion->cliente_institucion ?? '') }}" 
                class="form-control{{ $erroresFormulario && $errors->has('cliente_institucion') ? ' is-invalid' : '' }}"
                maxlength="250"
            >
            @if($erroresFormulario) @error('cliente_institucion') <div class="invalid-feedback d-block" role="alert">{{ $message }}</div> @enderror @endif
        </div>

        <div class="col-md-4">
            <label class="form-label">Código de acreditación</label>
            <input 
                type="text" 
                name="codigo_acreditacion" 
                value="{{ $valor('codigo_acreditacion', $formacion->codigo_acreditacion ?? '') }}" 
                class="form-control{{ $erroresFormulario && $errors->has('codigo_acreditacion') ? ' is-invalid' : '' }}"
                maxlength="100"
            >
            @if($erroresFormulario) @error('codigo_acreditacion') <div class="invalid-feedback d-block" role="alert">{{ $message }}</div> @enderror @endif
        </div>

        <div class="col-md-2">
            <label class="form-label">Fecha inicio</label>
            <input 
                type="date" 
                name="fecha_inicio" 
                value="{{ $valor('fecha_inicio', isset($formacion) && $formacion->fecha_inicio ? $formacion->fecha_inicio->format('Y-m-d') : '') }}" 
                class="form-control{{ $erroresFormulario && $errors->has('fecha_inicio') ? ' is-invalid' : '' }}"
            >
            @if($erroresFormulario) @error('fecha_inicio') <div class="invalid-feedback d-block" role="alert">{{ $message }}</div> @enderror @endif
        </div>

        <div class="col-md-2">
            <label class="form-label">Fecha fin</label>
            <input 
                type="date" 
                name="fecha_fin" 
                value="{{ $valor('fecha_fin', isset($formacion) && $formacion->fecha_fin ? $formacion->fecha_fin->format('Y-m-d') : '') }}" 
                class="form-control{{ $erroresFormulario && $errors->has('fecha_fin') ? ' is-invalid' : '' }}"
            >
            @if($erroresFormulario) @error('fecha_fin') <div class="invalid-feedback d-block" role="alert">{{ $message }}</div> @enderror @endif
        </div>

        <div class="col-md-2">
            <label class="form-label">Fecha emisión</label>
            <input 
                type="date" 
                name="fecha_emision" 
                value="{{ $valor('fecha_emision', isset($formacion) && $formacion->fecha_emision ? $formacion->fecha_emision->format('Y-m-d') : '') }}" 
                class="form-control{{ $erroresFormulario && $errors->has('fecha_emision') ? ' is-invalid' : '' }}"
            >
            @if($erroresFormulario) @error('fecha_emision') <div class="invalid-feedback d-block" role="alert">{{ $message }}</div> @enderror @endif
        </div>

        <div class="col-md-2">
            <label class="form-label">Fecha vencimiento</label>
            <input 
                type="date" 
                name="fecha_vencimiento" 
                value="{{ $valor('fecha_vencimiento', isset($formacion) && $formacion->fecha_vencimiento ? $formacion->fecha_vencimiento->format('Y-m-d') : '') }}" 
                class="form-control{{ $erroresFormulario && $errors->has('fecha_vencimiento') ? ' is-invalid' : '' }}"
            >
            @if($erroresFormulario) @error('fecha_vencimiento') <div class="invalid-feedback d-block" role="alert">{{ $message }}</div> @enderror @endif
        </div>

        <div class="col-md-2">
            <label class="form-label">Horas</label>
            <input 
                type="number" 
                name="horas" 
                value="{{ $valor('horas', $formacion->horas ?? '') }}" 
                class="form-control{{ $erroresFormulario && $errors->has('horas') ? ' is-invalid' : '' }}"
                min="0"
                max="99999"
            >
            @if($erroresFormulario) @error('horas') <div class="invalid-feedback d-block" role="alert">{{ $message }}</div> @enderror @endif
        </div>

        <div class="col-md-12">
            <label class="form-label">Descripción</label>
            <textarea 
                name="descripcion" maxlength="5000" 
                class="form-control{{ $erroresFormulario && $errors->has('descripcion') ? ' is-invalid' : '' }}"
                rows="3"
                placeholder="Describe brevemente el alcance, contenido o resultado del registro."
            >{{ $valor('descripcion', $formacion->descripcion ?? '') }}</textarea>
            @if($erroresFormulario) @error('descripcion') <div class="invalid-feedback d-block" role="alert">{{ $message }}</div> @enderror @endif
        </div>

        </div>
    </section>
    <section class="fac-guide-step" data-fac-step="3" hidden>
        <h6 class="fw-semibold mb-1">Documento de respaldo</h6>
        <p class="text-muted small mb-3">Adjunta tu evidencia, si dispones de ella.</p>
        <div class="row g-3">
        <div class="col-md-12">
            <label class="form-label">Evidencia / archivo</label>

            <div class="fac-upload-box{{ $erroresFormulario && $errors->has('archivo_atestado') ? ' border-danger' : '' }}">
                <label for="fac-archivo-{{ $idTipoFormacionActual }}-{{ $esEdicion ? $formacion->id_atestado : 'nuevo' }}" class="btn btn-outline-secondary fac-upload-button mb-0">
                    <i class="fa-solid fa-cloud-arrow-up me-1" aria-hidden="true"></i> Elegir documento
                </label>
                <span class="fac-upload-filename small text-muted" aria-live="polite">Ningún archivo nuevo seleccionado</span>
                <input
                    id="fac-archivo-{{ $idTipoFormacionActual }}-{{ $esEdicion ? $formacion->id_atestado : 'nuevo' }}"
                    type="file"
                    name="archivo_atestado"
                    class="visually-hidden{{ $erroresFormulario && $errors->has('archivo_atestado') ? ' is-invalid' : '' }}"
                    accept=".pdf,.jpg,.jpeg,.png,.webp"
                >
            </div>
            @if($erroresFormulario) @error('archivo_atestado') <div class="invalid-feedback d-block" role="alert">{{ $message }}</div> @enderror @endif

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

        </div>
    </section>
    <div class="alert alert-light border small mt-3 fac-guide-review" aria-live="polite" hidden></div>
    <div class="d-flex flex-wrap justify-content-between align-items-center gap-2 mt-4 border-top pt-3">
        <button type="button" class="btn btn-outline-secondary" onclick="cerrarFormularios()">Cancelar</button>
        <div class="d-flex gap-2">
            <button type="button" class="btn btn-outline-secondary fac-guide-back" hidden>Anterior</button>
            <button type="button" class="btn btn-fepade fac-guide-next">Continuar <i class="fa-solid fa-arrow-right ms-1" aria-hidden="true"></i></button>
            <button type="submit" class="btn btn-fepade fac-guide-save" hidden>{{ $esEdicion ? 'Actualizar registro' : 'Guardar registro' }}</button>
        </div>
    </div>
</form>
