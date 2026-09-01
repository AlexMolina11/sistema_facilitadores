@extends('layouts.app')

@section('title', 'Consultores | Facilitadores FEPADE')
@section('page-title', 'Consultores')
@section('page-subtitle', 'Gestión visual de perfiles de consultores')

@section('content')

<section class="busqueda-hero mb-4">
    <div class="busqueda-hero-main">
        <span class="busqueda-hero-icon">
            <i class="fa-solid fa-list"></i>
        </span>

        <div>
            <h2>Listado de consultores FEPADE</h2>
            <p>Consulta, edita y gestiona los datos de los consultores.</p>
        </div>
    </div>

    <div class="busqueda-hero-actions">

        @permiso('fac.consultores.crear')
            <a
                href="{{ route('fac.consultores.create') }}"
                class="btn btn-outline-light"
            >
                <i class="fas fa-plus me-1"></i>
                Nuevo consultor
            </a>
        @endpermiso

    </div>
</section>

<div class="fepade-card mb-4">
    <form
        method="GET"
        action="{{ route('fac.consultores.index') }}"
        class="row g-3 align-items-end"
    >
        <div class="col-md-6">
            <label class="form-label">
                Buscar
            </label>

            <input
                type="text"
                name="buscar"
                value="{{ $buscar }}"
                class="form-control"
                placeholder="Nombre, apellido, DUI, NIT"
            >
        </div>

        <div class="col-md-3">
            <label class="form-label">
                Estado
            </label>

            <select
                name="estado"
                class="form-select"
            >
                <option value="">
                    Todos
                </option>

                <option
                    value="1"
                    {{ $estado === '1' ? 'selected' : '' }}
                >
                    Activos
                </option>

                <option
                    value="0"
                    {{ $estado === '0' ? 'selected' : '' }}
                >
                    Inactivos
                </option>
            </select>
        </div>

        <div class="col-md-3 d-flex gap-2">
            <button
                type="submit"
                class="btn btn-navy w-100"
            >
                Buscar
            </button>

            <a
                href="{{ route('fac.consultores.index') }}"
                class="btn btn-outline-secondary w-100"
            >
                Limpiar
            </a>
        </div>
    </form>
</div>

