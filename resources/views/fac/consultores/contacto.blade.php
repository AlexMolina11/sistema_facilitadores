@extends('layouts.app')

@php
    $esMiPerfil =
        filled(auth()->user()?->id_consultor)
        && (int) auth()->user()->id_consultor === (int) $consultor->id_consultor;
@endphp

@push('styles')
    <link rel="stylesheet" href="{{ asset('vendor/intl-tel-input/css/intlTelInput.min.css') }}">

    <style>
        .iti {
            width: 100%;
        }
    </style>
@endpush

@section(
    'title',
    $esMiPerfil
        ? 'Mi contacto | Facilitadores FEPADE'
        : 'Contacto | Facilitadores FEPADE'
)

@section(
    'page-title',
    $esMiPerfil
        ? 'Mi información de contacto'
        : 'Contacto del consultor'
)

@section(
    'page-subtitle',
    $esMiPerfil
        ? 'Administra tus correos, teléfonos, redes sociales y contactos de emergencia.'
        : 'Administra los medios de contacto del consultor.'
)

@section('content')

<!--<x-ui.page-header
    :title="$esMiPerfil
        ? 'Mi información de contacto'
        : 'Información de contacto'"
    :subtitle="$esMiPerfil
        ? 'Mantén actualizados tus medios de contacto y la información para emergencias.'
        : 'Mantén actualizados los medios de contacto y la información de emergencia del consultor.'"
/>-->

@include('fac.consultores.partials._wizard', ['step' => 2, 'consultor' => $consultor])


{{-- ============================================================
    CORREOS ELECTRÓNICOS
============================================================ --}}

<div class="perfil-panel mb-4">

    <div class="perfil-section-header">
        <div>
            <h4>Correos electrónicos</h4>
            <p class="text-muted mb-0">
                Registra los correos de contacto y marca uno como principal.
            </p>
        </div>

        <button
            class="btn btn-fepade"
            type="button"
            onclick="mostrarFormularioPerfil('form-email-nuevo')">
            + Añadir correo
        </button>
    </div>


    {{-- NUEVO CORREO --}}
    <div id="form-email-nuevo" class="perfil-form-wrapper d-none">

        <form
            method="POST"
            action="{{ route('fac.consultores.contacto.emails.store', $consultor) }}"
            class="border rounded p-3 bg-light">

            @csrf

            <div class="row g-3 align-items-end">

                <div class="col-md-7">
                    <label class="form-label">Correo electrónico</label>

                    <input
                        type="email"
                        name="email"
                        class="form-control"
                        maxlength="150"
                        pattern="[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}"
                        title="Ingresa un correo electrónico válido. Ejemplo: nombre@dominio.com"
                        placeholder="nombre@dominio.com"
                        required>
                </div>

                <div class="col-md-3">
                    <div class="form-check form-switch">
                        <input
                            class="form-check-input"
                            type="checkbox"
                            name="principal"
                            value="1"
                            id="email_principal_nuevo">

                        <label
                            class="form-check-label"
                            for="email_principal_nuevo">
                            Principal
                        </label>
                    </div>
                </div>

                <div class="col-md-2 d-flex gap-2">
                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        onclick="cerrarFormulariosPerfil()">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="btn btn-fepade">
                        Guardar
                    </button>
                </div>

            </div>
        </form>
    </div>


    {{-- CORREOS REGISTRADOS --}}
    @forelse($consultor->emails as $email)

        <article class="perfil-card-item">

            <div class="perfil-card-icon">✉</div>

            <div class="perfil-card-body">

                <div class="d-flex justify-content-between gap-3">

                    <div>
                        <div class="perfil-card-title">
                            {{ $email->email }}
                        </div>

                        <div class="perfil-card-meta">
                            {{ $email->principal ? 'Correo principal' : 'Correo secundario' }}
                        </div>
                    </div>

                    <div class="d-flex align-items-center gap-2 flex-shrink-0">

                        <button
                            class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1"
                            type="button"
                            title="Editar correo"
                            aria-label="Editar correo"
                            onclick="mostrarFormularioPerfil('form-email-{{ $email->id_email }}')">

                            <i class="fa-solid fa-pen"></i>
                            <span>Editar</span>

                        </button>

                        <form
                            method="POST"
                            action="{{ route('fac.consultores.contacto.emails.destroy', [$consultor, $email]) }}"
                            class="m-0"
                            onsubmit="return confirm('¿Deseas eliminar este correo?')">

                            @csrf
                            @method('DELETE')

                            <button
                                class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1"
                                type="submit"
                                title="Eliminar correo"
                                aria-label="Eliminar correo">

                                <i class="fa-solid fa-trash"></i>
                                <span>Eliminar</span>

                            </button>

                        </form>

                    </div>
                </div>


                {{-- EDITAR CORREO --}}
                <div
                    id="form-email-{{ $email->id_email }}"
                    class="perfil-form-wrapper d-none mt-3">

                    <form
                        method="POST"
                        action="{{ route('fac.consultores.contacto.emails.update', [$consultor, $email]) }}"
                        class="border rounded p-3 bg-light">

                        @csrf
                        @method('PUT')

                        <div class="row g-3 align-items-end">

                            <div class="col-md-7">

                                <label class="form-label">
                                    Correo electrónico
                                </label>

                                <input
                                    type="email"
                                    name="email"
                                    value="{{ old('email', $email->email) }}"
                                    class="form-control"
                                    maxlength="150"
                                    pattern="[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}"
                                    title="Ingresa un correo electrónico válido. Ejemplo: nombre@dominio.com"
                                    placeholder="nombre@dominio.com"
                                    required>

                            </div>

                            <div class="col-md-3">

                                <div class="form-check form-switch">

                                    <input
                                        class="form-check-input"
                                        type="checkbox"
                                        name="principal"
                                        value="1"
                                        {{ $email->principal ? 'checked' : '' }}>

                                    <label class="form-check-label">
                                        Principal
                                    </label>

                                </div>

                            </div>

                            <div class="col-md-2 d-flex gap-2">

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    onclick="cerrarFormulariosPerfil()">
                                    Cancelar
                                </button>

                                <button
                                    type="submit"
                                    class="btn btn-fepade">
                                    Actualizar
                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </article>

    @empty

        <div class="text-muted border rounded p-4">
            No hay correos registrados.
        </div>

    @endforelse

