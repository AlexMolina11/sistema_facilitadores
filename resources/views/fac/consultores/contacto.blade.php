@extends('layouts.app')

@section('title', 'Contacto consultor | Facilitadores FEPADE')
@section('page-title', 'Contacto del consultor')
@section('page-subtitle', 'Correos, teléfonos, redes sociales y emergencia')

@section('content')

<x-ui.page-header 
    title="Contacto del consultor"
    subtitle="{{ $consultor->nombre_completo }}"
>
    <a href="{{ route('fac.consultores.edit', $consultor) }}" class="btn btn-outline-secondary">
        Volver a datos personales
    </a>
</x-ui.page-header>

@include('fac.consultores.partials._wizard', ['step' => 2, 'consultor' => $consultor])

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

<form method="POST" action="{{ route('fac.consultores.contacto.update', $consultor) }}">
    @csrf

    <div class="contacto-section">
        <div class="contacto-section-header">
            <div>
                <h4>Correos electrónicos</h4>
                <p>Registra uno o más correos y marca únicamente uno como principal.</p>
            </div>

            <button type="button" class="btn btn-fepade" onclick="agregarEmail()">
                + Añadir correo
            </button>
        </div>

        <div id="emails-wrapper">
            @forelse($consultor->emails as $index => $email)
                <div class="contacto-card item-email">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label">Correo electrónico</label>
                            <input 
                                type="email" 
                                name="emails[{{ $index }}][email]" 
                                value="{{ old("emails.$index.email", $email->email) }}" 
                                class="form-control"
                                placeholder="nombre@dominio.com"
                            >
                        </div>

                        <div class="col-md-3">
                            <div class="form-check form-switch">
                                <input 
                                    type="checkbox" 
                                    name="emails[{{ $index }}][principal]" 
                                    value="1" 
                                    class="form-check-input correo-principal-check"
                                    {{ old("emails.$index.principal", $email->principal) ? 'checked' : '' }}
                                    onchange="validarCorreoPrincipal(this)"
                                >
                                <label class="form-check-label">Correo principal</label>
                            </div>
                        </div>

                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">
                                X
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="contacto-card item-email">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-8">
                            <label class="form-label">Correo electrónico</label>
                            <input 
                                type="email" 
                                name="emails[0][email]" 
                                class="form-control"
                                placeholder="nombre@dominio.com"
                            >
                        </div>

                        <div class="col-md-3">
                            <div class="form-check form-switch">
                                <input 
                                    type="checkbox" 
                                    name="emails[0][principal]" 
                                    value="1" 
                                    class="form-check-input correo-principal-check"
                                    onchange="validarCorreoPrincipal(this)"
                                >
                                <label class="form-check-label">Correo principal</label>
                            </div>
                        </div>

                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">
                                X
                            </button>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="contacto-section">
        <div class="contacto-section-header">
            <div>
                <h4>Teléfonos</h4>
                <p>Registra teléfonos personales, institucionales o de contacto.</p>
            </div>

            <button type="button" class="btn btn-fepade" onclick="agregarTelefono()">
                + Añadir teléfono
            </button>
        </div>

        <div id="telefonos-wrapper">
            @forelse($consultor->telefonos as $index => $telefono)
                <div class="contacto-card item-telefono">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Tipo</label>
                            <select name="telefonos[{{ $index }}][id_tipo_telefono]" class="form-select">
                                <option value="">Seleccione</option>
                                @foreach($catalogos['tiposTelefono'] as $tipo)
                                    <option value="{{ $tipo->id_tipo_telefono }}" {{ old("telefonos.$index.id_tipo_telefono", $telefono->id_tipo_telefono) == $tipo->id_tipo_telefono ? 'selected' : '' }}>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Número telefónico</label>
                            <input 
                                type="text" 
                                name="telefonos[{{ $index }}][numero_telefono]" 
                                value="{{ old("telefonos.$index.numero_telefono", $telefono->numero_telefono) }}" 
                                class="form-control"
                                placeholder="7777-7777"
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Extensión</label>
                            <input 
                                type="text" 
                                name="telefonos[{{ $index }}][extension]" 
                                value="{{ old("telefonos.$index.extension", $telefono->extension) }}" 
                                class="form-control"
                            >
                        </div>

                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">
                                X
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="contacto-card item-telefono">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Tipo</label>
                            <select name="telefonos[0][id_tipo_telefono]" class="form-select">
                                <option value="">Seleccione</option>
                                @foreach($catalogos['tiposTelefono'] as $tipo)
                                    <option value="{{ $tipo->id_tipo_telefono }}">{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Número telefónico</label>
                            <input 
                                type="text" 
                                name="telefonos[0][numero_telefono]" 
                                class="form-control"
                                placeholder="7777-7777"
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Extensión</label>
                            <input 
                                type="text" 
                                name="telefonos[0][extension]" 
                                class="form-control"
                            >
                        </div>

                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">
                                X
                            </button>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="contacto-section">
        <div class="contacto-section-header">
            <div>
                <h4>Redes sociales / enlaces</h4>
                <p>Agrega LinkedIn, portafolios, sitios web u otros enlaces profesionales.</p>
            </div>

            <button type="button" class="btn btn-fepade" onclick="agregarRed()">
                + Añadir red
            </button>
        </div>

        <div id="redes-wrapper">
            @forelse($consultor->redesSociales as $index => $red)
                <div class="contacto-card item-red">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Tipo</label>
                            <select name="redes[{{ $index }}][id_tipo_red_social]" class="form-select">
                                <option value="">Seleccione</option>
                                @foreach($catalogos['tiposRedSocial'] as $tipo)
                                    <option value="{{ $tipo->id_tipo_red_social }}" {{ old("redes.$index.id_tipo_red_social", $red->id_tipo_red_social) == $tipo->id_tipo_red_social ? 'selected' : '' }}>
                                        {{ $tipo->nombre }}
                                    </option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-7">
                            <label class="form-label">Enlace</label>
                            <input 
                                type="url" 
                                name="redes[{{ $index }}][enlace]" 
                                value="{{ old("redes.$index.enlace", $red->enlace) }}" 
                                class="form-control"
                                placeholder="https://..."
                            >
                        </div>

                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">
                                X
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="contacto-card item-red">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Tipo</label>
                            <select name="redes[0][id_tipo_red_social]" class="form-select">
                                <option value="">Seleccione</option>
                                @foreach($catalogos['tiposRedSocial'] as $tipo)
                                    <option value="{{ $tipo->id_tipo_red_social }}">{{ $tipo->nombre }}</option>
                                @endforeach
                            </select>
                        </div>

                        <div class="col-md-7">
                            <label class="form-label">Enlace</label>
                            <input 
                                type="url" 
                                name="redes[0][enlace]" 
                                class="form-control"
                                placeholder="https://..."
                            >
                        </div>

                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">
                                X
                            </button>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="contacto-section">
        <div class="contacto-section-header">
            <div>
                <h4>Contactos de emergencia</h4>
                <p>Registra personas de contacto en caso de emergencia.</p>
            </div>

            <button type="button" class="btn btn-fepade" onclick="agregarEmergencia()">
                + Añadir contacto
            </button>
        </div>

        <div id="emergencias-wrapper">
            @forelse($consultor->emergencias as $index => $emergencia)
                <div class="contacto-card item-emergencia">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Nombre</label>
                            <input 
                                type="text" 
                                name="emergencias[{{ $index }}][nombre]" 
                                value="{{ old("emergencias.$index.nombre", $emergencia->nombre) }}" 
                                class="form-control"
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Teléfono</label>
                            <input 
                                type="text" 
                                name="emergencias[{{ $index }}][telefono]" 
                                value="{{ old("emergencias.$index.telefono", $emergencia->telefono) }}" 
                                class="form-control"
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Correo</label>
                            <input 
                                type="email" 
                                name="emergencias[{{ $index }}][correo]" 
                                value="{{ old("emergencias.$index.correo", $emergencia->correo) }}" 
                                class="form-control"
                                placeholder="nombre@dominio.com"
                            >
                        </div>

                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">
                                X
                            </button>
                        </div>
                    </div>
                </div>
            @empty
                <div class="contacto-card item-emergencia">
                    <div class="row g-3 align-items-end">
                        <div class="col-md-4">
                            <label class="form-label">Nombre</label>
                            <input 
                                type="text" 
                                name="emergencias[0][nombre]" 
                                class="form-control"
                            >
                        </div>

                        <div class="col-md-3">
                            <label class="form-label">Teléfono</label>
                            <input 
                                type="text" 
                                name="emergencias[0][telefono]" 
                                class="form-control"
                            >
                        </div>

                        <div class="col-md-4">
                            <label class="form-label">Correo</label>
                            <input 
                                type="email" 
                                name="emergencias[0][correo]" 
                                class="form-control"
                                placeholder="nombre@dominio.com"
                            >
                        </div>

                        <div class="col-md-1">
                            <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">
                                X
                            </button>
                        </div>
                    </div>
                </div>
            @endforelse
        </div>
    </div>

    <div class="d-flex justify-content-between mt-4">
        <a href="{{ route('fac.consultores.edit', $consultor) }}" class="btn btn-outline-secondary">
            Anterior: Datos personales
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
        button.closest('.contacto-card').remove();
    }

    function validarCorreoPrincipal(actual) {
        if (!actual.checked) {
            return;
        }

        document.querySelectorAll('.correo-principal-check').forEach(check => {
            if (check !== actual) {
                check.checked = false;
            }
        });
    }

    function agregarEmail() {
        document.getElementById('emails-wrapper').insertAdjacentHTML('beforeend', `
            <div class="contacto-card item-email">
                <div class="row g-3 align-items-end">
                    <div class="col-md-8">
                        <label class="form-label">Correo electrónico</label>
                        <input type="email" name="emails[${emailIndex}][email]" class="form-control" placeholder="nombre@dominio.com">
                    </div>
                    <div class="col-md-3">
                        <div class="form-check form-switch">
                            <input type="checkbox" name="emails[${emailIndex}][principal]" value="1" class="form-check-input correo-principal-check" onchange="validarCorreoPrincipal(this)">
                            <label class="form-check-label">Correo principal</label>
                        </div>
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">X</button>
                    </div>
                </div>
            </div>
        `);

        emailIndex++;
    }

    function agregarTelefono() {
        document.getElementById('telefonos-wrapper').insertAdjacentHTML('beforeend', `
            <div class="contacto-card item-telefono">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Tipo</label>
                        <select name="telefonos[${telefonoIndex}][id_tipo_telefono]" class="form-select">
                            <option value="">Seleccione</option>
                            @foreach($catalogos['tiposTelefono'] as $tipo)
                                <option value="{{ $tipo->id_tipo_telefono }}">{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Número telefónico</label>
                        <input type="text" name="telefonos[${telefonoIndex}][numero_telefono]" class="form-control" placeholder="7777-7777">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Extensión</label>
                        <input type="text" name="telefonos[${telefonoIndex}][extension]" class="form-control">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">X</button>
                    </div>
                </div>
            </div>
        `);

        telefonoIndex++;
    }

    function agregarRed() {
        document.getElementById('redes-wrapper').insertAdjacentHTML('beforeend', `
            <div class="contacto-card item-red">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Tipo</label>
                        <select name="redes[${redIndex}][id_tipo_red_social]" class="form-select">
                            <option value="">Seleccione</option>
                            @foreach($catalogos['tiposRedSocial'] as $tipo)
                                <option value="{{ $tipo->id_tipo_red_social }}">{{ $tipo->nombre }}</option>
                            @endforeach
                        </select>
                    </div>
                    <div class="col-md-7">
                        <label class="form-label">Enlace</label>
                        <input type="url" name="redes[${redIndex}][enlace]" class="form-control" placeholder="https://...">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">X</button>
                    </div>
                </div>
            </div>
        `);

        redIndex++;
    }

    function agregarEmergencia() {
        document.getElementById('emergencias-wrapper').insertAdjacentHTML('beforeend', `
            <div class="contacto-card item-emergencia">
                <div class="row g-3 align-items-end">
                    <div class="col-md-4">
                        <label class="form-label">Nombre</label>
                        <input type="text" name="emergencias[${emergenciaIndex}][nombre]" class="form-control">
                    </div>
                    <div class="col-md-3">
                        <label class="form-label">Teléfono</label>
                        <input type="text" name="emergencias[${emergenciaIndex}][telefono]" class="form-control">
                    </div>
                    <div class="col-md-4">
                        <label class="form-label">Correo</label>
                        <input type="email" name="emergencias[${emergenciaIndex}][correo]" class="form-control" placeholder="nombre@dominio.com">
                    </div>
                    <div class="col-md-1">
                        <button type="button" class="btn btn-outline-danger w-100" onclick="eliminarFila(this)">X</button>
                    </div>
                </div>
            </div>
        `);

        emergenciaIndex++;
    }
</script>

@endsection