<div class="fepade-card">

    <div class="table-responsive">
        <table class="table align-middle">

            <thead>
                <tr>
                    <th>Consultor</th>
                    <th>Identificación</th>
                    <th>Nacionalidad</th>
                    <th>Estado</th>
                    <th>Perfil completado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>

            <tbody>

                @forelse($consultores as $consultor)

                    @php
                        $avancePerfil = $consultor->avancePerfil();

                        $porcentajePerfil =
                            $avancePerfil['porcentaje'] ?? 0;

                        $claseAvance = match (true) {
                            $porcentajePerfil >= 85 => 'bg-success',
                            $porcentajePerfil >= 60 => 'bg-warning',
                            default => 'bg-danger',
                        };

                        $documentoPrincipal =
                            $consultor->documentos->first();

                        $tipoDocumento =
                            $documentoPrincipal
                                ?->tipoDocumento
                                ?->nombre;

                        $tipoIdentificacion =
                            $tipoDocumento
                            ?? $consultor->tipo_identificacion
                            ?? 'Documento';

                        $numeroIdentificacion =
                            $documentoPrincipal?->numero
                            ?? $consultor->numero_identificacion
                            ?? null;
                    @endphp

                    <tr>

                        {{-- Consultor --}}
                        <td>
                            <div class="fw-semibold">
                                {{ $consultor->nombre_completo }}
                            </div>

                            <div class="text-muted small">
                                {{ $consultor->direccion_residencia ?? 'Sin dirección registrada' }}
                            </div>
                        </td>


                        {{-- Identificación --}}
                        <td>
                            <div>
                                {{ $tipoIdentificacion }}
                            </div>

                            <div class="text-muted small">
                                {{ $numeroIdentificacion ?? 'No registrado' }}
                            </div>
                        </td>


                        {{-- Nacionalidad --}}
                        <td>
                            {{ $consultor->nacionalidad ?? 'No registrada' }}
                        </td>


                        {{-- Estado --}}
                        <td>
                            @if($consultor->activo)

                                <span class="badge badge-success-soft">
                                    Activo
                                </span>

                            @else

                                <span class="badge badge-warning-soft">
                                    Inactivo
                                </span>

                            @endif
                        </td>


                        {{-- Perfil completado --}}
                        <td>
                            <div
                                class="mt-2"
                                style="max-width: 260px;"
                            >
                                <div
                                    class="d-flex justify-content-between align-items-center small mb-1"
                                >
                                    <span class="text-muted">
                                        Perfil completado
                                    </span>

                                    <strong>
                                        {{ $porcentajePerfil }}%
                                    </strong>
                                </div>

                                <div
                                    class="progress"
                                    style="height: 8px;"
                                >
                                    <div
                                        class="progress-bar {{ $claseAvance }}"
                                        role="progressbar"
                                        style="width: {{ $porcentajePerfil }}%;"
                                        aria-valuenow="{{ $porcentajePerfil }}"
                                        aria-valuemin="0"
                                        aria-valuemax="100"
                                    ></div>
                                </div>
                            </div>
                        </td>


                        {{-- Acciones --}}
                        <td class="text-end">

                            {{-- Invitaciones --}}
                            @permiso('seg.invitaciones.gestionar')

                                @if(!$consultor->usuario)

                                    @if($consultor->invitacionActiva)

                                        <a
                                            href="{{ route('seg.invitaciones.show', $consultor->invitacionActiva) }}"
                                            class="btn btn-sm btn-outline-success"
                                            title="Ver invitación activa"
                                        >
                                            <i class="fa-solid fa-link me-1"></i>
                                            Invitación activa
                                        </a>

                                    @else

                                        <button
                                            type="button"
                                            class="btn btn-sm btn-outline-primary"
                                            data-bs-toggle="modal"
                                            data-bs-target="#modalInvitacion{{ $consultor->id_consultor }}"
                                        >
                                            <i class="fa-solid fa-envelope me-1"></i>
                                            Generar invitación
                                        </button>

                                    @endif

                                @else

                                    <span
                                        class="badge text-bg-success"
                                        title="Este consultor ya posee acceso al sistema"
                                    >
                                        <i class="fa-solid fa-user-check me-1"></i>
                                        Con usuario
                                    </span>

                                @endif

                            @endpermiso


                            {{-- Ver --}}
                            @permiso('fac.consultores.ver')

                                <a
                                    href="{{ route('fac.consultores.show', $consultor) }}"
                                    class="btn btn-sm btn-outline-primary"
                                >
                                    Ver
                                </a>

                            @endpermiso


                            {{-- Editar --}}
                            @permiso('fac.consultores.editar')

                                <a
                                    href="{{ route('fac.consultores.edit', $consultor) }}"
                                    class="btn btn-sm btn-outline-secondary"
                                >
                                    Editar
                                </a>

                            @endpermiso


                            {{-- Eliminar --}}
                            @permiso('fac.consultores.eliminar')

                                <form
                                    action="{{ route('fac.consultores.destroy', $consultor) }}"
                                    method="POST"
                                    class="d-inline"
                                    onsubmit="return confirm('¿Deseas eliminar este consultor?')"
                                >
                                    @csrf
                                    @method('DELETE')

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-danger"
                                    >
                                        Eliminar
                                    </button>
                                </form>

                            @endpermiso

                        </td>
                    </tr>


                    {{-- Modal de invitación --}}
                    @permiso('seg.invitaciones.gestionar')

                        @if(!$consultor->usuario && !$consultor->invitacionActiva)

                            <tr class="d-none">
                                <td colspan="6">

                                    <div
                                        class="modal fade"
                                        id="modalInvitacion{{ $consultor->id_consultor }}"
                                        tabindex="-1"
                                        aria-labelledby="modalInvitacionLabel{{ $consultor->id_consultor }}"
                                        aria-hidden="true"
                                    >
                                        <div class="modal-dialog modal-dialog-centered">

                                            <div class="modal-content">

                                                <form
                                                    method="POST"
                                                    action="{{ route('seg.invitaciones.store', $consultor) }}"
                                                >
                                                    @csrf

                                                    <div class="modal-header">

                                                        <div>
                                                            <h5
                                                                class="modal-title"
                                                                id="modalInvitacionLabel{{ $consultor->id_consultor }}"
                                                            >
                                                                Generar invitación
                                                            </h5>

                                                            <small class="text-muted">
                                                                {{ $consultor->nombre_completo }}
                                                            </small>
                                                        </div>

                                                        <button
                                                            type="button"
                                                            class="btn-close"
                                                            data-bs-dismiss="modal"
                                                            aria-label="Cerrar"
                                                        ></button>

                                                    </div>


                                                    <div class="modal-body">

                                                        {{-- Duración --}}
                                                        <div class="mb-4">

                                                            <label
                                                                for="duracion_horas_{{ $consultor->id_consultor }}"
                                                                class="form-label fw-semibold"
                                                            >
                                                                Duración de la invitación
                                                            </label>

                                                            <div class="input-group">

                                                                <input
                                                                    type="number"
                                                                    class="form-control"
                                                                    id="duracion_horas_{{ $consultor->id_consultor }}"
                                                                    name="duracion_horas"
                                                                    value="24"
                                                                    min="1"
                                                                    max="8760"
                                                                >

                                                                <span class="input-group-text">
                                                                    horas
                                                                </span>

                                                            </div>

                                                            <div class="form-check mt-2">

                                                                <input
                                                                    class="form-check-input js-duracion-ilimitada"
                                                                    type="checkbox"
                                                                    value="1"
                                                                    name="duracion_ilimitada"
                                                                    id="duracion_ilimitada_{{ $consultor->id_consultor }}"
                                                                    data-target="duracion_horas_{{ $consultor->id_consultor }}"
                                                                >

                                                                <label
                                                                    class="form-check-label"
                                                                    for="duracion_ilimitada_{{ $consultor->id_consultor }}"
                                                                >
                                                                    Sin límite de tiempo
                                                                </label>

                                                            </div>

                                                        </div>


                                                        {{-- Máximo de usos --}}
                                                        <div class="mb-3">

                                                            <label
                                                                for="max_usos_{{ $consultor->id_consultor }}"
                                                                class="form-label fw-semibold"
                                                            >
                                                                Máximo de usos
                                                            </label>

                                                            <input
                                                                type="number"
                                                                class="form-control"
                                                                id="max_usos_{{ $consultor->id_consultor }}"
                                                                name="max_usos"
                                                                value="1"
                                                                min="1"
                                                                max="1000"
                                                            >

                                                            <div class="form-check mt-2">

                                                                <input
                                                                    class="form-check-input js-usos-ilimitados"
                                                                    type="checkbox"
                                                                    value="1"
                                                                    name="usos_ilimitados"
                                                                    id="usos_ilimitados_{{ $consultor->id_consultor }}"
                                                                    data-target="max_usos_{{ $consultor->id_consultor }}"
                                                                >

                                                                <label
                                                                    class="form-check-label"
                                                                    for="usos_ilimitados_{{ $consultor->id_consultor }}"
                                                                >
                                                                    Usos ilimitados
                                                                </label>

                                                            </div>

                                                        </div>


                                                        <div class="alert alert-light border mb-0">

                                                            <i class="fa-solid fa-circle-info me-1"></i>

                                                            La invitación quedará asociada únicamente a este consultor
                                                            y asignará automáticamente el rol
                                                            <strong>Consultor</strong>.

                                                        </div>

                                                    </div>


                                                    <div class="modal-footer">

                                                        <button
                                                            type="button"
                                                            class="btn btn-outline-secondary"
                                                            data-bs-dismiss="modal"
                                                        >
                                                            Cancelar
                                                        </button>

                                                        <button
                                                            type="submit"
                                                            class="btn btn-fepade"
                                                        >
                                                            <i class="fa-solid fa-link me-1"></i>
                                                            Crear invitación
                                                        </button>

                                                    </div>

                                                </form>

                                            </div>

                                        </div>
                                    </div>

                                </td>
                            </tr>

                        @endif

                    @endpermiso

                @empty

                    <tr>
                        <td
                            colspan="6"
                            class="text-center text-muted py-4"
                        >
                            No hay consultores registrados.
                        </td>
                    </tr>

                @endforelse

            </tbody>

        </table>
    </div>


    <div class="mt-4 pagination-wrapper">
        {{ $consultores->links('pagination::bootstrap-5') }}
    </div>

</div>

@endsection


@push('scripts')

<script>
document.addEventListener('DOMContentLoaded', function () {

    /*
    |--------------------------------------------------------------------------
    | Invitación sin límite de tiempo
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('.js-duracion-ilimitada')
        .forEach(function (checkbox) {

            checkbox.addEventListener('change', function () {

                const input = document.getElementById(
                    this.dataset.target
                );

                if (!input) {
                    return;
                }

                input.disabled = this.checked;

                if (this.checked) {
                    input.value = '';
                } else if (!input.value) {
                    input.value = 24;
                }

            });

        });


    /*
    |--------------------------------------------------------------------------
    | Invitación con usos ilimitados
    |--------------------------------------------------------------------------
    */

    document.querySelectorAll('.js-usos-ilimitados')
        .forEach(function (checkbox) {

            checkbox.addEventListener('change', function () {

                const input = document.getElementById(
                    this.dataset.target
                );

                if (!input) {
                    return;
                }

                input.disabled = this.checked;

                if (this.checked) {
                    input.value = '';
                } else if (!input.value) {
                    input.value = 1;
                }

            });

        });

});
</script>

@endpush
