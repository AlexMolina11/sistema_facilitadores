@extends('layouts.app')

@section('title', 'Contacto consultor | Facilitadores FEPADE')
@section('page-title', 'Contacto del consultor')
@section('page-subtitle', 'Correos, teléfonos, redes sociales y emergencia')

@section('content')

<x-ui.page-header 
    title="Contacto del consultor"
    subtitle="{{ $consultor->nombre_completo }}"
/>

@include('fac.consultores.partials._wizard', ['step' => 2, 'consultor' => $consultor])

<form method="POST" action="{{ route('fac.consultores.contacto.update', $consultor) }}">
    @csrf

    <div class="fepade-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Correos electrónicos</h5>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="agregarEmail()">
                Agregar correo
            </button>
        </div>

        <div id="emails-wrapper">
            @forelse($consultor->emails as $index => $email)
                <div class="row g-3 mb-3 item-email">
                    <div class="col-md-8">
                        <input type="email" name="emails[{{ $index }}][email]" value="{{ $email->email }}" class="form-control" placeholder="correo@dominio.com">
                    </div>

                    <div class="col-md-3">
                        <div class="form-check mt-2">
                            <input type="checkbox" name="emails[{{ $index }}][principal]" value="1" class="form-check-input" {{ $email->principal ? 'checked' : '' }}>
                            <label class="form-check-label">Principal</label>
                        </div>
                    </div>

                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">X</button>
                    </div>
                </div>
            @empty
                <div class="row g-3 mb-3 item-email">
                    <div class="col-md-8">
                        <input type="email" name="emails[0][email]" class="form-control" placeholder="correo@dominio.com">
                    </div>

                    <div class="col-md-3">
                        <div class="form-check mt-2">
                            <input type="checkbox" name="emails[0][principal]" value="1" class="form-check-input">
                            <label class="form-check-label">Principal</label>
                        </div>
                    </div>

                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">X</button>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="fepade-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Teléfonos</h5>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="agregarTelefono()">
                Agregar teléfono
            </button>
        </div>

        <div id="telefonos-wrapper">
            @forelse($consultor->telefonos as $index => $telefono)
                <div class="row g-3 mb-3 item-telefono">
                    <div class="col-md-4">
                        <select name="telefonos[{{ $index }}][id_tipo_telefono]" class="form-select">
                            <option value="">Tipo</option>
                            @foreach($catalogos['tiposTelefono'] as $tipo)
                                <option value="{{ $tipo->id_tipo_telefono }}" {{ $telefono->id_tipo_telefono == $tipo->id_tipo_telefono ? 'selected' : '' }}>
                                    {{ $tipo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <input type="text" name="telefonos[{{ $index }}][numero_telefono]" value="{{ $telefono->numero_telefono }}" class="form-control" placeholder="Número">
                    </div>

                    <div class="col-md-3">
                        <input type="text" name="telefonos[{{ $index }}][extension]" value="{{ $telefono->extension }}" class="form-control" placeholder="Extensión">
                    </div>

                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">X</button>
                    </div>
                </div>
            @empty
                <div class="row g-3 mb-3 item-telefono">
                    <div class="col-md-4">
                        <select name="telefonos[0][id_tipo_telefono]" class="form-select">
                            <option value="">Tipo</option>
                            @foreach($catalogos['tiposTelefono'] as $tipo)
                                <option value="{{ $tipo->id_tipo_telefono }}">{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-4">
                        <input type="text" name="telefonos[0][numero_telefono]" class="form-control" placeholder="Número">
                    </div>

                    <div class="col-md-3">
                        <input type="text" name="telefonos[0][extension]" class="form-control" placeholder="Extensión">
                    </div>

                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">X</button>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="fepade-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Redes sociales / enlaces</h5>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="agregarRed()">
                Agregar red
            </button>
        </div>

        <div id="redes-wrapper">
            @forelse($consultor->redesSociales as $index => $red)
                <div class="row g-3 mb-3 item-red">
                    <div class="col-md-4">
                        <select name="redes[{{ $index }}][id_tipo_red_social]" class="form-select">
                            <option value="">Tipo</option>
                            @foreach($catalogos['tiposRedSocial'] as $tipo)
                                <option value="{{ $tipo->id_tipo_red_social }}" {{ $red->id_tipo_red_social == $tipo->id_tipo_red_social ? 'selected' : '' }}>
                                    {{ $tipo->nombre }}
                                </option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-7">
                        <input type="url" name="redes[{{ $index }}][enlace]" value="{{ $red->enlace }}" class="form-control" placeholder="https://...">
                    </div>

                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">X</button>
                    </div>
                </div>
            @empty
                <div class="row g-3 mb-3 item-red">
                    <div class="col-md-4">
                        <select name="redes[0][id_tipo_red_social]" class="form-select">
                            <option value="">Tipo</option>
                            @foreach($catalogos['tiposRedSocial'] as $tipo)
                                <option value="{{ $tipo->id_tipo_red_social }}">{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>

                    <div class="col-md-7">
                        <input type="url" name="redes[0][enlace]" class="form-control" placeholder="https://...">
                    </div>

                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">X</button>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="fepade-card mb-4">
        <div class="d-flex justify-content-between align-items-center mb-3">
            <h5 class="mb-0">Contactos de emergencia</h5>
            <button type="button" class="btn btn-sm btn-outline-primary" onclick="agregarEmergencia()">
                Agregar contacto
            </button>
        </div>

        <div id="emergencias-wrapper">
            @forelse($consultor->emergencias as $index => $emergencia)
                <div class="row g-3 mb-3 item-emergencia">
                    <div class="col-md-4">
                        <input type="text" name="emergencias[{{ $index }}][nombre]" value="{{ $emergencia->nombre }}" class="form-control" placeholder="Nombre">
                    </div>

                    <div class="col-md-3">
                        <input type="text" name="emergencias[{{ $index }}][telefono]" value="{{ $emergencia->telefono }}" class="form-control" placeholder="Teléfono">
                    </div>

                    <div class="col-md-4">
                        <input type="email" name="emergencias[{{ $index }}][correo]" value="{{ $emergencia->correo }}" class="form-control" placeholder="Correo">
                    </div>

                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">X</button>
                    </div>
                </div>
            @empty
                <div class="row g-3 mb-3 item-emergencia">
                    <div class="col-md-4">
                        <input type="text" name="emergencias[0][nombre]" class="form-control" placeholder="Nombre">
                    </div>

                    <div class="col-md-3">
                        <input type="text" name="emergencias[0][telefono]" class="form-control" placeholder="Teléfono">
                    </div>

                    <div class="col-md-4">
                        <input type="email" name="emergencias[0][correo]" class="form-control" placeholder="Correo">
                    </div>

                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">X</button>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="d-flex justify-content-between">
        <a href="{{ route('fac.consultores.edit', $consultor) }}" class="btn btn-outline-secondary">
            Volver a datos personales
        </a>

        <button type="submit" class="btn btn-fepade">
            Guardar y continuar
        </button>
    </div>
</form>

<script>
    let emailIndex = {{ max($consultor->emails->count(), 1) }};
    let telefonoIndex = {{ max($consultor->telefonos->count(), 1) }};
    let redIndex = {{ max($consultor->redesSociales->count(), 1) }};
    let emergenciaIndex = {{ max($consultor->emergencias->count(), 1) }};

    function eliminarFila(button) {
        button.closest('.row').remove();
    }

    function agregarEmail() {
        const wrapper = document.getElementById('emails-wrapper');

        wrapper.insertAdjacentHTML('beforeend', `
            <div class="row g-3 mb-3 item-email">
                <div class="col-md-8">
                    <input type="email" name="emails[${emailIndex}][email]" class="form-control" placeholder="correo@dominio.com">
                </div>
                <div class="col-md-3">
                    <div class="form-check mt-2">
                        <input type="checkbox" name="emails[${emailIndex}][principal]" value="1" class="form-check-input">
                        <label class="form-check-label">Principal</label>
                    </div>
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">X</button>
                </div>
            </div>
        `);

        emailIndex++;
    }

    function agregarTelefono() {
        const wrapper = document.getElementById('telefonos-wrapper');

        wrapper.insertAdjacentHTML('beforeend', `
            <div class="row g-3 mb-3 item-telefono">
                <div class="col-md-4">
                    <select name="telefonos[${telefonoIndex}][id_tipo_telefono]" class="form-select">
                        <option value="">Tipo</option>
                        @foreach($catalogos['tiposTelefono'] as $tipo)
                            <option value="{{ $tipo->id_tipo_telefono }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-4">
                    <input type="text" name="telefonos[${telefonoIndex}][numero_telefono]" class="form-control" placeholder="Número">
                </div>
                <div class="col-md-3">
                    <input type="text" name="telefonos[${telefonoIndex}][extension]" class="form-control" placeholder="Extensión">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">X</button>
                </div>
            </div>
        `);

        telefonoIndex++;
    }

    function agregarRed() {
        const wrapper = document.getElementById('redes-wrapper');

        wrapper.insertAdjacentHTML('beforeend', `
            <div class="row g-3 mb-3 item-red">
                <div class="col-md-4">
                    <select name="redes[${redIndex}][id_tipo_red_social]" class="form-select">
                        <option value="">Tipo</option>
                        @foreach($catalogos['tiposRedSocial'] as $tipo)
                            <option value="{{ $tipo->id_tipo_red_social }}">{{ $tipo->nombre }}</option>
                        @endforeach
                    </select>
                </div>
                <div class="col-md-7">
                    <input type="url" name="redes[${redIndex}][enlace]" class="form-control" placeholder="https://...">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">X</button>
                </div>
            </div>
        `);

        redIndex++;
    }

    function agregarEmergencia() {
        const wrapper = document.getElementById('emergencias-wrapper');

        wrapper.insertAdjacentHTML('beforeend', `
            <div class="row g-3 mb-3 item-emergencia">
                <div class="col-md-4">
                    <input type="text" name="emergencias[${emergenciaIndex}][nombre]" class="form-control" placeholder="Nombre">
                </div>
                <div class="col-md-3">
                    <input type="text" name="emergencias[${emergenciaIndex}][telefono]" class="form-control" placeholder="Teléfono">
                </div>
                <div class="col-md-4">
                    <input type="email" name="emergencias[${emergenciaIndex}][correo]" class="form-control" placeholder="Correo">
                </div>
                <div class="col-md-1">
                    <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">X</button>
                </div>
            </div>
        `);

        emergenciaIndex++;
    }
</script>

@endsection