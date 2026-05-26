@extends('layouts.app')

@section('title', 'Dashboard | Facilitadores FEPADE')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Indicadores consolidados del sistema de facilitadores')

@section('content')

<x-ui.page-header 
    title="Panel de control"
    subtitle="Resumen administrativo de consultores, invitaciones, experiencia, idiomas y disponibilidad."
/>

<form method="GET" action="{{ url('/dashboard') }}" class="dashboard-filter-card mb-4">
    <div class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">Filtro de fecha</label>
            <select name="filtro_fecha" class="form-select" onchange="this.form.submit()">
                <option value="todos" {{ $filtroFecha === 'todos' ? 'selected' : '' }}>Todos</option>
                <option value="hoy" {{ $filtroFecha === 'hoy' ? 'selected' : '' }}>Hoy</option>
                <option value="7_dias" {{ $filtroFecha === '7_dias' ? 'selected' : '' }}>Últimos 7 días</option>
                <option value="30_dias" {{ $filtroFecha === '30_dias' ? 'selected' : '' }}>Últimos 30 días</option>
                <option value="este_mes" {{ $filtroFecha === 'este_mes' ? 'selected' : '' }}>Este mes</option>
                <option value="mes_pasado" {{ $filtroFecha === 'mes_pasado' ? 'selected' : '' }}>Mes pasado</option>
                <option value="rango" {{ $filtroFecha === 'rango' ? 'selected' : '' }}>Rango personalizado</option>
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

        <div class="col-md-3">
            <button type="submit" class="btn btn-fepade w-100">
                Aplicar filtros
            </button>
        </div>
    </div>
</form>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <x-ui.stat-card 
            label="Consultores registrados" 
            :value="$totalConsultores" 
            description="Total de perfiles creados" 
        />
    </div>

    <div class="col-md-3">
        <x-ui.stat-card 
            label="Consultores activos" 
            :value="$consultoresActivos" 
            description="Perfiles disponibles" 
        />
    </div>

    <div class="col-md-3">
        <x-ui.stat-card 
            label="Consultores inactivos" 
            :value="$consultoresInactivos" 
            description="Perfiles desactivados" 
        />
    </div>

    <div class="col-md-3">
        <x-ui.stat-card 
            label="Experiencia promedio" 
            value="{{ $experienciaPromedio }} años" 
            description="Promedio estimado por consultor" 
        />
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <x-ui.stat-card 
            label="Idiomas únicos" 
            :value="$idiomasUnicos" 
            description="Idiomas registrados en perfiles" 
        />
    </div>

    <div class="col-md-3">
        <x-ui.stat-card 
            label="Invitaciones activas" 
            :value="$invitacionesActivas" 
            description="Enlaces disponibles" 
        />
    </div>

    <div class="col-md-3">
        <x-ui.stat-card 
            label="Invitaciones vencidas" 
            :value="$invitacionesVencidas" 
            description="Enlaces expirados" 
        />
    </div>

    <div class="col-md-3">
        <x-ui.stat-card 
            label="Invitaciones usadas" 
            :value="$invitacionesUsadas" 
            description="Invitaciones con uso registrado" 
        />
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div>
                    <h5>Distribución por país</h5>
                    <p>Consultores agrupados según país registrado.</p>
                </div>
            </div>

            <div class="dashboard-chart-wrap">
                <canvas id="chartPaises"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div>
                    <h5>Top 10 habilidades</h5>
                    <p>Habilidades más frecuentes entre consultores.</p>
                </div>
            </div>

            <div class="dashboard-chart-wrap">
                <canvas id="chartHabilidades"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div>
                    <h5>Experiencia por nivel académico</h5>
                    <p>Consultores agrupados por nivel educativo registrado.</p>
                </div>
            </div>

            <div class="dashboard-chart-wrap">
                <canvas id="chartNivelAcademico"></canvas>
            </div>
        </div>
    </div>

    <div class="col-lg-6">
        <div class="dashboard-card">
            <div class="dashboard-card-header">
                <div>
                    <h5>Disponibilidad</h5>
                    <p>Tipos de disponibilidad declarados por consultores.</p>
                </div>
            </div>

            <div class="dashboard-chart-wrap">
                <canvas id="chartDisponibilidad"></canvas>
            </div>
        </div>
    </div>
</div>

<div class="dashboard-card mb-4">
    <div class="dashboard-card-header">
        <div>
            <h5>Idiomas más frecuentes</h5>
            <p>Ranking de idiomas registrados en los perfiles.</p>
        </div>
    </div>

    <div class="table-responsive">
        <table class="table align-middle mb-0">
            <thead>
                <tr>
                    <th>Idioma</th>
                    <th class="text-end">Consultores</th>
                </tr>
            </thead>
            <tbody>
                @forelse($idiomasFrecuentes as $idioma)
                    <tr>
                        <td>{{ $idioma->nombre }}</td>
                        <td class="text-end fw-bold">{{ $idioma->total }}</td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="2" class="text-muted text-center py-4">
                            No hay idiomas registrados todavía.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>
</div>

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