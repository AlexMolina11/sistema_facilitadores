@php
    $municipioActual = null;
    $idDepartamentoActual = old('id_departamento');
    $idMunicipioMhActual = old('id_municipio_mh');

    if (isset($consultor) && $consultor && $consultor->id_municipio) {
        $municipioActual = $catalogos['municipios']->firstWhere('id_municipio', $consultor->id_municipio);
        $idDepartamentoActual = old('id_departamento', $municipioActual->id_departamento ?? null);
        $idMunicipioMhActual = old('id_municipio_mh', $municipioActual->id_municipio_mh ?? null);
    }

    $documentoIdentificacion = null;
    $documentoNit = null;
    $documentoNrc = null;

    if (isset($consultor) && $consultor && $consultor->relationLoaded('documentos')) {
        $documentoIdentificacion = $consultor->documentos->first(function ($documento) use ($catalogos, $consultor) {
            $tipo = $catalogos['tiposDocumento']->get($documento->id_tipo_documento) ?? null;

            return $tipo && strtolower(str_replace(['.', ' ', '-'], '', $tipo->nombre)) === strtolower(str_replace(['.', ' ', '-'], '', $consultor->tipo_identificacion ?? ''));
        });

        $documentoNit = $consultor->documentos->first(function ($documento) use ($catalogos) {
            $tipo = $catalogos['tiposDocumento']->get($documento->id_tipo_documento) ?? null;

            return $tipo && strtolower(str_replace(['.', ' ', '-'], '', $tipo->nombre)) === 'nit';
        });

        $documentoNrc = $consultor->documentos->first(function ($documento) use ($catalogos) {
            $tipo = $catalogos['tiposDocumento']->get($documento->id_tipo_documento) ?? null;

            return $tipo && strtolower(str_replace(['.', ' ', '-'], '', $tipo->nombre)) === 'nrc';
        });
    }
@endphp



@if($errors->any())
    <div class="alert alert-danger">
        <strong>Revisa los campos marcados.</strong>
        <ul class="mb-0 mt-2">
            @foreach($errors->all() as $error)
                <li>{{ $error }}</li>
            @endforeach
        </ul>
    </div>
@endif

