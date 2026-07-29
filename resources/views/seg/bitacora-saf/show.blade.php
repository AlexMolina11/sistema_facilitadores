@extends('layouts.app')

@section('title', 'Detalle de sincronización SAF | Facilitadores FEPADE')
@section('page-title', 'Detalle de sincronización SAF')
@section('page-subtitle', 'Resultados, métricas e incidencias de la ejecución seleccionada')

@section('content')

@php
    use App\Modules\Fac\Models\SincronizacionSaf;
    use App\Modules\Fac\Models\SincronizacionSafError;

    /*
    |--------------------------------------------------------------------------
    | Estado de sincronización
    |--------------------------------------------------------------------------
    */

    $estadoLabel = static function (?string $estado): string {
        return match ($estado) {
            SincronizacionSaf::ESTADO_PENDIENTE =>
                'Pendiente',

            SincronizacionSaf::ESTADO_EN_PROCESO =>
                'En proceso',

            SincronizacionSaf::ESTADO_COMPLETADA =>
                'Completada',

            SincronizacionSaf::ESTADO_COMPLETADA_CON_ERRORES =>
                'Completada con errores',

            SincronizacionSaf::ESTADO_FALLIDA =>
                'Fallida',

            default =>
                $estado ?: 'Sin estado',
        };
    };

    $estadoBadge = static function (?string $estado): string {
        return match ($estado) {
            SincronizacionSaf::ESTADO_COMPLETADA =>
                'badge-success-soft',

            SincronizacionSaf::ESTADO_COMPLETADA_CON_ERRORES =>
                'badge-warning-soft',

            SincronizacionSaf::ESTADO_FALLIDA =>
                'badge-danger-soft',

            SincronizacionSaf::ESTADO_EN_PROCESO =>
                'badge-primary-soft',

            SincronizacionSaf::ESTADO_PENDIENTE =>
                'badge-secondary-soft',

            default =>
                'badge-secondary-soft',
        };
    };

    /*
    |--------------------------------------------------------------------------
    | Tipo de ejecución
    |--------------------------------------------------------------------------
    */

    $tipoEjecucionLabel = static function (?string $tipo): string {
        return match ($tipo) {
            SincronizacionSaf::TIPO_AUTOMATICA =>
                'Automática',

            SincronizacionSaf::TIPO_MANUAL =>
                'Manual',

            default =>
                $tipo ?: 'No definido',
        };
    };

    /*
    |--------------------------------------------------------------------------
    | Tipo de registro relacionado con el error
    |--------------------------------------------------------------------------
    */

    $tipoRegistroLabel = static function (?string $tipo): string {
        return match ($tipo) {
            SincronizacionSafError::TIPO_REGISTRO_CONSULTOR =>
                'Consultor',

            SincronizacionSafError::TIPO_REGISTRO_CAPACITACION =>
                'Capacitación',

            SincronizacionSafError::TIPO_REGISTRO_GENERAL =>
                'General',

            default =>
                $tipo ?: 'No definido',
        };
    };

    $tipoRegistroBadge = static function (?string $tipo): string {
        return match ($tipo) {
            SincronizacionSafError::TIPO_REGISTRO_CONSULTOR =>
                'badge-primary-soft',

            SincronizacionSafError::TIPO_REGISTRO_CAPACITACION =>
                'badge-warning-soft',

            SincronizacionSafError::TIPO_REGISTRO_GENERAL =>
                'badge-secondary-soft',

            default =>
                'badge-secondary-soft',
        };
    };

    /*
    |--------------------------------------------------------------------------
    | Estado del error
    |--------------------------------------------------------------------------
    */

    $estadoErrorLabel = static function ($error): string {
        return $error->resuelto
            ? 'Resuelto'
            : 'Pendiente';
    };

    $estadoErrorBadge = static function ($error): string {
        return $error->resuelto
            ? 'badge-success-soft'
            : 'badge-danger-soft';
    };

    /*
    |--------------------------------------------------------------------------
    | Usuario ejecutor
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
    | Duración
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
            return $minutos
                . ' min '
                . $segundosRestantes
                . ' s';
        }

        $horas = intdiv($minutos, 60);
        $minutosRestantes = $minutos % 60;

        return $horas
            . ' h '
            . $minutosRestantes
            . ' min';
    };

    /*
    |--------------------------------------------------------------------------
    | Totales de la sincronización
    |--------------------------------------------------------------------------
    */

    $totalRecibidos = (int) (
        $sincronizacionSaf->total_registros_recibidos ?? 0
    );

    $totalProcesados = (int) (
        $sincronizacionSaf->total_registros_procesados ?? 0
    );

    $totalExitosos = (int) (
        $sincronizacionSaf->total_registros_exitosos ?? 0
    );

    $totalConError = (int) (
        $sincronizacionSaf->total_registros_con_error ?? 0
    );

    /*
    |--------------------------------------------------------------------------
    | Consultores
    |--------------------------------------------------------------------------
    */

    $consultoresCreados = (int) (
        $sincronizacionSaf->consultores_creados ?? 0
    );

    $consultoresActualizados = (int) (
        $sincronizacionSaf->consultores_actualizados ?? 0
    );

    $consultoresSinCambios = (int) (
        $sincronizacionSaf->consultores_sin_cambios ?? 0
    );

    $consultoresConError = (int) (
        $sincronizacionSaf->consultores_con_error ?? 0
    );

    /*
    |--------------------------------------------------------------------------
    | Capacitaciones
    |--------------------------------------------------------------------------
    */

    $capacitacionesCreadas = (int) (
        $sincronizacionSaf->capacitaciones_creadas ?? 0
    );

    $capacitacionesActualizadas = (int) (
        $sincronizacionSaf->capacitaciones_actualizadas ?? 0
    );

    $capacitacionesSinCambios = (int) (
        $sincronizacionSaf->capacitaciones_sin_cambios ?? 0
    );

    $capacitacionesDesactivadas = (int) (
        $sincronizacionSaf->capacitaciones_desactivadas ?? 0
    );

    $capacitacionesConError = (int) (
        $sincronizacionSaf->capacitaciones_con_error ?? 0
    );

    /*
    |--------------------------------------------------------------------------
    | Resumen de errores
    |--------------------------------------------------------------------------
    */

    $totalErrores = (int) (
        $resumenErrores->total_errores ?? 0
    );

    $erroresPendientes = (int) (
        $resumenErrores->pendientes ?? 0
    );

    $erroresResueltos = (int) (
        $resumenErrores->resueltos ?? 0
    );

    $erroresConsultores = (int) (
        $resumenErrores->errores_consultores ?? 0
    );

    $erroresCapacitaciones = (int) (
        $resumenErrores->errores_capacitaciones ?? 0
    );

    $erroresGenerales = (int) (
        $resumenErrores->errores_generales ?? 0
    );

    /*
    |--------------------------------------------------------------------------
    | Porcentaje procesado
    |--------------------------------------------------------------------------
    */

    $porcentajeProcesado = $totalRecibidos > 0
        ? min(
            100,
            (int) round(
                ($totalProcesados / $totalRecibidos) * 100
            )
        )
        : 0;
