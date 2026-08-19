@extends('layouts.app')

@section('title', 'Aceptación de términos | Facilitadores FEPADE')
@section('page-title', 'Bitácora de aceptación de términos')
@section('page-subtitle', 'Trazabilidad de términos y políticas aceptadas por consultores')

@section('content')

<div class="invitacion-page">

    <section class="invitacion-hero">

        <div class="invitacion-hero-main">

            <div class="invitacion-hero-icon">
                <i class="fa-solid fa-file-signature"></i>
            </div>

            <div>

                <div class="invitacion-kicker">
                    Seguridad · Bitácoras
                </div>

                <h2>
                    Aceptación de términos
                </h2>

                <div class="invitacion-hero-meta">
                    <span>
                        Registro histórico de aceptación de términos
                        y políticas de uso.
                    </span>
                </div>

            </div>

        </div>

    </section>


    <section class="invitacion-panel">

        <div class="invitacion-panel-header">

            <div class="invitacion-section-heading">

                <div class="invitacion-section-icon">
                    <i class="fa-solid fa-filter"></i>
                </div>

                <div>
                    <h4>Filtros</h4>
                    <p>
                        Consulta aceptaciones por consultor,
                        correo o versión.
                    </p>
                </div>

            </div>

        </div>


        <form
            method="GET"
            action="{{ route('seg.bitacora-terminos.index') }}"
            class="row g-3 align-items-end"
        >

            <div class="col-lg-6">

                <label class="form-label">
                    Buscar
                </label>

                <input
                    type="text"
                    name="buscar"
                    class="form-control"
                    value="{{ $buscar }}"
                    placeholder="Nombre, identificación o correo"
                >

            </div>


            <div class="col-lg-3">

                <label class="form-label">
                    Versión
                </label>

                <select
                    name="version"
                    class="form-select"
                >

                    <option value="">
                        Todas
                    </option>

                    @foreach($versiones as $item)

                        <option
                            value="{{ $item }}"
                            @selected($version === $item)
                        >
                            {{ $item }}
                        </option>

                    @endforeach

                </select>

            </div>


            <div class="col-lg-3">

                <div class="fepade-filter-actions">

                    <button
                        type="submit"
                        class="btn btn-fepade flex-fill"
                    >
                        <i class="fa-solid fa-magnifying-glass me-1"></i>
                        Buscar
                    </button>

                    <a
                        href="{{ route('seg.bitacora-terminos.index') }}"
                        class="btn btn-outline-secondary flex-fill"
                    >
                        Limpiar
                    </a>

                </div>

            </div>

        </form>

    </section>


    <section class="invitacion-panel">

        <div class="invitacion-panel-header">

            <div class="invitacion-section-heading">

                <div class="invitacion-section-icon">
                    <i class="fa-solid fa-clock-rotate-left"></i>
                </div>

                <div>
                    <h4>Registros de aceptación</h4>

                    <p>
                        {{ $aceptaciones->total() }}
                        registros encontrados
                    </p>
                </div>

            </div>

        </div>


        @if($aceptaciones->count())

            <div class="fepade-table-wrapper">

                <table class="table table-hover align-middle">

                    <thead>
                        <tr>
                            <th>Consultor</th>
                            <th>Correo</th>
                            <th>Versión</th>
                            <th>Fecha de aceptación</th>
                            <th>IP</th>
                            <th>Invitación</th>
                            <th>Hash</th>
                        </tr>
                    </thead>

                    <tbody>

                        @foreach($aceptaciones as $aceptacion)

                            <tr>

                                <td>
                                    <strong>
                                        {{ $aceptacion->consultor?->nombre_completo ?? '—' }}
                                    </strong>
                                </td>

                                <td>
                                    {{ $aceptacion->usuario?->email ?? '—' }}
                                </td>

                                <td>
                                    <span class="badge badge-primary-soft">
                                        v{{ $aceptacion->version_terminos }}
                                    </span>
                                </td>

                                <td>
                                    <strong>
                                        {{ $aceptacion->fecha_aceptacion?->format('d/m/Y') }}
                                    </strong>

                                    <small class="d-block text-muted">
                                        {{ $aceptacion->fecha_aceptacion?->format('h:i:s A') }}
                                    </small>
                                </td>

                                <td>
                                    {{ $aceptacion->ip ?? '—' }}
                                </td>

                                <td>
                                    #{{ $aceptacion->id_invitacion }}
                                </td>

                                <td>
                                    <code
                                        class="fepade-code"
                                        title="{{ $aceptacion->hash_terminos }}"
                                    >
                                        {{ \Illuminate\Support\Str::limit(
                                            $aceptacion->hash_terminos,
                                            16,
                                            '…'
                                        ) }}
                                    </code>
                                </td>

                            </tr>

                        @endforeach

                    </tbody>

                </table>

            </div>


            <div class="pagination-wrapper">
                {{ $aceptaciones->links() }}
            </div>

        @else

            <div class="busqueda-empty-state">

                <div class="busqueda-empty-icon">
                    <i class="fa-solid fa-file-circle-check"></i>
                </div>

                <h5>
                    No hay aceptaciones registradas
                </h5>

                <p>
                    Los registros aparecerán cuando los consultores
                    creen sus credenciales mediante una invitación.
                </p>

            </div>

        @endif

    </section>

</div>

@endsection