</div>


{{-- ============================================================
    TELÉFONOS
============================================================ --}}

<div class="perfil-panel mb-4">

    <div class="perfil-section-header">

        <div>
            <h4>Teléfonos</h4>
            <p class="text-muted mb-0">
                Registra teléfonos personales, institucionales o de contacto.
            </p>
        </div>

        <button
            class="btn btn-fepade"
            type="button"
            onclick="mostrarFormularioPerfil('form-telefono-nuevo')">
            + Añadir teléfono
        </button>

    </div>


    {{-- NUEVO TELÉFONO --}}
    <div
        id="form-telefono-nuevo"
        class="perfil-form-wrapper d-none">

        <form
            method="POST"
            action="{{ route('fac.consultores.contacto.telefonos.store', $consultor) }}"
            class="border rounded p-3 bg-light">

            @csrf

            <div class="row g-3 align-items-end">

                <div class="col-md-3">

                    <label class="form-label">
                        Tipo
                    </label>

                    <select
                        name="id_tipo_telefono"
                        class="form-select"
                        required>

                        <option value="">
                            Seleccione
                        </option>

                        @foreach($catalogos['tiposTelefono'] as $tipo)

                            <option value="{{ $tipo->id_tipo_telefono }}">
                                {{ $tipo->nombre }}
                            </option>

                        @endforeach

                    </select>

                    <div class="form-text">
                        Selecciona el tipo de teléfono a registrar.
                    </div>

                </div>


                <div class="col-md-5">

                    <label class="form-label">
                        Número de teléfono
                    </label>

                    <input
                        type="tel"
                        name="numero_telefono"
                        class="form-control telefono-internacional"
                        maxlength="20"
                        inputmode="tel"
                        autocomplete="tel"
                        required>

                    <div class="form-text">
                        Selecciona el país e ingresa el número telefónico.
                    </div>

                </div>


                <div class="col-md-2">

                    <label class="form-label">
                        Extensión telefónica
                    </label>

                    <input
                        type="text"
                        name="extension"
                        class="form-control"
                        maxlength="10"
                        inputmode="numeric"
                        placeholder="Ej. 123">

                    <div class="form-text">
                        Opcional. Para centrales u oficinas.
                    </div>

                </div>


                <div class="col-md-2 d-flex gap-2">

                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        onclick="cerrarFormulariosPerfil()">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="btn btn-fepade">
                        Guardar
                    </button>

                </div>

            </div>

        </form>

    </div>


    {{-- TELÉFONOS REGISTRADOS --}}
    @forelse($consultor->telefonos as $telefono)

        @php
            $tipoTelefono =
                $catalogos['tiposTelefono']
                    ->firstWhere(
                        'id_tipo_telefono',
                        $telefono->id_tipo_telefono
                    );
        @endphp

        <article class="perfil-card-item">

            <div class="perfil-card-icon">
                ☎
            </div>

            <div class="perfil-card-body">

                <div class="d-flex justify-content-between gap-3">

                    <div>

                        <div class="perfil-card-title">
                            {{ $telefono->numero_telefono }}
                        </div>

                        <div class="perfil-card-meta">

                            {{ $tipoTelefono->nombre ?? 'Tipo no registrado' }}

                            @if($telefono->extension)
                                · Ext. {{ $telefono->extension }}
                            @endif

                        </div>

                    </div>


                    <div class="d-flex align-items-center gap-2 flex-shrink-0">

                        <button
                            class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1"
                            type="button"
                            title="Editar teléfono"
                            aria-label="Editar teléfono"
                            onclick="mostrarFormularioPerfil('form-telefono-{{ $telefono->id_consultor_telefono }}')">

                            <i class="fa-solid fa-pen"></i>
                            <span>Editar</span>

                        </button>

                        <form
                            method="POST"
                            action="{{ route('fac.consultores.contacto.telefonos.destroy', [$consultor, $telefono]) }}"
                            class="m-0"
                            onsubmit="return confirm('¿Deseas eliminar este teléfono?')">

                            @csrf
                            @method('DELETE')

                            <button
                                class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1"
                                type="submit"
                                title="Eliminar teléfono"
                                aria-label="Eliminar teléfono">

                                <i class="fa-solid fa-trash"></i>
                                <span>Eliminar</span>

                            </button>

                        </form>

                    </div>

                </div>


                {{-- EDITAR TELÉFONO --}}
                <div
                    id="form-telefono-{{ $telefono->id_consultor_telefono }}"
                    class="perfil-form-wrapper d-none mt-3">

                    <form
                        method="POST"
                        action="{{ route('fac.consultores.contacto.telefonos.update', [$consultor, $telefono]) }}"
                        class="border rounded p-3 bg-light">

                        @csrf
                        @method('PUT')

                        <div class="row g-3 align-items-end">


                            <div class="col-md-3">

                                <label class="form-label">
                                    Tipo
                                </label>

                                <select
                                    name="id_tipo_telefono"
                                    class="form-select"
                                    required>

                                    <option value="">
                                        Seleccione
                                    </option>

                                    @foreach($catalogos['tiposTelefono'] as $tipo)

                                        <option
                                            value="{{ $tipo->id_tipo_telefono }}"
                                            {{ $telefono->id_tipo_telefono == $tipo->id_tipo_telefono ? 'selected' : '' }}>

                                            {{ $tipo->nombre }}

                                        </option>

                                    @endforeach

                                </select>

                                <div class="form-text">
                                    Selecciona el tipo de teléfono a registrar.
                                </div>

                            </div>


                            <div class="col-md-5">

                                <label class="form-label">
                                    Número de teléfono
                                </label>

                                <input
                                    type="tel"
                                    name="numero_telefono"
                                    value="{{ $telefono->numero_telefono }}"
                                    class="form-control telefono-internacional"
                                    maxlength="20"
                                    inputmode="tel"
                                    autocomplete="tel"
                                    required>

                                <div class="form-text">
                                    Selecciona el país e ingresa el número telefónico.
                                </div>

                            </div>


                            <div class="col-md-2">

                                <label class="form-label">
                                    Extensión telefónica
                                </label>

                                <input
                                    type="text"
                                    name="extension"
                                    value="{{ $telefono->extension }}"
                                    class="form-control"
                                    maxlength="10"
                                    inputmode="numeric"
                                    placeholder="Ej. 123">

                                <div class="form-text">
                                    Opcional. Para centrales u oficinas.
                                </div>

                            </div>


                            <div class="col-md-2 d-flex gap-2">

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    onclick="cerrarFormulariosPerfil()">
                                    Cancelar
                                </button>

                                <button
                                    type="submit"
                                    class="btn btn-fepade">
                                    Actualizar
                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </article>

    @empty

        <div class="text-muted border rounded p-4">
            No hay teléfonos registrados.
        </div>

    @endforelse

