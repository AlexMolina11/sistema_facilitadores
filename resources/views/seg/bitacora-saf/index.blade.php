@extends('layouts.app')

@section('title', 'Bitácora SAF | Facilitadores FEPADE')
@section('page-title', 'Bitácora SAF')
@section('page-subtitle', 'Seguimiento de sincronizaciones e incidencias de la integración SAF')

@section('content')

@php
    use App\Modules\Fac\Models\SincronizacionSaf;

    /*
    |--------------------------------------------------------------------------
    | Etiquetas de estado
    |--------------------------------------------------------------------------
    */

    $estadoLabel = static function (?string $estado): string {
        return match ($estado) {
            SincronizacionSaf::ESTADO_PENDIENTE => 'Pendiente',
            SincronizacionSaf::ESTADO_EN_PROCESO => 'En proceso',
            SincronizacionSaf::ESTADO_COMPLETADA => 'Completada',
            SincronizacionSaf::ESTADO_COMPLETADA_CON_ERRORES => 'Completada con errores',
            SincronizacionSaf::ESTADO_FALLIDA => 'Fallida',
            default => $estado ?: 'Sin estado',
        };
    };

    /*
    |--------------------------------------------------------------------------
    | Clases de badges existentes
    |--------------------------------------------------------------------------
    */

    $estadoBadge = static function (?string $estado): string {
        return match ($estado) {
            SincronizacionSaf::ESTADO_COMPLETADA => 'badge-success-soft',
            SincronizacionSaf::ESTADO_COMPLETADA_CON_ERRORES => 'badge-warning-soft',
            SincronizacionSaf::ESTADO_FALLIDA => 'badge-danger-soft',
            SincronizacionSaf::ESTADO_EN_PROCESO => 'badge-primary-soft',
            SincronizacionSaf::ESTADO_PENDIENTE => 'badge-secondary-soft',
            default => 'badge-secondary-soft',
        };
    };

    /*
    |--------------------------------------------------------------------------
    | Tipo de ejecución
    |--------------------------------------------------------------------------
    */

    $tipoEjecucionLabel = static function (?string $tipo): string {
        return match ($tipo) {
            SincronizacionSaf::TIPO_AUTOMATICA => 'Automática',
            SincronizacionSaf::TIPO_MANUAL => 'Manual',
            default => $tipo ?: 'No definido',
        };
    };

    /*
    |--------------------------------------------------------------------------
    | Nombre del usuario ejecutor
    |--------------------------------------------------------------------------
    */

    $nombreUsuario = static function ($usuario): string {
        if (! $usuario) {
            return 'Proceso automático';
        }

        $nombreCompleto = trim(
            ($usuario->nombres ?? '')
            . ' '
            . ($usuario->apellidos ?? '')
        );

        if ($nombreCompleto !== '') {
            return $nombreCompleto;
        }

        return $usuario->email ?? 'Usuario del sistema';
    };

    /*
    |--------------------------------------------------------------------------
    | Duración de la ejecución
    |--------------------------------------------------------------------------
    */

    $duracion = static function ($sincronizacion): string {
        if (! $sincronizacion?->fecha_inicio) {
            return '—';
        }

        if (! $sincronizacion->fecha_fin) {
            return 'En ejecución';
        }

        $segundos = $sincronizacion
            ->fecha_inicio
            ->diffInSeconds($sincronizacion->fecha_fin);

        if ($segundos < 60) {
            return $segundos . ' segundos';
        }

        $minutos = intdiv($segundos, 60);
        $segundosRestantes = $segundos % 60;

        if ($minutos < 60) {
            return $minutos . ' min ' . $segundosRestantes . ' s';
        }

        $horas = intdiv($minutos, 60);
        $minutosRestantes = $minutos % 60;

        return $horas . ' h ' . $minutosRestantes . ' min';
    };

    /*
    |--------------------------------------------------------------------------
    | Totales generales
    |--------------------------------------------------------------------------
    */

    $totalSincronizaciones = (int) (
        $resumenGeneral->total_sincronizaciones ?? 0
    );

    $totalCompletadas = (int) (
        $resumenGeneral->completadas ?? 0
    );

    $totalConErrores = (int) (
        $resumenGeneral->completadas_con_errores ?? 0
    );

    $totalFallidas = (int) (
        $resumenGeneral->fallidas ?? 0
    );

    $totalErrores = (int) (
        $resumenErrores->total_errores ?? 0
    );

    $erroresPendientes = (int) (
        $resumenErrores->pendientes ?? 0
    );

    $erroresResueltos = (int) (
        $resumenErrores->resueltos ?? 0
    );

    /*
    |--------------------------------------------------------------------------
    | Totales de consultores
    |--------------------------------------------------------------------------
    */

    $consultoresCreados = (int) (
        $resumenGeneral->consultores_creados ?? 0
    );

    $consultoresActualizados = (int) (
        $resumenGeneral->consultores_actualizados ?? 0
    );

    $consultoresSinCambios = (int) (
        $resumenGeneral->consultores_sin_cambios ?? 0
    );

    $consultoresConError = (int) (
        $resumenGeneral->consultores_con_error ?? 0
    );

    /*
    |--------------------------------------------------------------------------
    | Totales de capacitaciones
    |--------------------------------------------------------------------------
    */

    $capacitacionesCreadas = (int) (
        $resumenGeneral->capacitaciones_creadas ?? 0
    );

    $capacitacionesActualizadas = (int) (
        $resumenGeneral->capacitaciones_actualizadas ?? 0
    );

    $capacitacionesSinCambios = (int) (
        $resumenGeneral->capacitaciones_sin_cambios ?? 0
    );

    $capacitacionesConError = (int) (
        $resumenGeneral->capacitaciones_con_error ?? 0
    );
