@extends('layouts.app')

@section('title', 'Bitácora SAF | Facilitadores FEPADE')
@section('page-title', 'Bitácora SAF')
@section('page-subtitle', 'Seguimiento operativo de la integración SAF')

@section('content')

@php
    use App\Modules\Fac\Models\SincronizacionSaf;

    $estadoBadge = static function (?string $estado): string {
        return match ($estado) {
            SincronizacionSaf::ESTADO_COMPLETADA =>
                'badge-success-soft',

            SincronizacionSaf::ESTADO_COMPLETADA_CON_ERRORES =>
                'badge-warning-soft',

            SincronizacionSaf::ESTADO_FALLIDA =>
                'badge-danger-soft',

            default =>
                'badge-muted-soft',
        };
    };

    $estadoTexto = static function (?string $estado): string {
        return match ($estado) {
            SincronizacionSaf::ESTADO_COMPLETADA =>
                'Completada',

            SincronizacionSaf::ESTADO_COMPLETADA_CON_ERRORES =>
                'Con errores',

            SincronizacionSaf::ESTADO_FALLIDA =>
                'Fallida',

            SincronizacionSaf::ESTADO_EN_PROCESO =>
                'En proceso',

            default =>
                $estado ?? 'Pendiente',
        };
    };
@endphp

<div class="dashboard-advanced-hero mb-4">
    <div>
        <span class="dashboard-kicker">
            Integración SAF
        </span>

        <h2>
            Actividad de sincronizaciones
        </h2>

        <p>
            Visualiza cuándo se recibieron nuevos registros,
            qué sincronizaciones tuvieron errores y cuáles
            requieren revisión.
        </p>
    </div>

    <div class="dashboard-hero-metric">
        <strong>
            {{ $ultimaSincronizacion?->fecha_inicio
                ?->format('d/m/Y') ?? '—' }}
        </strong>

        <span>
            última sincronización
        </span>
    </div>
</div>

<x-ui.filter-card
    title="Periodo de análisis"
    subtitle="Filtra la actividad de sincronización por fecha o resultado."
    class="mb-4 dashboard-print-hide"
>
    <form
        method="GET"
        action="{{ route('seg.bitacora-saf.index') }}"
        class="row g-3 align-items-end"
    >
        <div class="col-md-3">
            <label class="form-label">
                Fecha inicio
            </label>

            <input
                type="date"
                name="desde"
                class="form-control"
                value="{{ request('desde') }}"
            >
        </div>

        <div class="col-md-3">
            <label class="form-label">
                Fecha fin
            </label>

            <input
                type="date"
                name="hasta"
                class="form-control"
                value="{{ request('hasta') }}"
            >
        </div>

        <div class="col-md-3">
            <label class="form-label">
                Resultado
            </label>

            <select
                name="estado"
                class="form-select"
            >
                <option value="">
                    Todos
                </option>

                @foreach($estados as $valor => $texto)
                    <option
                        value="{{ $valor }}"
                        @selected(
                            request('estado') === $valor
                        )
                    >
                        {{ $texto }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 fepade-filter-actions">
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

<x-ui.dashboard-grid
    :columns="3"
    class="mb-4"
>
    <div class="col">
        <x-ui.stat-card
            label="Consultores ingresados"
            :value="(int) ($totalesPeriodo->consultores_creados ?? 0)"
            description="Nuevos consultores en el periodo"
            icon="fa-user-plus"
            variant="success"
        />
    </div>

    <div class="col">
        <x-ui.stat-card
            label="Sincronizaciones con error"
            :value="(int) ($totalesPeriodo->sincronizaciones_con_error ?? 0)"
            description="Ejecuciones que requieren revisión"
            icon="fa-triangle-exclamation"
            variant="warning"
        />
    </div>

    <div class="col">
        <x-ui.stat-card
            label="Registros con error"
            :value="(int) ($totalesPeriodo->registros_con_error ?? 0)"
            description="Registros que no pudieron procesarse"
            icon="fa-circle-xmark"
            variant="danger"
        />
    </div>
</x-ui.dashboard-grid>

<div class="mb-4">
    <x-ui.chart-card
        title="Actividad SAF por día"
        subtitle="Consultores y capacitaciones creadas, junto con los registros que presentaron error."
    >
        <canvas id="chartActividadSaf"></canvas>
    </x-ui.chart-card>
</div>

<x-ui.table-card
    title="Sincronizaciones"
    subtitle="Historial compacto de ejecuciones realizadas."
    :items="$sincronizaciones"
    empty-title="Sin sincronizaciones"
    empty-message="No existen ejecuciones para el periodo seleccionado."
    class="mb-4"
>
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Resultado</th>
                <th class="text-end">
                    Consultores
                </th>
                <th class="text-end">
                    Capacitaciones
                </th>
                <th class="text-end">
                    Errores
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
                            {{ $sincronizacion->fecha_inicio
                                ?->format('d/m/Y') ?? '—' }}
                        </div>

                        <small class="text-muted">
                            {{ $sincronizacion->fecha_inicio
                                ?->format('H:i') ?? '' }}
                        </small>
                    </td>

                    <td>
                        <span
                            class="badge {{ $estadoBadge(
                                $sincronizacion->estado
                            ) }}"
                        >
                            {{ $estadoTexto(
                                $sincronizacion->estado
                            ) }}
                        </span>
                    </td>

                    <td class="text-end">
                        <strong>
                            {{ number_format(
                                (int) $sincronizacion
                                    ->consultores_creados
                            ) }}
                        </strong>

                        @if(
                            $sincronizacion
                                ->consultores_actualizados > 0
                        )
                            <small class="text-muted d-block">
                                +{{ $sincronizacion
                                    ->consultores_actualizados }}
                                actualizados
                            </small>
                        @endif
                    </td>

                    <td class="text-end">
                        <strong>
                            {{ number_format(
                                (int) $sincronizacion
                                    ->capacitaciones_creadas
                            ) }}
                        </strong>

                        @if(
                            $sincronizacion
                                ->capacitaciones_actualizadas > 0
                        )
                            <small class="text-muted d-block">
                                +{{ $sincronizacion
                                    ->capacitaciones_actualizadas }}
                                actualizadas
                            </small>
                        @endif
                    </td>

                    <td class="text-end">
                        @if($sincronizacion->errores_count > 0)
                            <span class="badge badge-danger-soft">
                                {{ $sincronizacion->errores_count }}
                            </span>
                        @else
                            <span class="badge badge-success-soft">
                                0
                            </span>
                        @endif
                    </td>

                    <td class="text-end">
                        <a
                            href="{{ route(
                                'seg.bitacora-saf.show',
                                [
                                    'sincronizacionSaf' =>
                                        $sincronizacion,

                                    'vista' =>
                                        $sincronizacion->errores_count > 0
                                            ? 'errores'
                                            : 'consultores',
                                ]
                            ) }}"
                            class="btn btn-sm btn-outline-primary"
                        >
                            @if($sincronizacion->errores_count > 0)
                                <i class="fa-solid fa-triangle-exclamation me-1"></i>
                                Revisar errores
                            @else
                                <i class="fa-solid fa-list-check me-1"></i>
                                Ver registros
                            @endif
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>

    @if($sincronizaciones->hasPages())
        <div class="mt-4">
            {{ $sincronizaciones->links() }}
        </div>
    @endif