<div class="row g-4">
    <div class="col-12">
        <h5 class="mb-1">Datos personales</h5>
        <p class="text-muted mb-0">Información general e identificación del consultor.</p>
    </div>

    <div class="col-md-6">
        <label class="form-label">Nombres <span class="text-danger">*</span></label>
        <input 
            type="text" 
            name="nombres" 
            value="{{ old('nombres', $consultor->nombres ?? '') }}" 
            class="form-control @error('nombres') is-invalid @enderror"
            maxlength="100"
            required
        >
        @error('nombres') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Apellidos <span class="text-danger">*</span></label>
        <input 
            type="text" 
            name="apellidos" 
            value="{{ old('apellidos', $consultor->apellidos ?? '') }}" 
            class="form-control @error('apellidos') is-invalid @enderror"
            maxlength="100"
            required
        >
        @error('apellidos') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Apellido de casada</label>
        <input 
            type="text" 
            name="apellido_casa" 
            value="{{ old('apellido_casa', $consultor->apellido_casa ?? '') }}" 
            class="form-control"
            maxlength="100"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">Estado civil</label>
        <select name="estado_civil" class="form-select">
            <option value="">Seleccione</option>
            @foreach(['Soltero/a', 'Casado/a', 'Divorciado/a', 'Viudo/a', 'Unión libre'] as $estadoCivil)
                <option value="{{ $estadoCivil }}" {{ old('estado_civil', $consultor->estado_civil ?? '') === $estadoCivil ? 'selected' : '' }}>
                    {{ $estadoCivil }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">Sexo <span class="text-danger">*</span></label>
        <select name="id_sexo" class="form-select @error('id_sexo') is-invalid @enderror" required>
            <option value="">Seleccione</option>
            @foreach(($catalogos['sexos'] ?? collect()) as $sexo)
                <option value="{{ $sexo->id_sexo }}"
                    {{ (string) old('id_sexo', $consultor->id_sexo ?? '') === (string) $sexo->id_sexo ? 'selected' : '' }}>
                    {{ $sexo->nombre }}
                </option>
            @endforeach
        </select>
        @error('id_sexo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Fecha de nacimiento <span class="text-danger">*</span></label>
        <input 
            type="date" 
            name="fecha_nacimiento" 
            value="{{ old('fecha_nacimiento', isset($consultor) && $consultor->fecha_nacimiento ? $consultor->fecha_nacimiento->format('Y-m-d') : '') }}" 
            class="form-control @error('fecha_nacimiento') is-invalid @enderror"
            required
        >
        @error('fecha_nacimiento') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Nacionalidad <span class="text-danger">*</span></label>
        <input 
            type="text" 
            name="nacionalidad" 
            value="{{ old('nacionalidad', $consultor->nacionalidad ?? '') }}" 
            class="form-control"
            maxlength="50"
            placeholder="Ej: Salvadoreña"
            required
        >
        @error('nacionalidad') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <hr>
        <h5 class="mb-1">Documentos de identificación</h5>
        <p class="text-muted mb-0">Registra el número y adjunta el documento correspondiente.</p>
    </div>

    <div class="col-md-4">
        <div class="fepade-card h-100">
            <h6 class="mb-3">Documento de identidad</h6>

            <label class="form-label">Tipo de identificación</label>
            <select name="tipo_identificacion" class="form-select mb-3">
                <option value="">Seleccione</option>
                @foreach(['DUI', 'Pasaporte', 'Carné de residencia'] as $tipo)
                    <option value="{{ $tipo }}" {{ old('tipo_identificacion', $consultor->tipo_identificacion ?? '') === $tipo ? 'selected' : '' }}>
                        {{ $tipo }}
                    </option>
                @endforeach
            </select>

            <label class="form-label">Número</label>
            <input 
                type="text" 
                name="numero_identificacion" 
                value="{{ old('numero_identificacion', $consultor->numero_identificacion ?? '') }}" 
                class="form-control @error('numero_identificacion') is-invalid @enderror"
                maxlength="30"
            >
            @error('numero_identificacion') <div class="invalid-feedback">{{ $message }}</div> @enderror

            <label class="form-label mt-3">Archivo</label>
            <input 
                type="file" 
                name="documento_identificacion" 
                class="form-control @error('documento_identificacion') is-invalid @enderror"
                accept=".pdf,.jpg,.jpeg,.png,.webp"
            >
            <div class="form-text">PDF, JPG, JPEG, PNG o WEBP. Máximo 5 MB.</div>
            @error('documento_identificacion') <div class="invalid-feedback">{{ $message }}</div> @enderror
            @if($documentoIdentificacion?->url_archivo)
                <div class="mt-2">
                    <a 
                        href="{{ \Illuminate\Support\Facades\Storage::url($documentoIdentificacion->url_archivo) }}" 
                        target="_blank"
                        class="formacion-document-link"
                    >
                        Ver documento actual
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="col-md-4">
        <div class="fepade-card h-100">
            <h6 class="mb-3">NIT</h6>

            <label class="form-label">Número</label>
            <input 
                type="text" 
                name="nit" 
                value="{{ old('nit', $consultor->nit ?? '') }}" 
                class="form-control @error('nit') is-invalid @enderror"
                maxlength="20"
            >
            @error('nit') <div class="invalid-feedback">{{ $message }}</div> @enderror

            <label class="form-label mt-3">Archivo</label>
            <input 
                type="file" 
                name="documento_nit" 
                class="form-control @error('documento_nit') is-invalid @enderror"
                accept=".pdf,.jpg,.jpeg,.png,.webp"
            >
            <div class="form-text">PDF, JPG, JPEG, PNG o WEBP. Máximo 5 MB.</div>
            @error('documento_nit') <div class="invalid-feedback">{{ $message }}</div> @enderror
            @if($documentoNit?->url_archivo)
                <div class="mt-2">
                    <a 
                        href="{{ \Illuminate\Support\Facades\Storage::url($documentoNit->url_archivo) }}" 
                        target="_blank"
                        class="formacion-document-link"
                    >
                        Ver documento actual
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="col-md-4">
        <div class="fepade-card h-100">
            <h6 class="mb-3">NRC</h6>

            <label class="form-label">Número</label>
            <input 
                type="text" 
                name="nrc" 
                value="{{ old('nrc', $consultor->nrc ?? '') }}" 
                class="form-control @error('nrc') is-invalid @enderror"
                maxlength="20"
            >
            @error('nrc') <div class="invalid-feedback">{{ $message }}</div> @enderror

            <label class="form-label mt-3">Actividad / Giro</label>
            <input 
                type="text" 
                name="actividad_giro" 
                value="{{ old('actividad_giro') }}" 
                class="form-control @error('actividad_giro') is-invalid @enderror"
                maxlength="255"
            >
            @error('actividad_giro') <div class="invalid-feedback">{{ $message }}</div> @enderror

            <label class="form-label mt-3">Archivo</label>
            <input 
                type="file" 
                name="documento_nrc" 
                class="form-control @error('documento_nrc') is-invalid @enderror"
                accept=".pdf,.jpg,.jpeg,.png,.webp"
            >
            <div class="form-text">PDF, JPG, JPEG, PNG o WEBP. Máximo 5 MB.</div>
            @error('documento_nrc') <div class="invalid-feedback">{{ $message }}</div> @enderror
            @if($documentoNrc?->url_archivo)
                <div class="mt-2">
                    <a 
                        href="{{ \Illuminate\Support\Facades\Storage::url($documentoNrc->url_archivo) }}" 
                        target="_blank"
                        class="formacion-document-link"
                    >
                        Ver documento actual
                    </a>
                </div>
            @endif
        </div>
    </div>

    <div class="col-12">
        <hr>
        <h5 class="mb-1">Residencia</h5>
        <p class="text-muted mb-0">Selecciona país, departamento, municipio y distrito.</p>
    </div>

    <div class="col-md-6">
        <label class="form-label">País</label>
        <select name="id_pais" id="id_pais" class="form-select @error('id_pais') is-invalid @enderror">
            <option value="">Seleccione</option>
            @foreach($catalogos['paises'] as $pais)
                <option value="{{ $pais->id_pais }}" {{ (string) old('id_pais', $consultor->id_pais ?? '') === (string) $pais->id_pais ? 'selected' : '' }}>
                    {{ $pais->nombre_pais }}
                </option>
            @endforeach
        </select>
        @error('id_pais') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Departamento</label>
        <select name="id_departamento" id="id_departamento" class="form-select @error('id_departamento') is-invalid @enderror">
            <option value="">Seleccione</option>
            @foreach($catalogos['departamentos'] as $departamento)
                <option 
                    value="{{ $departamento->id_departamento }}"
                    data-pais="{{ $departamento->id_pais }}"
                    {{ (string) $idDepartamentoActual === (string) $departamento->id_departamento ? 'selected' : '' }}
                >
                    {{ $departamento->nombre_departamento }}
                </option>
            @endforeach
        </select>
        @error('id_departamento') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Municipio</label>
        <select name="id_municipio_mh" id="id_municipio_mh" class="form-select @error('id_municipio_mh') is-invalid @enderror">
            <option value="">Seleccione</option>
            @foreach($catalogos['municipiosMh'] as $municipioMh)
                <option 
                    value="{{ $municipioMh->id_municipio_mh }}"
                    data-departamento="{{ $municipioMh->id_departamento }}"
                    {{ (string) $idMunicipioMhActual === (string) $municipioMh->id_municipio_mh ? 'selected' : '' }}
                >
                    {{ $municipioMh->municipio_mh_nombre }}
                </option>
            @endforeach
        </select>
        @error('id_municipio_mh') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Distrito</label>
        <select name="id_municipio" id="id_municipio" class="form-select @error('id_municipio') is-invalid @enderror">
            <option value="">Seleccione</option>
            @foreach($catalogos['municipios'] as $municipio)
                <option 
                    value="{{ $municipio->id_municipio }}"
                    data-pais="{{ $municipio->id_pais }}"
                    data-departamento="{{ $municipio->id_departamento }}"
                    data-municipio-mh="{{ $municipio->id_municipio_mh }}"
                    {{ (string) old('id_municipio', $consultor->id_municipio ?? '') === (string) $municipio->id_municipio ? 'selected' : '' }}
                >
                    {{ $municipio->nombre_distrito }}
                </option>
            @endforeach
        </select>
        @error('id_municipio') <div class="invalid-feedback">{{ $message }}</div> @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Dirección de residencia</label>
        <textarea 
            name="direccion_residencia" 
            rows="3" 
            class="form-control"
            maxlength="200"
        >{{ old('direccion_residencia', $consultor->direccion_residencia ?? '') }}</textarea>
    </div>

    <div class="col-12">
        <hr>
        <h5 class="mb-1">Foto y estado</h5>
        <p class="text-muted mb-0">Foto del consultor y estado del perfil.</p>
    </div>

    <div class="col-md-6">
        <label class="form-label">Foto del consultor</label>
        <input 
            type="file" 
            name="foto" 
            class="form-control @error('foto') is-invalid @enderror"
            accept=".jpg,.jpeg,.png,.webp"
        >
        <div class="form-text">Formatos permitidos: JPG, JPEG, PNG, WEBP. Máximo 2 MB.</div>
        @error('foto') <div class="invalid-feedback">{{ $message }}</div> @enderror

        @if(isset($consultor) && $consultor && $consultor->ruta_foto)
            <div class="mt-3">
                <img 
                    src="{{ \Illuminate\Support\Facades\Storage::url($consultor->ruta_foto) }}" 
                    alt="Foto del consultor"
                    class="rounded"
                    style="max-width: 140px;"
                >
            </div>
        @endif
    </div>

    <div class="col-md-3">
        <label class="form-label d-block">Vigencia</label>
        <div class="form-check form-switch mt-2">
            <input 
                class="form-check-input" 
                type="checkbox" 
                name="vigente" 
                value="1" 
                id="vigente"
                {{ old('vigente', $consultor->vigente ?? true) ? 'checked' : '' }}
            >
            <label class="form-check-label" for="vigente">Consultor vigente</label>
        </div>
    </div>

    <div class="col-md-3">
        <label class="form-label d-block">Estado</label>
        <div class="form-check form-switch mt-2">
            <input 
                class="form-check-input" 
                type="checkbox" 
                name="activo" 
                value="1" 
                id="activo"
                {{ old('activo', $consultor->activo ?? true) ? 'checked' : '' }}
            >
            <label class="form-check-label" for="activo">Activo</label>
        </div>
    </div>
</div>

<script>
    function filtrarUbicacion() {
        const pais = document.getElementById('id_pais').value;
        const departamento = document.getElementById('id_departamento').value;
        const municipioMh = document.getElementById('id_municipio_mh').value;

        document.querySelectorAll('#id_departamento option').forEach(option => {
            if (!option.value) {
                option.hidden = false;
                return;
            }

            option.hidden = pais && option.dataset.pais !== pais;
        });

        document.querySelectorAll('#id_municipio_mh option').forEach(option => {
            if (!option.value) {
                option.hidden = false;
                return;
            }

            option.hidden = departamento && option.dataset.departamento !== departamento;
        });

        document.querySelectorAll('#id_municipio option').forEach(option => {
            if (!option.value) {
                option.hidden = false;
                return;
            }

            const noCoincidePais = pais && option.dataset.pais !== pais;
            const noCoincideDepartamento = departamento && option.dataset.departamento !== departamento;
            const noCoincideMh = municipioMh && option.dataset.municipioMh !== municipioMh;

            option.hidden = noCoincidePais || noCoincideDepartamento || noCoincideMh;
        });
    }

    document.addEventListener('DOMContentLoaded', function () {
        filtrarUbicacion();

        document.getElementById('id_pais').addEventListener('change', function () {
            document.getElementById('id_departamento').value = '';
            document.getElementById('id_municipio_mh').value = '';
            document.getElementById('id_municipio').value = '';
            filtrarUbicacion();
        });

        document.getElementById('id_departamento').addEventListener('change', function () {
            document.getElementById('id_municipio_mh').value = '';
            document.getElementById('id_municipio').value = '';
            filtrarUbicacion();
        });

        document.getElementById('id_municipio_mh').addEventListener('change', function () {
            document.getElementById('id_municipio').value = '';
            filtrarUbicacion();
        });
    });
</script>