</div>


{{-- ============================================================
    REDES SOCIALES / ENLACES
============================================================ --}}

<div class="perfil-panel mb-4">

    <div class="perfil-section-header">

        <div>
            <h4>Redes sociales / enlaces</h4>

            <p class="text-muted mb-0">
                Agrega LinkedIn, portafolios, sitios web u otros enlaces profesionales.
            </p>
        </div>

        <button
            class="btn btn-fepade"
            type="button"
            onclick="mostrarFormularioPerfil('form-red-nueva')">
            + Añadir enlace
        </button>

    </div>


    <div
        id="form-red-nueva"
        class="perfil-form-wrapper d-none">

        <form
            method="POST"
            action="{{ route('fac.consultores.contacto.redes.store', $consultor) }}"
            class="border rounded p-3 bg-light">

            @csrf

            <div class="row g-3 align-items-end">

                <div class="col-md-4">

                    <label class="form-label">
                        Tipo
                    </label>

                    <select
                        name="id_tipo_red_social"
                        class="form-select"
                        required>

                        <option value="">
                            Seleccione
                        </option>

                        @foreach($catalogos['tiposRedSocial'] as $tipo)

                            <option value="{{ $tipo->id_tipo_red_social }}">
                                {{ $tipo->nombre }}
                            </option>

                        @endforeach

                    </select>

                </div>


                <div class="col-md-6">

                    <label class="form-label">
                        Enlace
                    </label>

                    <input
                        type="url"
                        name="enlace"
                        class="form-control"
                        placeholder="https://..."
                        required>

                </div>


                <div class="col-md-2 d-flex gap-2">

                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        onclick="cerrarFormulariosPerfil()">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="btn btn-fepade">
                        Guardar
                    </button>

                </div>

            </div>

        </form>

    </div>


    @forelse($consultor->redesSociales as $red)

        @php
            $tipoRed =
                $catalogos['tiposRedSocial']
                    ->firstWhere(
                        'id_tipo_red_social',
                        $red->id_tipo_red_social
                    );
        @endphp

        <article class="perfil-card-item">

            <div class="perfil-card-icon">
                🔗
            </div>

            <div class="perfil-card-body">

                <div class="d-flex justify-content-between gap-3">

                    <div>

                        <div class="perfil-card-title">
                            {{ $tipoRed->nombre ?? 'Enlace' }}
                        </div>

                        <a
                            href="{{ $red->enlace }}"
                            target="_blank"
                            rel="noopener noreferrer">
                            {{ $red->enlace }}
                        </a>

                    </div>


                    <div class="d-flex align-items-center gap-2 flex-shrink-0">

                        <button
                            class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1"
                            type="button"
                            title="Editar enlace"
                            aria-label="Editar enlace"
                            onclick="mostrarFormularioPerfil('form-red-{{ $red->id_consultor_red_social }}')">

                            <i class="fa-solid fa-pen"></i>
                            <span>Editar</span>

                        </button>


                        <form
                            method="POST"
                            action="{{ route('fac.consultores.contacto.redes.destroy', [$consultor, $red]) }}"
                            class="m-0"
                            onsubmit="return confirm('¿Deseas eliminar este enlace?')">

                            @csrf
                            @method('DELETE')

                            <button
                                class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1"
                                type="submit"
                                title="Eliminar enlace"
                                aria-label="Eliminar enlace">

                                <i class="fa-solid fa-trash"></i>
                                <span>Eliminar</span>

                            </button>

                        </form>

                    </div>

                </div>


                {{-- EDITAR RED SOCIAL / ENLACE --}}
                <div
                    id="form-red-{{ $red->id_consultor_red_social }}"
                    class="perfil-form-wrapper d-none mt-3">

                    <form
                        method="POST"
                        action="{{ route('fac.consultores.contacto.redes.update', [$consultor, $red]) }}"
                        class="border rounded p-3 bg-light">

                        @csrf
                        @method('PUT')

                        <div class="row g-3 align-items-end">

                            <div class="col-md-4">

                                <label class="form-label">
                                    Tipo
                                </label>

                                <select
                                    name="id_tipo_red_social"
                                    class="form-select"
                                    required>

                                    <option value="">
                                        Seleccione
                                    </option>

                                    @foreach($catalogos['tiposRedSocial'] as $tipo)

                                        <option
                                            value="{{ $tipo->id_tipo_red_social }}"
                                            {{ $red->id_tipo_red_social == $tipo->id_tipo_red_social ? 'selected' : '' }}>

                                            {{ $tipo->nombre }}

                                        </option>

                                    @endforeach

                                </select>

                            </div>


                            <div class="col-md-6">

                                <label class="form-label">
                                    Enlace
                                </label>

                                <input
                                    type="url"
                                    name="enlace"
                                    value="{{ $red->enlace }}"
                                    class="form-control"
                                    maxlength="500"
                                    placeholder="https://..."
                                    required>

                            </div>


                            <div class="col-md-2 d-flex gap-2">

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    onclick="cerrarFormulariosPerfil()">
                                    Cancelar
                                </button>

                                <button
                                    type="submit"
                                    class="btn btn-fepade">
                                    Actualizar
                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </article>

    @empty

        <div class="text-muted border rounded p-4">
            No hay enlaces registrados.
        </div>

    @endforelse