@endphp

{{-- Encabezado principal --}}
<div class="dashboard-advanced-hero mb-4">
    <div>
        <span class="dashboard-kicker">
            Integración SAF
        </span>

        <h2>
            Seguimiento de sincronizaciones
        </h2>

        <p>
            Consulta las ejecuciones realizadas, los registros procesados,
            los consultores y capacitaciones sincronizadas, así como los
            errores identificados durante la integración con SAF.
        </p>
    </div>

    <div class="dashboard-hero-metric">
        <strong>
            {{ number_format($totalSincronizaciones) }}
        </strong>

        <span>
            sincronizaciones registradas
        </span>

        <div class="d-flex gap-2 flex-wrap mt-3">
            <a
                href="{{ route('seg.bitacora-saf.index') }}"
                class="btn btn-success"
            >
                <i class="fa-solid fa-arrows-rotate me-1"></i>
                Actualizar
            </a>
        </div>
    </div>
</div>

{{-- Alertas --}}
@if(session('success'))
    <div
        class="alert alert-success alert-dismissible fade show"
        role="alert"
    >
        <i class="fa-solid fa-circle-check me-1"></i>

        {{ session('success') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Cerrar"
        ></button>
    </div>
@endif

@if(session('error'))
    <div
        class="alert alert-danger alert-dismissible fade show"
        role="alert"
    >
        <i class="fa-solid fa-circle-exclamation me-1"></i>

        {{ session('error') }}

        <button
            type="button"
            class="btn-close"
            data-bs-dismiss="alert"
            aria-label="Cerrar"
        ></button>
    </div>
@endif

{{-- Filtros --}}
<x-ui.filter-card
    title="Filtros de sincronización"
    subtitle="Consulta las ejecuciones por estado, tipo, periodo, identificador o presencia de errores."
    class="mb-4"
>
    <form
        method="GET"
        action="{{ route('seg.bitacora-saf.index') }}"
        class="row g-3 align-items-end"
    >
        <div class="col-md-6 col-xl-3">
            <label
                for="q"
                class="form-label"
            >
                Buscar
            </label>

            <input
                type="search"
                name="q"
                id="q"
                value="{{ request('q') }}"
                class="form-control @error('q') is-invalid @enderror"
                placeholder="UUID, mensaje o usuario"
            >

            @error('q')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="col-md-6 col-xl-2">
            <label
                for="estado"
                class="form-label"
            >
                Estado
            </label>

            <select
                name="estado"
                id="estado"
                class="form-select @error('estado') is-invalid @enderror"
            >
                <option value="">
                    Todos
                </option>

                @foreach($estados as $valor => $etiqueta)
                    <option
                        value="{{ $valor }}"
                        @selected(request('estado') === $valor)
                    >
                        {{ $etiqueta }}
                    </option>
                @endforeach
            </select>

            @error('estado')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="col-md-6 col-xl-2">
            <label
                for="tipo_ejecucion"
                class="form-label"
            >
                Tipo de ejecución
            </label>

            <select
                name="tipo_ejecucion"
                id="tipo_ejecucion"
                class="form-select @error('tipo_ejecucion') is-invalid @enderror"
            >
                <option value="">
                    Todas
                </option>

                @foreach($tiposEjecucion as $valor => $etiqueta)
                    <option
                        value="{{ $valor }}"
                        @selected(
                            request('tipo_ejecucion') === $valor
                        )
                    >
                        {{ $etiqueta }}
                    </option>
                @endforeach
            </select>

            @error('tipo_ejecucion')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="col-md-6 col-xl-2">
            <label
                for="desde"
                class="form-label"
            >
                Fecha inicio
            </label>

            <input
                type="date"
                name="desde"
                id="desde"
                value="{{ request('desde') }}"
                class="form-control @error('desde') is-invalid @enderror"
            >

            @error('desde')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="col-md-6 col-xl-2">
            <label
                for="hasta"
                class="form-label"
            >
                Fecha fin
            </label>

            <input
                type="date"
                name="hasta"
                id="hasta"
                value="{{ request('hasta') }}"
                class="form-control @error('hasta') is-invalid @enderror"
            >

            @error('hasta')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="form-check mb-2">
                <input
                    type="hidden"
                    name="solo_con_errores"
                    value="0"
                >

                <input
                    type="checkbox"
                    name="solo_con_errores"
                    id="solo_con_errores"
                    value="1"
                    class="form-check-input"
                    @checked(
                        request()->boolean('solo_con_errores')
                    )
                >

                <label
                    for="solo_con_errores"
                    class="form-check-label"
                >
                    Solo ejecuciones con errores
                </label>
            </div>
        </div>

        <div class="col-md-6 col-xl-3 fepade-filter-actions">
            <button
                type="submit"
                class="btn btn-navy w-100"
            >
                <i class="fa-solid fa-filter me-1"></i>
                Aplicar
            </button>

            <a
                href="{{ route('seg.bitacora-saf.index') }}"
                class="btn btn-outline-secondary w-100"
            >
                Limpiar
            </a>
        </div>
    </form>
</x-ui.filter-card>

{{-- Indicadores principales --}}
<x-ui.dashboard-grid
    :columns="4"
    class="mb-4"
>
    <div class="col">
        <x-ui.stat-card
            label="Sincronizaciones"
            :value="$totalSincronizaciones"
            description="Total de ejecuciones"
            icon="fa-arrows-rotate"
            variant="primary"
        />
    </div>

    <div class="col">
        <x-ui.stat-card
            label="Completadas"
            :value="$totalCompletadas"
            description="Finalizadas sin errores"
            icon="fa-circle-check"
            variant="success"
        />
    </div>

    <div class="col">
        <x-ui.stat-card
            label="Con errores"
            :value="$totalConErrores"
            description="Finalizadas con incidencias"
            icon="fa-triangle-exclamation"
            variant="warning"
        />
    </div>

    <div class="col">
        <x-ui.stat-card
            label="Fallidas"
            :value="$totalFallidas"
            description="Ejecuciones interrumpidas"
            icon="fa-circle-xmark"
            variant="danger"
        />
    </div>
</x-ui.dashboard-grid>

{{-- Indicadores de errores --}}
<x-ui.dashboard-grid
    :columns="4"
    class="mb-4"
>
    <div class="col">
        <x-ui.stat-card
            label="Errores registrados"
            :value="$totalErrores"
            description="Total de incidencias"
            icon="fa-bug"
            variant="warning"
        />
    </div>

    <div class="col">
        <x-ui.stat-card
            label="Errores pendientes"
            :value="$erroresPendientes"
            description="Requieren revisión"
            icon="fa-clock"
            variant="danger"
        />
    </div>

    <div class="col">
        <x-ui.stat-card
            label="Errores resueltos"
            :value="$erroresResueltos"
            description="Incidencias atendidas"
            icon="fa-check-double"
            variant="success"
        />
    </div>

    <div class="col">
        <x-ui.stat-card
            label="Ejecuciones visibles"
            :value="$sincronizaciones->total()"
            description="Según filtros aplicados"
            icon="fa-list-check"
            variant="info"
        />
    </div>
</x-ui.dashboard-grid>

{{-- Última sincronización --}}
<x-ui.table-card
    title="Última sincronización"
    subtitle="Resumen de la ejecución más reciente registrada por la integración SAF."
    :items="$ultimaSincronizacion ? collect([$ultimaSincronizacion]) : collect()"
    empty-title="Sin sincronizaciones registradas"
    empty-message="La información aparecerá después de ejecutar por primera vez el procesamiento SAF."
    class="mb-4"
>
    @if($ultimaSincronizacion)
        <div class="row g-3 mb-4">
            <div class="col-md-6 col-xl-3">
                <div class="dashboard-kpi-card h-100">
                    <div>
                        <span>Estado</span>

                        <strong class="fs-5">
                            <span
                                class="badge {{ $estadoBadge(
                                    $ultimaSincronizacion->estado
                                ) }}"
                            >
                                {{ $estadoLabel(
                                    $ultimaSincronizacion->estado
                                ) }}
                            </span>
                        </strong>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="dashboard-kpi-card h-100">
                    <div>
                        <span>Tipo de ejecución</span>

                        <strong class="fs-5">
                            {{ $tipoEjecucionLabel(
                                $ultimaSincronizacion->tipo_ejecucion
                            ) }}
                        </strong>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="dashboard-kpi-card h-100">
                    <div>
                        <span>Registros procesados</span>

                        <strong>
                            {{ number_format(
                                $ultimaSincronizacion
                                    ->total_registros_procesados
                            ) }}
                            /
                            {{ number_format(
                                $ultimaSincronizacion
                                    ->total_registros_recibidos
                            ) }}
                        </strong>
                    </div>
                </div>
            </div>

            <div class="col-md-6 col-xl-3">
                <div class="dashboard-kpi-card h-100">
                    <div>
                        <span>Duración</span>

                        <strong class="fs-5">
                            {{ $duracion($ultimaSincronizacion) }}
                        </strong>
                    </div>
                </div>
            </div>
        </div>

        <table class="table table-hover align-middle">
            <tbody>
                <tr>
                    <th style="width: 220px;">
                        Identificador
                    </th>

                    <td>
                        #{{ $ultimaSincronizacion
                            ->id_sincronizacion_saf }}
                    </td>
                </tr>

                <tr>
                    <th>
                        UUID
                    </th>

                    <td>
                        <code>
                            {{ $ultimaSincronizacion->uuid }}
                        </code>
                    </td>
                </tr>

                <tr>
                    <th>
                        Inicio
                    </th>

                    <td>
                        {{ $ultimaSincronizacion
                            ->fecha_inicio
                            ?->format('d/m/Y H:i:s') ?? '—' }}
                    </td>
                </tr>

                <tr>
                    <th>
                        Finalización
                    </th>

                    <td>
                        {{ $ultimaSincronizacion
                            ->fecha_fin
                            ?->format('d/m/Y H:i:s') ?? 'En ejecución' }}
                    </td>
                </tr>

                <tr>
                    <th>
                        Ejecutada por
                    </th>

                    <td>
                        {{ $nombreUsuario(
                            $ultimaSincronizacion->usuarioEjecutor
                        ) }}
                    </td>
                </tr>

                @if($ultimaSincronizacion->mensaje)
                    <tr>
                        <th>
                            Resultado
                        </th>

                        <td>
                            {{ $ultimaSincronizacion->mensaje }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>

        <div class="text-end mt-3">
            <a
                href="{{ route(
                    'seg.bitacora-saf.show',
                    $ultimaSincronizacion
                ) }}"
                class="btn btn-navy"
            >
                <i class="fa-solid fa-eye me-1"></i>
                Ver detalle completo
            </a>
        </div>
    @endif
