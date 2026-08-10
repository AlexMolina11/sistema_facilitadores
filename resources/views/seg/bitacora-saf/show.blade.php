@extends('layouts.app')

@section('title', 'Detalle SAF | Facilitadores FEPADE')
@section('page-title', 'Detalle de sincronización')
@section('page-subtitle', 'Registros procesados durante la ejecución SAF')

@section('content')

@php
    use App\Modules\Fac\Models\SafInstructorImportacion;
    use App\Modules\Fac\Models\SafCapacitacionImportacion;
    use App\Modules\Fac\Models\SincronizacionSaf;

    $estadoTexto = match ($sincronizacionSaf->estado) {
        SincronizacionSaf::ESTADO_COMPLETADA =>
            'Completada',

        SincronizacionSaf::ESTADO_COMPLETADA_CON_ERRORES =>
            'Completada con errores',

        SincronizacionSaf::ESTADO_FALLIDA =>
            'Fallida',

        default =>
            $sincronizacionSaf->estado,
    };

    $estadoBadge = match ($sincronizacionSaf->estado) {
        SincronizacionSaf::ESTADO_COMPLETADA =>
            'badge-success-soft',

        SincronizacionSaf::ESTADO_COMPLETADA_CON_ERRORES =>
            'badge-warning-soft',

        SincronizacionSaf::ESTADO_FALLIDA =>
            'badge-danger-soft',

        default =>
            'badge-muted-soft',
    };

    $resultadoBadge = static function (?string $resultado): string {
        return match ($resultado) {
            'CREADO' =>
                'badge-success-soft',

            'ACTUALIZADO',
            'SIN_CAMBIOS' =>
                'badge-primary-soft',

            'DESACTIVADO' =>
                'badge-warning-soft',

            'ERROR',
            'OMITIDO' =>
                'badge-danger-soft',

            default =>
                'badge-muted-soft',
        };
    };
@endphp

<div class="dashboard-advanced-hero mb-4">
    <div>
        <span class="dashboard-kicker">
            Integración SAF
        </span>

        <h2>
            Sincronización del
            {{ $sincronizacionSaf->fecha_inicio
                ?->format('d/m/Y') ?? '—' }}
        </h2>

        <p>
            {{ $sincronizacionSaf->fecha_inicio
                ?->format('H:i:s') ?? '' }}

            ·

            <span
                class="badge {{ $estadoBadge }}"
            >
                {{ $estadoTexto }}
            </span>
        </p>
    </div>

    <div class="dashboard-hero-metric">
        <strong>
            {{ (int) ($resumenConsultores->exitosos ?? 0) }}
        </strong>

        <span>
            consultores procesados correctamente
        </span>

        <div class="mt-3">
            <a
                href="{{ route('seg.bitacora-saf.index') }}"
                class="btn btn-success"
            >
                <i class="fa-solid fa-arrow-left me-1"></i>
                Volver
            </a>
        </div>
    </div>
</div>

@if(session('success'))
    <div class="alert alert-success">
        {{ session('success') }}
    </div>
@endif

<div class="d-flex gap-2 flex-wrap mb-4">

    <a
        href="{{ route(
            'seg.bitacora-saf.show',
            [
                'sincronizacionSaf' =>
                    $sincronizacionSaf,

                'vista' =>
                    'consultores',
            ]
        ) }}"
        class="btn {{ $vista === 'consultores'
            ? 'btn-navy'
            : 'btn-outline-secondary' }}"
    >
        <i class="fa-solid fa-users me-1"></i>

        Consultores

        <span class="badge badge-muted-soft ms-1">
            {{ (int) ($resumenConsultores->total ?? 0) }}
        </span>
    </a>

    <a
        href="{{ route(
            'seg.bitacora-saf.show',
            [
                'sincronizacionSaf' =>
                    $sincronizacionSaf,

                'vista' =>
                    'capacitaciones',
            ]
        ) }}"
        class="btn {{ $vista === 'capacitaciones'
            ? 'btn-navy'
            : 'btn-outline-secondary' }}"
    >
        <i class="fa-solid fa-graduation-cap me-1"></i>

        Capacitaciones

        <span class="badge badge-muted-soft ms-1">
            {{ (int) ($resumenCapacitaciones->total ?? 0) }}
        </span>
    </a>

    <a
        href="{{ route(
            'seg.bitacora-saf.show',
            [
                'sincronizacionSaf' =>
                    $sincronizacionSaf,

                'vista' =>
                    'errores',
            ]
        ) }}"
        class="btn {{ $vista === 'errores'
            ? 'btn-navy'
            : 'btn-outline-secondary' }}"
    >
        <i class="fa-solid fa-triangle-exclamation me-1"></i>

        Errores

        @if($erroresPendientes > 0)
            <span class="badge badge-danger-soft ms-1">
                {{ $erroresPendientes }}
            </span>
        @else
            <span class="badge badge-success-soft ms-1">
                {{ $totalErrores }}
            </span>
        @endif
    </a>

