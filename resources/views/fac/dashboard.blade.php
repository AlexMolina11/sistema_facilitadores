@extends('layouts.app')

@section('title', 'Dashboard | Facilitadores FEPADE')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Indicadores consolidados del sistema de facilitadores')

@section('content')

<x-ui.page-header
    title="Panel de control"
    subtitle="Resumen administrativo de consultores, invitaciones, experiencia, idiomas y disponibilidad."
/>

<x-ui.filter-card
    title="Filtros del dashboard"
    subtitle="Ajusta el periodo de análisis para los indicadores principales."
    class="mb-4"
>
    <form method="GET" action="{{ url('/dashboard') }}" class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">Filtro de fecha</label>
            <select name="filtro_fecha" class="form-select" onchange="this.form.submit()">
                <option value="todos" @selected($filtroFecha === 'todos')>Todos</option>
                <option value="hoy" @selected($filtroFecha === 'hoy')>Hoy</option>
                <option value="7_dias" @selected($filtroFecha === '7_dias')>Últimos 7 días</option>
                <option value="30_dias" @selected($filtroFecha === '30_dias')>Últimos 30 días</option>
                <option value="este_mes" @selected($filtroFecha === 'este_mes')>Este mes</option>
                <option value="mes_pasado" @selected($filtroFecha === 'mes_pasado')>Mes pasado</option>
                <option value="rango" @selected($filtroFecha === 'rango')>Rango personalizado</option>
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Fecha inicio</label>
            <input
                type="date"
                name="fecha_inicio"
                class="form-control"
                value="{{ request('fecha_inicio') }}"
            >
        </div>

        <div class="col-md-3">
            <label class="form-label">Fecha fin</label>
            <input
                type="date"
                name="fecha_fin"
                class="form-control"
                value="{{ request('fecha_fin') }}"
            >
        </div>

        <div class="col-md-3 fepade-filter-actions">
            <button type="submit" class="btn btn-navy w-100">
                <i class="fa-solid fa-filter me-1"></i>
                Aplicar filtros
            </button>

            <a href="{{ url('/dashboard') }}" class="btn btn-outline-secondary w-100">
                Limpiar
            </a>
        </div>
    </form>
</x-ui.filter-card>

<x-ui.dashboard-grid :columns="4" class="mb-4">
    <div class="col">
        <x-ui.stat-card
            label="Consultores registrados"
            :value="$totalConsultores"
            description="Total de perfiles creados"
            icon="fa-users"
            variant="primary"
        />
    </div>

    <div class="col">
        <x-ui.stat-card
            label="Consultores activos"
            :value="$consultoresActivos"
            description="Perfiles disponibles"
            icon="fa-user-check"
            variant="success"
        />
    </div>

    <div class="col">
        <x-ui.stat-card
            label="Consultores inactivos"
            :value="$consultoresInactivos"
            description="Perfiles desactivados"
            icon="fa-user-slash"
            variant="warning"
        />
    </div>

    <div class="col">
        <x-ui.stat-card
            label="Experiencia promedio"
            value="{{ $experienciaPromedio }} años"
            description="Promedio estimado por consultor"
            icon="fa-briefcase"
            variant="info"
        />
    </div>
</x-ui.dashboard-grid>

<x-ui.dashboard-grid :columns="4" class="mb-4">
    <div class="col">
        <x-ui.stat-card
            label="Idiomas únicos"
            :value="$idiomasUnicos"
            description="Idiomas registrados en perfiles"
            icon="fa-language"
            variant="primary"
        />
    </div>

    <div class="col">
        <x-ui.stat-card
            label="Invitaciones activas"
            :value="$invitacionesActivas"
            description="Enlaces disponibles"
            icon="fa-envelope-open-text"
            variant="success"
        />
    </div>

    <div class="col">
        <x-ui.stat-card
            label="Invitaciones vencidas"
            :value="$invitacionesVencidas"
            description="Enlaces expirados"
            icon="fa-clock"
            variant="warning"
        />
    </div>

    <div class="col">
        <x-ui.stat-card
            label="Invitaciones usadas"
            :value="$invitacionesUsadas"
            description="Invitaciones con uso registrado"
            icon="fa-check-double"
            variant="info"
        />
    </div>
</x-ui.dashboard-grid>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <x-ui.chart-card
            title="Distribución por país"
            subtitle="Consultores agrupados según país registrado."
        >
            <canvas id="chartPaises"></canvas>
        </x-ui.chart-card>
    </div>

    <div class="col-lg-6">
        <x-ui.chart-card
            title="Top 10 habilidades técnicas"
            subtitle="Habilidades técnicas más frecuentes entre consultores."
        >
            <canvas id="chartHabilidades"></canvas>
        </x-ui.chart-card>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <x-ui.chart-card
            title="Experiencia por nivel académico"
            subtitle="Consultores agrupados por nivel educativo registrado."
        >
            <canvas id="chartNivelAcademico"></canvas>
        </x-ui.chart-card>
    </div>

    <div class="col-lg-6">
        <x-ui.chart-card
            title="Disponibilidad"
            subtitle="Tipos de disponibilidad declarados por consultores."
        >
            <canvas id="chartDisponibilidad"></canvas>
        </x-ui.chart-card>
    </div>
</div>

<x-ui.table-card
    title="Idiomas más frecuentes"
    subtitle="Ranking de idiomas registrados en los perfiles."
    :items="$idiomasFrecuentes"
    empty-title="No hay idiomas registrados todavía."
    empty-message="Cuando los consultores agreguen idiomas, aparecerán en este ranking."
    class="mb-4"
>
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>Idioma</th>
                <th class="text-end">Consultores</th>
            </tr>
        </thead>

        <tbody>
            @foreach($idiomasFrecuentes as $idioma)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $idioma->nombre }}</div>
                    </td>
                    <td class="text-end">
                        <span class="badge badge-primary-soft">
                            {{ $idioma->total }}
                        </span>
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
    const paises = @json($distribucionPais);
    const habilidades = @json($topHabilidades);
    const niveles = @json($experienciaPorNivel);
    const disponibilidad = @json($disponibilidad);

    const chartDefaults = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: {
            legend: {
                position: 'bottom'
            }
        }
    };

    new Chart(document.getElementById('chartPaises'), {
        type: 'doughnut',
        data: {
            labels: paises.map(item => item.nombre),
            datasets: [{
                data: paises.map(item => item.total)
            }]
        },
        options: chartDefaults
    });

    new Chart(document.getElementById('chartHabilidades'), {
        type: 'bar',
        data: {
            labels: habilidades.map(item => item.nombre),
            datasets: [{
                label: 'Consultores',
                data: habilidades.map(item => item.total)
            }]
        },
        options: {
            ...chartDefaults,
            indexAxis: 'y'
        }
    });

    new Chart(document.getElementById('chartNivelAcademico'), {
        type: 'bar',
        data: {
            labels: niveles.map(item => item.nombre),
            datasets: [{
                label: 'Consultores',
                data: niveles.map(item => item.total)
            }]
        },
        options: chartDefaults
    });

    new Chart(document.getElementById('chartDisponibilidad'), {
        type: 'bar',
        data: {
            labels: disponibilidad.map(item => item.nombre),
            datasets: [{
                label: 'Consultores',
                data: disponibilidad.map(item => item.total)
            }]
        },
        options: chartDefaults
    });
});
</script>
@endpush