@endphp

{{-- Encabezado --}}
<div class="dashboard-advanced-hero mb-4">
    <div>
        <span class="dashboard-kicker">
            Integración SAF
        </span>

        <h2>
            Sincronización
            #{{ $sincronizacionSaf->id_sincronizacion_saf }}
        </h2>

        <p>
            Consulta el resultado general, los registros procesados y
            las incidencias asociadas a esta ejecución.
        </p>
    </div>

    <div class="dashboard-hero-metric">
        <strong>
            {{ number_format($porcentajeProcesado) }}%
        </strong>

        <span>
            registros procesados
        </span>

        <div class="d-flex gap-2 flex-wrap mt-3">
            <a
                href="{{ route('seg.bitacora-saf.index') }}"
                class="btn btn-outline-secondary"
            >
                <i class="fa-solid fa-arrow-left me-1"></i>
                Volver
            </a>

            <a
                href="{{ route(
                    'seg.bitacora-saf.show',
                    $sincronizacionSaf
                ) }}"
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

{{-- Datos generales --}}
<x-ui.table-card
    title="Información de la ejecución"
    subtitle="Identificación, estado y datos técnicos de la sincronización."
    :items="collect([$sincronizacionSaf])"
    class="mb-4"
>
    <div class="row g-3 mb-4">
        <div class="col-md-6 col-xl-3">
            <div class="dashboard-kpi-card h-100">
                <div>
                    <span>
                        Estado
                    </span>

                    <strong class="fs-5">
                        <span
                            class="badge {{ $estadoBadge(
                                $sincronizacionSaf->estado
                            ) }}"
                        >
                            {{ $estadoLabel(
                                $sincronizacionSaf->estado
                            ) }}
                        </span>
                    </strong>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="dashboard-kpi-card h-100">
                <div>
                    <span>
                        Tipo de ejecución
                    </span>

                    <strong class="fs-5">
                        {{ $tipoEjecucionLabel(
                            $sincronizacionSaf->tipo_ejecucion
                        ) }}
                    </strong>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="dashboard-kpi-card h-100">
                <div>
                    <span>
                        Duración
                    </span>

                    <strong class="fs-5">
                        {{ $duracion($sincronizacionSaf) }}
                    </strong>
                </div>
            </div>
        </div>

        <div class="col-md-6 col-xl-3">
            <div class="dashboard-kpi-card h-100">
                <div>
                    <span>
                        Errores
                    </span>

                    <strong>
                        {{ number_format($totalErrores) }}
                    </strong>
                </div>
            </div>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <tbody>
                <tr>
                    <th style="width: 240px;">
                        Identificador
                    </th>

                    <td>
                        #{{ $sincronizacionSaf
                            ->id_sincronizacion_saf }}
                    </td>
                </tr>

                <tr>
                    <th>
                        UUID
                    </th>

                    <td>
                        <code>
                            {{ $sincronizacionSaf->uuid }}
                        </code>
                    </td>
                </tr>

                <tr>
                    <th>
                        Fecha de inicio
                    </th>

                    <td>
                        {{ $sincronizacionSaf
                            ->fecha_inicio
                            ?->format('d/m/Y H:i:s') ?? '—' }}
                    </td>
                </tr>

                <tr>
                    <th>
                        Fecha de finalización
                    </th>

                    <td>
                        {{ $sincronizacionSaf
                            ->fecha_fin
                            ?->format('d/m/Y H:i:s')
                            ?? 'En ejecución' }}
                    </td>
                </tr>

                <tr>
                    <th>
                        Ejecutada por
                    </th>

                    <td>
                        {{ $nombreUsuario(
                            $sincronizacionSaf->usuarioEjecutor
                        ) }}
                    </td>
                </tr>

                <tr>
                    <th>
                        Registros recibidos
                    </th>

                    <td>
                        {{ number_format($totalRecibidos) }}
                    </td>
                </tr>

                <tr>
                    <th>
                        Registros procesados
                    </th>

                    <td>
                        {{ number_format($totalProcesados) }}
                        de
                        {{ number_format($totalRecibidos) }}

                        <span class="badge badge-primary-soft ms-2">
                            {{ number_format($porcentajeProcesado) }}%
                        </span>
                    </td>
                </tr>

                @if($sincronizacionSaf->mensaje)
                    <tr>
                        <th>
                            Resultado
                        </th>

                        <td>
                            {{ $sincronizacionSaf->mensaje }}
                        </td>
                    </tr>
                @endif
            </tbody>
        </table>
    </div>