</div>

@if($vista === 'consultores')

    <x-ui.table-card
        title="Consultores procesados"
        subtitle="Instructores incluidos en esta sincronización."
        :items="$consultores"
        empty-title="Sin consultores"
        empty-message="Esta sincronización no procesó instructores."
    >
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Instructor SAF</th>
                    <th>Consultor</th>
                    <th>DUI</th>
                    <th>Resultado</th>
                    <th>Procesado</th>
                    <th class="text-end">
                        Acción
                    </th>
                </tr>
            </thead>

            <tbody>
                @foreach($consultores as $registro)
                    <tr>
                        <td>
                            <strong>
                                {{ $registro->id_instructor }}
                            </strong>
                        </td>

                        <td>
                            <div class="fw-semibold">
                                {{ $registro->nombres }}
                                {{ $registro->apellidos }}
                            </div>
                        </td>

                        <td>
                            @php
                                $tipoDocumentoSaf =
                                    match (
                                        (int) $registro->tipo_identificacion
                                    ) {
                                        2 => 'NIT',
                                        4 => 'Pasaporte',
                                        5 => 'Licencia de conducir',
                                        7 => 'DUI',
                                        default => 'No identificado',
                                    };
                            @endphp

                            <div>
                                {{ $registro->numero_identificacion ?? '—' }}
                            </div>

                            @if($registro->tipo_identificacion)
                                <small class="text-muted">
                                    {{ $tipoDocumentoSaf }}
                                </small>
                            @endif
                        </td>

                        <td>
                            <span
                                class="badge {{ $resultadoBadge(
                                    $registro->resultado_procesamiento
                                ) }}"
                            >
                                {{ $registro
                                    ->resultado_procesamiento
                                    ?? $registro->estado }}
                            </span>

                            @if($registro->mensaje_error)
                                <small class="text-danger d-block mt-1">
                                    {{ $registro->mensaje_error }}
                                </small>
                            @endif
                        </td>

                        <td>
                            {{ $registro->fecha_procesamiento
                                ?->format('d/m/Y H:i:s') ?? '—' }}
                        </td>

                        <td class="text-end">
                            @if($registro->id_registro_local)
                                <a
                                    href="{{ route(
                                        'fac.consultores.show',
                                        $registro->id_registro_local
                                    ) }}"
                                    class="btn btn-sm btn-outline-primary"
                                >
                                    <i class="fa-solid fa-eye me-1"></i>
                                    Expediente
                                </a>
                            @else
                                <span class="text-muted">
                                    —
                                </span>
                            @endif
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($consultores->hasPages())
            <div class="mt-4">
                {{ $consultores->links() }}
            </div>
        @endif
    </x-ui.table-card>

@endif

@if($vista === 'capacitaciones')

    <x-ui.table-card
        title="Capacitaciones procesadas"
        subtitle="Capacitaciones incluidas en esta sincronización."
        :items="$capacitaciones"
        empty-title="Sin capacitaciones"
        empty-message="Esta sincronización no procesó capacitaciones."
    >
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Código SAF</th>
                    <th>Instructor</th>
                    <th>Capacitación</th>
                    <th>Fecha</th>
                    <th>Resultado</th>
                    <th>Procesado</th>
                </tr>
            </thead>

            <tbody>
                @foreach($capacitaciones as $registro)
                    <tr>
                        <td>
                            <strong>
                                {{ $registro->codigo_evento }}
                            </strong>
                        </td>

                        <td>
                            {{ $registro->id_instructor }}
                        </td>

                        <td>
                            <div class="fw-semibold">
                                {{ $registro->curso_nombre }}
                            </div>

                            @if($registro->cliente)
                                <small class="text-muted d-block">
                                    {{ $registro->cliente }}
                                </small>
                            @endif

                            @if($registro->tipo_evento_nombre)
                                <small class="text-muted d-block">
                                    {{ $registro->tipo_evento_nombre }}
                                </small>
                            @endif
                        </td>

                        <td>
                            {{ $registro->fecha_inicio
                                ?->format('d/m/Y') ?? '—' }}
                        </td>

                        <td>
                            <span
                                class="badge {{ $resultadoBadge(
                                    $registro->resultado_procesamiento
                                ) }}"
                            >
                                {{ $registro
                                    ->resultado_procesamiento
                                    ?? $registro->estado }}
                            </span>

                            @if($registro->mensaje_error)
                                <small class="text-danger d-block mt-1">
                                    {{ $registro->mensaje_error }}
                                </small>
                            @endif
                        </td>

                        <td>
                            {{ $registro->fecha_procesamiento
                                ?->format('d/m/Y H:i:s') ?? '—' }}
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>

        @if($capacitaciones->hasPages())
            <div class="mt-4">
                {{ $capacitaciones->links() }}
            </div>
        @endif
    </x-ui.table-card>