</x-ui.table-card>

{{-- Resumen de consultores --}}
<x-ui.dashboard-grid
    :columns="4"
    class="mb-4"
>
    <div class="col">
        <x-ui.stat-card
            label="Consultores creados"
            :value="$consultoresCreados"
            description="Nuevos registros"
            icon="fa-user-plus"
            variant="success"
        />
    </div>

    <div class="col">
        <x-ui.stat-card
            label="Consultores actualizados"
            :value="$consultoresActualizados"
            description="Registros modificados"
            icon="fa-user-pen"
            variant="info"
        />
    </div>

    <div class="col">
        <x-ui.stat-card
            label="Consultores sin cambios"
            :value="$consultoresSinCambios"
            description="Información ya actualizada"
            icon="fa-user-check"
            variant="primary"
        />
    </div>

    <div class="col">
        <x-ui.stat-card
            label="Consultores con error"
            :value="$consultoresConError"
            description="No pudieron procesarse"
            icon="fa-user-xmark"
            variant="danger"
        />
    </div>
</x-ui.dashboard-grid>

{{-- Resumen de capacitaciones --}}
<x-ui.dashboard-grid
    :columns="4"
    class="mb-4"
>
    <div class="col">
        <x-ui.stat-card
            label="Capacitaciones creadas"
            :value="$capacitacionesCreadas"
            description="Nuevos registros"
            icon="fa-graduation-cap"
            variant="success"
        />
    </div>

    <div class="col">
        <x-ui.stat-card
            label="Capacitaciones actualizadas"
            :value="$capacitacionesActualizadas"
            description="Registros modificados"
            icon="fa-pen-to-square"
            variant="info"
        />
    </div>

    <div class="col">
        <x-ui.stat-card
            label="Capacitaciones sin cambios"
            :value="$capacitacionesSinCambios"
            description="Información ya actualizada"
            icon="fa-circle-check"
            variant="primary"
        />
    </div>

    <div class="col">
        <x-ui.stat-card
            label="Capacitaciones con error"
            :value="$capacitacionesConError"
            description="No pudieron procesarse"
            icon="fa-triangle-exclamation"
            variant="danger"
        />
    </div>