</x-ui.table-card>

{{-- Resumen de procesamiento --}}
<x-ui.dashboard-grid
    :columns="4"
    class="mb-4"
>
    <div class="col">
        <x-ui.stat-card
            label="Registros recibidos"
            :value="$totalRecibidos"
            description="Total disponible para procesar"
            icon="fa-inbox"
            variant="primary"
        />
    </div>

    <div class="col">
        <x-ui.stat-card
            label="Registros procesados"
            :value="$totalProcesados"
            description="Registros examinados"
            icon="fa-gears"
            variant="info"
        />
    </div>

    <div class="col">
        <x-ui.stat-card
            label="Registros exitosos"
            :value="$totalExitosos"
            description="Procesados correctamente"
            icon="fa-circle-check"
            variant="success"
        />
    </div>

    <div class="col">
        <x-ui.stat-card
            label="Registros con error"
            :value="$totalConError"
            description="No pudieron sincronizarse"
            icon="fa-circle-exclamation"
            variant="danger"
        />
    </div>
</x-ui.dashboard-grid>

{{-- Consultores --}}
<x-ui.table-card
    title="Resultado de consultores"
    subtitle="Movimientos realizados sobre los expedientes de consultores."
    :items="collect([
        $consultoresCreados,
        $consultoresActualizados,
        $consultoresSinCambios,
        $consultoresConError
    ])"
    class="mb-4"
>
    <x-ui.dashboard-grid :columns="4">
        <div class="col">
            <x-ui.stat-card
                label="Creados"
                :value="$consultoresCreados"
                description="Nuevos consultores"
                icon="fa-user-plus"
                variant="success"
            />
        </div>

        <div class="col">
            <x-ui.stat-card
                label="Actualizados"
                :value="$consultoresActualizados"
                description="Expedientes modificados"
                icon="fa-user-pen"
                variant="info"
            />
        </div>

        <div class="col">
            <x-ui.stat-card
                label="Sin cambios"
                :value="$consultoresSinCambios"
                description="Información ya actualizada"
                icon="fa-user-check"
                variant="primary"
            />
        </div>

        <div class="col">
            <x-ui.stat-card
                label="Con error"
                :value="$consultoresConError"
                description="No pudieron procesarse"
                icon="fa-user-xmark"
                variant="danger"
            />
        </div>
    </x-ui.dashboard-grid>