</div>


{{-- ============================================================
    CONTACTO DE EMERGENCIA
============================================================ --}}

<div class="perfil-panel mb-4">

    <div class="perfil-section-header">

        <div>
            <h4>Contacto de emergencia</h4>

            <p class="text-muted mb-0">
                Registra contactos para emergencias.
            </p>
        </div>

        <button
            class="btn btn-fepade"
            type="button"
            onclick="mostrarFormularioPerfil('form-emergencia-nueva')">
            + Añadir contacto
        </button>

    </div>


    {{-- NUEVO CONTACTO DE EMERGENCIA --}}
    <div
        id="form-emergencia-nueva"
        class="perfil-form-wrapper d-none">

        <form
            method="POST"
            action="{{ route('fac.consultores.contacto.emergencias.store', $consultor) }}"
            class="border rounded p-3 bg-light">

            @csrf

            <div class="row g-3 align-items-end">

                <div class="col-md-4">

                    <label class="form-label">
                        Nombre
                    </label>

                    <input
                        type="text"
                        name="nombre"
                        class="form-control"
                        maxlength="150"
                        required>

                    <div class="form-text">
                        Ingresa el nombre de la persona a contactar.
                    </div>

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Teléfono
                    </label>

                    <input
                        type="tel"
                        name="telefono"
                        class="form-control telefono-internacional"
                        maxlength="20"
                        inputmode="tel"
                        autocomplete="tel"
                        required>

                    <div class="form-text">
                        Selecciona el país e ingresa el número telefónico.
                    </div>

                </div>


                <div class="col-md-3">

                    <label class="form-label">
                        Correo
                    </label>

                    <input
                        type="email"
                        name="correo"
                        class="form-control"
                        maxlength="120"
                        pattern="[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}"
                        title="Ingresa un correo electrónico válido. Ejemplo: nombre@dominio.com"
                        placeholder="nombre@dominio.com">

                    <div class="form-text">
                        Ingresa el correo de la persona a contactar.
                    </div>

                </div>


                <div class="col-md-2 d-flex gap-2">

                    <button
                        type="button"
                        class="btn btn-outline-secondary"
                        onclick="cerrarFormulariosPerfil()">
                        Cancelar
                    </button>

                    <button
                        type="submit"
                        class="btn btn-fepade">
                        Guardar
                    </button>

                </div>

            </div>

        </form>

    </div>


    {{-- CONTACTOS DE EMERGENCIA REGISTRADOS --}}
    @forelse($consultor->emergencias as $emergencia)

        <article class="perfil-card-item">

            <div class="perfil-card-icon">
                ☂
            </div>

            <div class="perfil-card-body">

                <div class="d-flex justify-content-between gap-3">

                    <div>

                        <div class="perfil-card-title">
                            {{ $emergencia->nombre }}
                        </div>

                        <div class="perfil-card-meta">

                            {{ $emergencia->telefono }}

                            @if($emergencia->correo)
                                · {{ $emergencia->correo }}
                            @endif

                        </div>

                    </div>


                    <div class="d-flex align-items-center gap-2 flex-shrink-0">

                        <button
                            class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1"
                            type="button"
                            title="Editar contacto de emergencia"
                            aria-label="Editar contacto de emergencia"
                            onclick="mostrarFormularioPerfil('form-emergencia-{{ $emergencia->id_consultor_emergencia }}')">

                            <i class="fa-solid fa-pen"></i>
                            <span>Editar</span>

                        </button>


                        <form
                            method="POST"
                            action="{{ route('fac.consultores.contacto.emergencias.destroy', [$consultor, $emergencia]) }}"
                            class="m-0"
                            onsubmit="return confirm('¿Deseas eliminar este contacto?')">

                            @csrf
                            @method('DELETE')

                            <button
                                class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1"
                                type="submit"
                                title="Eliminar contacto de emergencia"
                                aria-label="Eliminar contacto de emergencia">

                                <i class="fa-solid fa-trash"></i>
                                <span>Eliminar</span>

                            </button>

                        </form>

                    </div>

                </div>


                {{-- EDITAR CONTACTO DE EMERGENCIA --}}
                <div
                    id="form-emergencia-{{ $emergencia->id_consultor_emergencia }}"
                    class="perfil-form-wrapper d-none mt-3">

                    <form
                        method="POST"
                        action="{{ route('fac.consultores.contacto.emergencias.update', [$consultor, $emergencia]) }}"
                        class="border rounded p-3 bg-light">

                        @csrf
                        @method('PUT')

                        <div class="row g-3 align-items-end">

                            <div class="col-md-4">

                                <label class="form-label">
                                    Nombre
                                </label>

                                <input
                                    type="text"
                                    name="nombre"
                                    value="{{ $emergencia->nombre }}"
                                    class="form-control"
                                    maxlength="150"
                                    required>

                                <div class="form-text">
                                    Modifica el nombre de la persona a contactar.
                                </div>

                            </div>


                            <div class="col-md-3">

                                <label class="form-label">
                                    Teléfono
                                </label>

                                <input
                                    type="tel"
                                    name="telefono"
                                    value="{{ $emergencia->telefono }}"
                                    class="form-control telefono-internacional"
                                    maxlength="20"
                                    inputmode="tel"
                                    autocomplete="tel"
                                    required>

                                <div class="form-text">
                                    Selecciona el país e ingresa el número telefónico.
                                </div>

                            </div>


                            <div class="col-md-3">

                                <label class="form-label">
                                    Correo
                                </label>

                                <input
                                    type="email"
                                    name="correo"
                                    value="{{ $emergencia->correo }}"
                                    class="form-control"
                                    maxlength="120"
                                    pattern="[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}"
                                    title="Ingresa un correo electrónico válido. Ejemplo: nombre@dominio.com"
                                    placeholder="nombre@dominio.com">

                                <div class="form-text">
                                    Modifica el correo de la persona a contactar.
                                </div>

                            </div>


                            <div class="col-md-2 d-flex gap-2">

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary"
                                    onclick="cerrarFormulariosPerfil()">
                                    Cancelar
                                </button>

                                <button
                                    type="submit"
                                    class="btn btn-fepade">
                                    Actualizar
                                </button>

                            </div>

                        </div>

                    </form>

                </div>

            </div>

        </article>

    @empty

        <div class="text-muted border rounded p-4">
            No hay contactos de emergencia registrados.
        </div>

    @endforelse