</x-ui.dashboard-grid>

{{-- Historial de sincronizaciones --}}
<x-ui.table-card
    title="Historial de sincronizaciones"
    subtitle="Ejecuciones registradas por el proceso de integración SAF."
    :items="$sincronizaciones"
    empty-title="No hay sincronizaciones"
    empty-message="No se encontraron ejecuciones para los filtros seleccionados."
>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>
                        Ejecución
                    </th>

                    <th>
                        Fecha
                    </th>

                    <th>
                        Tipo
                    </th>

                    <th>
                        Estado
                    </th>

                    <th class="text-center">
                        Recibidos
                    </th>

                    <th class="text-center">
                        Procesados
                    </th>

                    <th class="text-center">
                        Exitosos
                    </th>

                    <th class="text-center">
                        Errores
                    </th>

                    <th>
                        Duración
                    </th>

                    <th class="text-end">
                        Acción
                    </th>
                </tr>
            </thead>

            <tbody>
                @foreach($sincronizaciones as $sincronizacion)
                    <tr>
                        <td>
                            <div class="fw-semibold">
                                #{{ $sincronizacion
                                    ->id_sincronizacion_saf }}
                            </div>

                            <small
                                class="text-muted"
                                title="{{ $sincronizacion->uuid }}"
                            >
                                {{ \Illuminate\Support\Str::limit(
                                    $sincronizacion->uuid,
                                    18
                                ) }}
                            </small>
                        </td>

                        <td>
                            <div class="fw-semibold">
                                {{ $sincronizacion
                                    ->fecha_inicio
                                    ?->format('d/m/Y') ?? '—' }}
                            </div>

                            <small class="text-muted">
                                {{ $sincronizacion
                                    ->fecha_inicio
                                    ?->format('H:i:s') ?? '—' }}
                            </small>
                        </td>

                        <td>
                            <span class="badge badge-primary-soft">
                                {{ $tipoEjecucionLabel(
                                    $sincronizacion->tipo_ejecucion
                                ) }}
                            </span>
                        </td>

                        <td>
                            <span
                                class="badge {{ $estadoBadge(
                                    $sincronizacion->estado
                                ) }}"
                            >
                                {{ $estadoLabel(
                                    $sincronizacion->estado
                                ) }}
                            </span>
                        </td>

                        <td class="text-center fw-semibold">
                            {{ number_format(
                                $sincronizacion
                                    ->total_registros_recibidos
                            ) }}
                        </td>

                        <td class="text-center fw-semibold">
                            {{ number_format(
                                $sincronizacion
                                    ->total_registros_procesados
                            ) }}
                        </td>

                        <td class="text-center">
                            <span class="badge badge-success-soft">
                                {{ number_format(
                                    $sincronizacion
                                        ->total_registros_exitosos
                                ) }}
                            </span>
                        </td>

                        <td class="text-center">
                            @if($sincronizacion->errores_count > 0)
                                <span class="badge badge-danger-soft">
                                    {{ number_format(
                                        $sincronizacion->errores_count
                                    ) }}
                                </span>

                                @if(
                                    $sincronizacion
                                        ->errores_pendientes_count > 0
                                )
                                    <div>
                                        <small class="text-danger">
                                            {{ number_format(
                                                $sincronizacion
                                                    ->errores_pendientes_count
                                            ) }}
                                            pendientes
                                        </small>
                                    </div>
                                @endif
                            @else
                                <span class="badge badge-success-soft">
                                    0
                                </span>
                            @endif
                        </td>

                        <td>
                            {{ $duracion($sincronizacion) }}
                        </td>

                        <td class="text-end">
                            <a
                                href="{{ route(
                                    'seg.bitacora-saf.show',
                                    $sincronizacion
                                ) }}"
                                class="btn btn-sm btn-outline-primary"
                                title="Ver detalle"
                            >
                                <i class="fa-solid fa-eye me-1"></i>
                                Ver
                            </a>
                        </td>
                    </tr>
                @endforeach
            </tbody>
        </table>
    </div>

    @if($sincronizaciones->hasPages())
        <div class="mt-4">
            {{ $sincronizaciones->links() }}
        </div>
    @endif
</x-ui.table-card>

@endsection