</x-ui.table-card>

{{-- Capacitaciones --}}
<x-ui.table-card
    title="Resultado de capacitaciones"
    subtitle="Movimientos realizados sobre las capacitaciones FEPADE."
    :items="collect([
        $capacitacionesCreadas,
        $capacitacionesActualizadas,
        $capacitacionesSinCambios,
        $capacitacionesDesactivadas,
        $capacitacionesConError
    ])"
    class="mb-4"
>
    <x-ui.dashboard-grid :columns="4">
        <div class="col">
            <x-ui.stat-card
                label="Creadas"
                :value="$capacitacionesCreadas"
                description="Nuevas capacitaciones"
                icon="fa-graduation-cap"
                variant="success"
            />
        </div>

        <div class="col">
            <x-ui.stat-card
                label="Actualizadas"
                :value="$capacitacionesActualizadas"
                description="Registros modificados"
                icon="fa-pen-to-square"
                variant="info"
            />
        </div>

        <div class="col">
            <x-ui.stat-card
                label="Sin cambios"
                :value="$capacitacionesSinCambios"
                description="Información ya actualizada"
                icon="fa-circle-check"
                variant="primary"
            />
        </div>

        <div class="col">
            <x-ui.stat-card
                label="Desactivadas"
                :value="$capacitacionesDesactivadas"
                description="Registros deshabilitados"
                icon="fa-ban"
                variant="warning"
            />
        </div>
    </x-ui.dashboard-grid>

    @if($capacitacionesConError > 0)
        <div class="alert alert-danger mt-4 mb-0">
            <i class="fa-solid fa-triangle-exclamation me-1"></i>

            Se registraron

            <strong>
                {{ number_format($capacitacionesConError) }}
            </strong>

            capacitaciones con error durante esta sincronización.
        </div>
    @endif
</x-ui.table-card>

{{-- Resumen de errores --}}
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
            label="Errores visibles"
            :value="$errores->total()"
            description="Según filtros aplicados"
            icon="fa-list-check"
            variant="info"
        />
    </div>
</x-ui.dashboard-grid>

{{-- Clasificación de errores --}}
<x-ui.dashboard-grid
    :columns="3"
    class="mb-4"
>
    <div class="col">
        <x-ui.stat-card
            label="Errores de consultores"
            :value="$erroresConsultores"
            description="Relacionados con instructores"
            icon="fa-user-xmark"
            variant="danger"
        />
    </div>

    <div class="col">
        <x-ui.stat-card
            label="Errores de capacitaciones"
            :value="$erroresCapacitaciones"
            description="Relacionados con formación"
            icon="fa-graduation-cap"
            variant="warning"
        />
    </div>

    <div class="col">
        <x-ui.stat-card
            label="Errores generales"
            :value="$erroresGenerales"
            description="Incidencias de ejecución"
            icon="fa-server"
            variant="primary"
        />
    </div>
</x-ui.dashboard-grid>

{{-- Filtros de errores --}}
<x-ui.filter-card
    title="Filtros de incidencias"
    subtitle="Consulta los errores por tipo, estado o contenido del mensaje."
    class="mb-4"
>
    <form
        method="GET"
        action="{{ route(
            'seg.bitacora-saf.show',
            $sincronizacionSaf
        ) }}"
        class="row g-3 align-items-end"
    >
        <div class="col-md-6 col-xl-4">
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
                placeholder="Mensaje, código o identificador externo"
            >

            @error('q')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="col-md-6 col-xl-3">
            <label
                for="tipo_registro"
                class="form-label"
            >
                Tipo de registro
            </label>

            <select
                name="tipo_registro"
                id="tipo_registro"
                class="form-select @error('tipo_registro') is-invalid @enderror"
            >
                <option value="">
                    Todos
                </option>

                @foreach($tiposRegistro as $valor => $etiqueta)
                    <option
                        value="{{ $valor }}"
                        @selected(
                            request('tipo_registro') === $valor
                        )
                    >
                        {{ $etiqueta }}
                    </option>
                @endforeach
            </select>

            @error('tipo_registro')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
        </div>

        <div class="col-md-6 col-xl-2">
            <label
                for="resuelto"
                class="form-label"
            >
                Estado
            </label>

            <select
                name="resuelto"
                id="resuelto"
                class="form-select @error('resuelto') is-invalid @enderror"
            >
                <option value="">
                    Todos
                </option>

                <option
                    value="0"
                    @selected(request('resuelto') === '0')
                >
                    Pendientes
                </option>

                <option
                    value="1"
                    @selected(request('resuelto') === '1')
                >
                    Resueltos
                </option>
            </select>

            @error('resuelto')
                <div class="invalid-feedback">
                    {{ $message }}
                </div>
            @enderror
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
                href="{{ route(
                    'seg.bitacora-saf.show',
                    $sincronizacionSaf
                ) }}"
                class="btn btn-outline-secondary w-100"
            >
                Limpiar
            </a>
        </div>
    </form>