@endif

@if($vista === 'errores')

    <x-ui.table-card
        title="Errores de la sincronización"
        subtitle="Registros que requieren revisión y acciones sugeridas para corregirlos."
        :items="$errores"
        empty-title="Sin errores"
        empty-message="Esta sincronización no registró incidencias."
    >
        @foreach($errores as $error)

            <div class="border-bottom pb-4 mb-4">

                <div
                    class="d-flex justify-content-between gap-3 flex-wrap"
                >
                    <div>
                        <div class="d-flex gap-2 flex-wrap mb-2">
                            <span class="badge badge-warning-soft">
                                {{ $error->tipo_registro }}
                            </span>

                            @if($error->resuelto)
                                <span class="badge badge-success-soft">
                                    Resuelto
                                </span>
                            @else
                                <span class="badge badge-danger-soft">
                                    Pendiente
                                </span>
                            @endif
                        </div>

                        <h5 class="mb-1">
                            {{ $error->id_registro_externo
                                ?? 'Error general' }}
                        </h5>

                        <p class="mb-0">
                            {{ $error->mensaje }}
                        </p>
                    </div>

                    <div class="text-muted">
                        {{ $error->created_at
                            ?->format('d/m/Y H:i') }}
                    </div>
                </div>

                <div class="alert alert-warning mt-3 mb-3">
                    <strong>
                        <i class="fa-solid fa-screwdriver-wrench me-1"></i>
                        Cómo resolverlo
                    </strong>

                    <div class="mt-1">
                        {{ $error->recomendacionResolucion() }}
                    </div>
                </div>

                @if($error->codigo_error)
                    <small class="text-muted d-block mb-2">
                        Código:
                        <code>{{ $error->codigo_error }}</code>
                    </small>
                @endif

                <details class="mb-3">
                    <summary class="fw-semibold">
                        Ver información técnica
                    </summary>

                    <div class="mt-3">
                        <p>
                            <strong>Operación:</strong>
                            {{ $error->tipo_operacion ?? '—' }}
                        </p>

                        @if($error->detalle_tecnico)
                            <pre class="bg-light p-3 rounded">{{ $error->detalle_tecnico }}</pre>
                        @endif
                    </div>
                </details>

                @if(!$error->resuelto)

                    <form
                        method="POST"
                        action="{{ route(
                            'seg.bitacora-saf.errores.resolver',
                            [
                                'sincronizacionSaf' =>
                                    $sincronizacionSaf,

                                'error' =>
                                    $error,
                            ]
                        ) }}"
                    >
                        @csrf

                        <div class="row g-2 align-items-end">
                            <div class="col-md-9">
                                <label class="form-label">
                                    Observación de resolución
                                </label>

                                <input
                                    type="text"
                                    name="observacion"
                                    class="form-control"
                                    required
                                    maxlength="1000"
                                    placeholder="Ej. Se corrigió el registro en SAF y se verificó nuevamente."
                                >
                            </div>

                            <div class="col-md-3">
                                <button
                                    type="submit"
                                    class="btn btn-success w-100"
                                >
                                    <i class="fa-solid fa-check me-1"></i>
                                    Marcar resuelto
                                </button>
                            </div>
                        </div>
                    </form>

                @else

                    <div class="alert alert-success mb-2">
                        <strong>
                            Resolución:
                        </strong>

                        {{ $error->observacion_resolucion
                            ?? 'Sin observación.' }}

                        @if($error->fecha_resolucion)
                            <small class="d-block mt-1">
                                {{ $error->fecha_resolucion
                                    ->format('d/m/Y H:i') }}
                            </small>
                        @endif
                    </div>

                    <form
                        method="POST"
                        action="{{ route(
                            'seg.bitacora-saf.errores.reabrir',
                            [
                                'sincronizacionSaf' =>
                                    $sincronizacionSaf,

                                'error' =>
                                    $error,
                            ]
                        ) }}"
                    >
                        @csrf

                        <button
                            type="submit"
                            class="btn btn-sm btn-outline-secondary"
                        >
                            Reabrir incidencia
                        </button>
                    </form>

                @endif

            </div>

        @endforeach

        @if($errores->hasPages())
            <div class="mt-4">
                {{ $errores->links() }}
            </div>
        @endif
    </x-ui.table-card>

@endif

@endsection