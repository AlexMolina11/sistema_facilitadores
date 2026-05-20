<div class="row g-4">
    <div class="col-md-6">
        <label class="form-label">Nombres <span class="text-danger">*</span></label>
        <input 
            type="text" 
            name="nombres" 
            value="{{ old('nombres', $consultor->nombres ?? '') }}" 
            class="form-control @error('nombres') is-invalid @enderror"
            placeholder="Ej: Juan Carlos"
        >
        @error('nombres')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Apellidos <span class="text-danger">*</span></label>
        <input 
            type="text" 
            name="apellidos" 
            value="{{ old('apellidos', $consultor->apellidos ?? '') }}" 
            class="form-control @error('apellidos') is-invalid @enderror"
            placeholder="Ej: Pérez López"
        >
        @error('apellidos')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Apellido de casada</label>
        <input 
            type="text" 
            name="apellido_casa" 
            value="{{ old('apellido_casa', $consultor->apellido_casa ?? '') }}" 
            class="form-control"
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
        <label class="form-label">Sexo</label>
        <select name="sexo" class="form-select @error('sexo') is-invalid @enderror">
            <option value="">Seleccione</option>
            <option value="M" {{ old('sexo', $consultor->sexo ?? '') === 'M' ? 'selected' : '' }}>Masculino</option>
            <option value="F" {{ old('sexo', $consultor->sexo ?? '') === 'F' ? 'selected' : '' }}>Femenino</option>
        </select>
        @error('sexo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Fecha de nacimiento</label>
        <input 
            type="date" 
            name="fecha_nacimiento" 
            value="{{ old('fecha_nacimiento', isset($consultor) && $consultor->fecha_nacimiento ? $consultor->fecha_nacimiento->format('Y-m-d') : '') }}" 
            class="form-control @error('fecha_nacimiento') is-invalid @enderror"
        >
        @error('fecha_nacimiento')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-4">
        <label class="form-label">Nacionalidad</label>
        <input 
            type="text" 
            name="nacionalidad" 
            value="{{ old('nacionalidad', $consultor->nacionalidad ?? '') }}" 
            class="form-control"
            placeholder="Ej: Salvadoreña"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">Tipo de identificación</label>
        <select name="tipo_identificacion" class="form-select">
            <option value="">Seleccione</option>
            @foreach(['DUI', 'Pasaporte', 'Carné de residencia'] as $tipo)
                <option value="{{ $tipo }}" {{ old('tipo_identificacion', $consultor->tipo_identificacion ?? '') === $tipo ? 'selected' : '' }}>
                    {{ $tipo }}
                </option>
            @endforeach
        </select>
    </div>

    <div class="col-md-4">
        <label class="form-label">Número de identificación</label>
        <input 
            type="text" 
            name="numero_identificacion" 
            value="{{ old('numero_identificacion', $consultor->numero_identificacion ?? '') }}" 
            class="form-control"
            placeholder="Ej: 00000000-0"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">NIT</label>
        <input 
            type="text" 
            name="nit" 
            value="{{ old('nit', $consultor->nit ?? '') }}" 
            class="form-control"
        >
    </div>

    <div class="col-md-4">
        <label class="form-label">NRC</label>
        <input 
            type="text" 
            name="nrc" 
            value="{{ old('nrc', $consultor->nrc ?? '') }}" 
            class="form-control"
        >
    </div>

    <div class="col-md-6">
        <label class="form-label">País</label>
        <select name="id_pais" class="form-select @error('id_pais') is-invalid @enderror">
            <option value="">Seleccione</option>
            @foreach($catalogos['paises'] as $pais)
                <option value="{{ $pais->id_pais }}" {{ (string) old('id_pais', $consultor->id_pais ?? '') === (string) $pais->id_pais ? 'selected' : '' }}>
                    {{ $pais->nombre_pais }}
                </option>
            @endforeach
        </select>
        @error('id_pais')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Municipio</label>
        <select name="id_municipio" class="form-select @error('id_municipio') is-invalid @enderror">
            <option value="">Seleccione</option>
            @foreach($catalogos['municipios'] as $municipio)
                <option value="{{ $municipio->id_municipio }}" {{ (string) old('id_municipio', $consultor->id_municipio ?? '') === (string) $municipio->id_municipio ? 'selected' : '' }}>
                    {{ $municipio->nombre_distrito }}
                </option>
            @endforeach
        </select>
        @error('id_municipio')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <label class="form-label">Dirección de residencia</label>
        <textarea 
            name="direccion_residencia" 
            rows="3" 
            class="form-control"
            placeholder="Dirección completa"
        >{{ old('direccion_residencia', $consultor->direccion_residencia ?? '') }}</textarea>
    </div>

    <div class="col-12">
        <label class="form-label">Contacto de emergencia</label>
        <input 
            type="text" 
            name="emergencia_contacto" 
            value="{{ old('emergencia_contacto', $consultor->emergencia_contacto ?? '') }}" 
            class="form-control"
            placeholder="Nombre y teléfono de contacto"
        >
    </div>

    <div class="col-md-6">
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
            <label class="form-check-label" for="vigente">
                Consultor vigente
            </label>
        </div>
    </div>

    <div class="col-md-6">
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
            <label class="form-check-label" for="activo">
                Activo
            </label>
        </div>
    </div>
</div>