</x-ui.table-card>

<x-ui.table-card
    title="Errores pendientes"
    subtitle="Incidencias recientes que todavía requieren revisión."
    :items="$erroresPendientesRecientes"
    empty-title="Sin errores pendientes"
    empty-message="Actualmente no existen incidencias pendientes."
>
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>Fecha</th>
                <th>Tipo</th>
                <th>Registro</th>
                <th>Motivo</th>
                <th class="text-end">
                    Acción
                </th>
            </tr>
        </thead>

        <tbody>
            @foreach(
                $erroresPendientesRecientes as $error
            )
                <tr>
                    <td>
                        {{ $error->created_at
                            ?->format('d/m/Y H:i') ?? '—' }}
                    </td>

                    <td>
                        <span class="badge badge-warning-soft">
                            {{ $error->tipo_registro }}
                        </span>
                    </td>

                    <td>
                        {{ $error->id_registro_externo ?? '—' }}
                    </td>

                    <td>
                        {{ \Illuminate\Support\Str::limit(
                            $error->mensaje,
                            90
                        ) }}
                    </td>

                    <td class="text-end">
                        <a
                            href="{{ route(
                                'seg.bitacora-saf.show',
                                [
                                    'sincronizacionSaf' =>
                                        $error->id_sincronizacion_saf,

                                    'vista' =>
                                        'errores',
                                ]
                            ) }}"
                            class="btn btn-sm btn-outline-primary"
                        >
                            Revisar
                        </a>
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-ui.table-card>

@endsection

@push('scripts')
<script src="https://cdn.jsdelivr.net/npm/chart.js"></script>

<script>
document.addEventListener('DOMContentLoaded', function () {
    const actividad = @json($tendencia);

    const canvas = document.getElementById(
        'chartActividadSaf'
    );

    if (!canvas) {
        return;
    }

    new Chart(canvas, {
        type: 'line',

        data: {
            labels: actividad.map(
                item => item.fecha
            ),

            datasets: [
                {
                    label: 'Consultores ingresados',
                    data: actividad.map(
                        item => item.consultores
                    ),
                    tension: .35,
                    fill: false
                },
                {
                    label: 'Capacitaciones creadas',
                    data: actividad.map(
                        item => item.capacitaciones
                    ),
                    tension: .35,
                    fill: false
                },
                {
                    label: 'Registros con error',
                    data: actividad.map(
                        item => item.errores
                    ),
                    tension: .35,
                    fill: false
                }
            ]
        },

        options: {
            responsive: true,
            maintainAspectRatio: false,

            plugins: {
                legend: {
                    position: 'bottom'
                }
            },

            scales: {
                y: {
                    beginAtZero: true,

                    ticks: {
                        precision: 0
                    }
                }
            }
        }
    });
});
</script>
@endpush