</div>


@include(
    'fac.consultores.partials._wizard_actions',
    [
        'consultor' => $consultor,

        'anterior' =>
            route(
                'fac.consultores.edit',
                $consultor
            ),

        'continuarRoute' =>
            route(
                'fac.consultores.contacto.continuar',
                $consultor
            ),

        'continuarLabel' =>
            'Continuar a Experiencia',
    ]
)

@endsection


@push('scripts')

<script src="{{ asset('vendor/intl-tel-input/js/intlTelInputWithUtils.min.js') }}"></script>

<script>

    function mostrarFormularioPerfil(id) {

        document
            .querySelectorAll('.perfil-form-wrapper')
            .forEach(el => el.classList.add('d-none'));

        const target =
            document.getElementById(id);

        if (target) {
            target.classList.remove('d-none');
        }
    }


    function cerrarFormulariosPerfil() {

        document
            .querySelectorAll('.perfil-form-wrapper')
            .forEach(el => el.classList.add('d-none'));
    }


    document.addEventListener(
        'DOMContentLoaded',
        function () {

            document
                .querySelectorAll('.telefono-internacional')
                .forEach(function (input) {

                    const iti =
                        window.intlTelInput(
                            input,
                            {
                                initialCountry: 'sv',

                                preferredCountries: [
                                    'sv',
                                    'gt',
                                    'hn',
                                    'ni',
                                    'cr',
                                    'us',
                                    'mx'
                                ],

                                nationalMode: true,

                                separateDialCode: true,

                                strictMode: true
                            }
                        );


                    /*
                    |--------------------------------------------------------------------------
                    | Números ya existentes
                    |--------------------------------------------------------------------------
                    |
                    | Si el número almacenado comienza con +,
                    | intl-tel-input detectará automáticamente
                    | el país correspondiente.
                    |
                    */

                    if (
                        input.value
                        && input.value.trim().startsWith('+')
                    ) {

                        iti.setNumber(
                            input.value.trim()
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Validación y normalización antes del envío
                    |--------------------------------------------------------------------------
                    */

                    const form =
                        input.closest('form');


                    if (form) {

                        form.addEventListener(
                            'submit',
                            function (event) {

                                input.setCustomValidity('');


                                if (!iti.isValidNumber()) {

                                    event.preventDefault();

                                    event.stopPropagation();

                                    input.setCustomValidity(
                                        'Ingresa un número de teléfono válido para el país seleccionado.'
                                    );

                                    input.reportValidity();

                                    return;
                                }


                                /*
                                |--------------------------------------------------------------
                                | Laravel recibirá el teléfono normalizado
                                |--------------------------------------------------------------
                                |
                                | Ejemplo visual:
                                | 7777-7777
                                |
                                | Valor enviado:
                                | +50377777777
                                |
                                */

                                input.value =
                                    iti.getNumber();
                            }
                        );
                    }


                    /*
                    |--------------------------------------------------------------------------
                    | Limpiar mensajes al modificar el teléfono
                    |--------------------------------------------------------------------------
                    */

                    input.addEventListener(
                        'input',
                        function () {

                            input.setCustomValidity('');
                        }
                    );


                    input.addEventListener(
                        'countrychange',
                        function () {

                            input.setCustomValidity('');
                        }
                    );

                });

        }
    );

</script>

@endpush