</x-ui.filter-card>

{{-- Tabla de errores --}}
<x-ui.table-card
    title="Incidencias registradas"
    subtitle="Detalle de los errores identificados durante esta sincronización."
    :items="$errores"
    empty-title="Sin errores registrados"
    empty-message="Esta sincronización no contiene incidencias o no existen resultados para los filtros seleccionados."
>
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>
                        Error
                    </th>

                    <th>
                        Tipo
                    </th>

                    <th>
                        Registro relacionado
                    </th>

                    <th>
                        Código
                    </th>

                    <th>
                        Mensaje
                    </th>

                    <th>
                        Estado
                    </th>

                    <th>
                        Fecha
                    </th>
                </tr>
            </thead>

            <tbody>
                @foreach($errores as $error)
                    <tr>
                        <td>
                            <div class="fw-semibold">
                                #{{ $error
                                    ->id_sincronizacion_saf_error }}
                            </div>
                        </td>

                        <td>
                            <span
                                class="badge {{ $tipoRegistroBadge(
                                    $error->tipo_registro
                                ) }}"
                            >
                                {{ $tipoRegistroLabel(
                                    $error->tipo_registro
                                ) }}
                            </span>
                        </td>

                        <td>
                            @if($error->id_registro_origen)
                                <div class="fw-semibold">
                                    {{ $error->id_registro_origen }}
                                </div>
                            @else
                                <span class="text-muted">
                                    —
                                </span>
                            @endif

                            @if($error->identificador_externo)
                                <small class="text-muted d-block">
                                    {{ $error->identificador_externo }}
                                </small>
                            @endif
                        </td>

                        <td>
                            @if($error->codigo_error)
                                <code>
                                    {{ $error->codigo_error }}
                                </code>
                            @else
                                <span class="text-muted">
                                    —
                                </span>
                            @endif
                        </td>

                        <td style="min-width: 280px;">
                            <div>
                                {{ $error->mensaje_error }}
                            </div>

                            @if($error->campo_error)
                                <small class="text-muted d-block mt-1">
                                    Campo:

                                    <strong>
                                        {{ $error->campo_error }}
                                    </strong>
                                </small>
                            @endif
                        </td>

                        <td>
                            <span
                                class="badge {{ $estadoErrorBadge(
                                    $error
                                ) }}"
                            >
                                {{ $estadoErrorLabel($error) }}
                            </span>

                            @if(
                                $error->resuelto
                                && $error->fecha_resolucion
                            )
                                <small class="text-muted d-block mt-1">
                                    {{ $error
                                        ->fecha_resolucion
                                        ->format('d/m/Y H:i') }}
                                </small>
                            @endif
                        </td>

                        <td>
                            <div class="fw-semibold">
                                {{ $error
                                    ->created_at
                                    ?->format('d/m/Y') ?? '—' }}
                            </div>

                            <small class="text-muted">
                                {{ $error
                                    ->created_at
                                    ?->format('H:i:s') ?? '—' }}
                            </small>
                        </td>
                    </tr>

                    @if($error->observacion_resolucion)
                        <tr>
                            <td colspan="7">
                                <div class="alert alert-success mb-0 py-2">
                                    <i class="fa-solid fa-circle-check me-1"></i>

                                    <strong>
                                        Observación de resolución:
                                    </strong>

                                    {{ $error->observacion_resolucion }}
                                </div>
                            </td>
                        </tr>
                    @endif
                @endforeach
            </tbody>
        </table>
    </div>

    @if($errores->hasPages())
        <div class="mt-4">
            {{ $errores->links() }}
        </div>
    @endif
</x-ui.table-card>

@endsection