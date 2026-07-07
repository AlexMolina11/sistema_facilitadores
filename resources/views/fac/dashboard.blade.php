@extends('layouts.app')

@section('title', 'Dashboard avanzado | Facilitadores FEPADE')
@section('page-title', 'Dashboard avanzado')
@section('page-subtitle', 'KPIs, gráficos, filtros y estadísticas consolidadas')

@section('content')

<x-ui.page-header
    title="Dashboard funcional avanzado"
    subtitle="Indicadores consolidados para seguimiento administrativo, análisis de perfiles y reportería."
>
    <div class="d-flex gap-2 flex-wrap">
        <a href="{{ route('fac.dashboard.exportar-csv', request()->query()) }}" class="btn btn-outline-primary">
            <i class="fa-solid fa-file-csv me-1"></i> Exportar CSV
        </a>
        <button type="button" class="btn btn-navy" onclick="window.print()">
            <i class="fa-solid fa-print me-1"></i> Imprimir
        </button>
    </div>
</x-ui.page-header>

<div class="dashboard-advanced-hero mb-4">
    <div>
        <span class="dashboard-kicker">Fase 11</span>
        <h2>Lectura ejecutiva de la base de consultores</h2>
        <p>
            Visualiza registros, estado de perfiles, cobertura de información, especialidades,
            disponibilidad, idiomas y comportamiento de altas por periodo.
        </p>
    </div>
    <div class="dashboard-hero-metric">
        <strong>{{ $totalConsultores }}</strong>
        <span>consultores filtrados</span>
    </div>
</div>

<x-ui.filter-card
    title="Filtros de análisis"
    subtitle="Combina periodo, estado, país, sexo, área de especialización y disponibilidad."
    class="mb-4 dashboard-print-hide"
>
    <form method="GET" action="{{ route('fac.dashboard') }}" class="row g-3 align-items-end">
        <div class="col-md-3 col-xl-2">
            <label class="form-label">Periodo</label>
            <select name="filtro_fecha" class="form-select">
                <option value="todos" @selected($filtros['filtro_fecha'] === 'todos')>Todos</option>
                <option value="hoy" @selected($filtros['filtro_fecha'] === 'hoy')>Hoy</option>
                <option value="7_dias" @selected($filtros['filtro_fecha'] === '7_dias')>Últimos 7 días</option>
                <option value="30_dias" @selected($filtros['filtro_fecha'] === '30_dias')>Últimos 30 días</option>
                <option value="este_mes" @selected($filtros['filtro_fecha'] === 'este_mes')>Este mes</option>
                <option value="mes_pasado" @selected($filtros['filtro_fecha'] === 'mes_pasado')>Mes pasado</option>
                <option value="rango" @selected($filtros['filtro_fecha'] === 'rango')>Rango personalizado</option>
            </select>
        </div>

        <div class="col-md-3 col-xl-2">
            <label class="form-label">Fecha inicio</label>
            <input type="date" name="fecha_inicio" class="form-control" value="{{ request('fecha_inicio') }}">
        </div>

        <div class="col-md-3 col-xl-2">
            <label class="form-label">Fecha fin</label>
            <input type="date" name="fecha_fin" class="form-control" value="{{ request('fecha_fin') }}">
        </div>

        <div class="col-md-3 col-xl-2">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select">
                <option value="todos" @selected($filtros['estado'] === 'todos')>Todos</option>
                <option value="activos" @selected($filtros['estado'] === 'activos')>Activos</option>
                <option value="inactivos" @selected($filtros['estado'] === 'inactivos')>Inactivos</option>
            </select>
        </div>

        <div class="col-md-4 col-xl-2">
            <label class="form-label">País</label>
            <select name="id_pais" class="form-select">
                <option value="">Todos</option>
                @foreach($catalogos['paises'] as $pais)
                    <option value="{{ $pais->id_pais }}" @selected($filtros['id_pais'] === $pais->id_pais)>
                        {{ $pais->nombre_pais }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4 col-xl-2">
            <label class="form-label">Sexo</label>
            <select name="id_sexo" class="form-select">
                <option value="">Todos</option>
                @foreach($catalogos['sexos'] as $sexo)
                    <option value="{{ $sexo->id_sexo }}" @selected($filtros['id_sexo'] === $sexo->id_sexo)>
                        {{ $sexo->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-6">
            <label class="form-label">Área de especialización</label>
            <select name="id_area_especializacion" class="form-select">
                <option value="">Todas</option>
                @foreach($catalogos['areas'] as $area)
                    <option value="{{ $area->id_area_especializacion }}" @selected($filtros['id_area_especializacion'] === $area->id_area_especializacion)>
                        {{ $area->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Disponibilidad</label>
            <select name="id_tipo_disponibilidad" class="form-select">
                <option value="">Todas</option>
                @foreach($catalogos['disponibilidades'] as $tipo)
                    <option value="{{ $tipo->id_tipo_disponibilidad }}" @selected($filtros['id_tipo_disponibilidad'] === $tipo->id_tipo_disponibilidad)>
                        {{ $tipo->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3 fepade-filter-actions">
            <button type="submit" class="btn btn-navy w-100">
                <i class="fa-solid fa-filter me-1"></i> Aplicar
            </button>
            <a href="{{ route('fac.dashboard') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
        </div>
    </form>
</x-ui.filter-card>

<x-ui.dashboard-grid :columns="4" class="mb-4">
    <div class="col"><x-ui.stat-card label="Consultores" :value="$totalConsultores" description="Total según filtros" icon="fa-users" variant="primary" /></div>
    <div class="col"><x-ui.stat-card label="Activos" value="{{ $consultoresActivos }} / {{ $porcentajeActivos }}%" description="Perfiles habilitados" icon="fa-user-check" variant="success" /></div>
    <div class="col"><x-ui.stat-card label="Experiencia promedio" value="{{ $experienciaPromedio }} años" description="Promedio estimado" icon="fa-briefcase" variant="info" /></div>
    <div class="col"><x-ui.stat-card label="Idiomas únicos" :value="$idiomasUnicos" description="Idiomas registrados" icon="fa-language" variant="primary" /></div>
</x-ui.dashboard-grid>

<div class="dashboard-kpi-grid mb-4">
    @foreach($resumenCobertura as $item)
        <div class="dashboard-kpi-card">
            <div class="d-flex justify-content-between gap-3">
                <div>
                    <span>{{ $item['nombre'] }}</span>
                    <strong>{{ $item['total'] }}</strong>
                </div>
                <div class="dashboard-kpi-percent">{{ $item['porcentaje'] }}%</div>
            </div>
            <div class="dashboard-progress-track mt-3">
                <div class="dashboard-progress-fill" style="width: {{ $item['porcentaje'] }}%"></div>
            </div>
        </div>
    @endforeach
</div>

<x-ui.dashboard-grid :columns="4" class="mb-4">
    <div class="col"><x-ui.stat-card label="Vigentes" :value="$consultoresVigentes" description="Marcados como vigentes" icon="fa-id-badge" variant="success" /></div>
    <div class="col"><x-ui.stat-card label="Invitaciones activas" :value="$invitacionesActivas" description="Enlaces disponibles" icon="fa-envelope-open-text" variant="success" /></div>
    <div class="col"><x-ui.stat-card label="Invitaciones vencidas" :value="$invitacionesVencidas" description="Enlaces expirados" icon="fa-clock" variant="warning" /></div>
    <div class="col"><x-ui.stat-card label="Invitaciones usadas" :value="$invitacionesUsadas" description="Con uso registrado" icon="fa-check-double" variant="info" /></div>
</x-ui.dashboard-grid>

<div class="row g-4 mb-4">
    <div class="col-xl-8">
        <x-ui.chart-card title="Altas de consultores por mes" subtitle="Tendencia de registros dentro del periodo filtrado.">
            <canvas id="chartRegistrosMes"></canvas>
        </x-ui.chart-card>
    </div>
    <div class="col-xl-4">
        <x-ui.chart-card title="Estado de consultores" subtitle="Activos e inactivos.">
            <canvas id="chartEstado"></canvas>
        </x-ui.chart-card>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <x-ui.chart-card title="Distribución por país" subtitle="Top 10 países registrados."><canvas id="chartPaises"></canvas></x-ui.chart-card>
    </div>
    <div class="col-lg-6">
        <x-ui.chart-card title="Top 10 habilidades técnicas" subtitle="Habilidades más frecuentes."><canvas id="chartHabilidades"></canvas></x-ui.chart-card>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-4">
        <x-ui.chart-card title="Sexo" subtitle="Distribución de perfiles."><canvas id="chartSexo"></canvas></x-ui.chart-card>
    </div>
    <div class="col-lg-4">
        <x-ui.chart-card title="Nivel académico" subtitle="Agrupado por atestados."><canvas id="chartNivelAcademico"></canvas></x-ui.chart-card>
    </div>
    <div class="col-lg-4">
        <x-ui.chart-card title="Disponibilidad" subtitle="Tipos declarados."><canvas id="chartDisponibilidad"></canvas></x-ui.chart-card>
    </div>
</div>

<div class="row g-4 mb-4">
    <div class="col-lg-6">
        <x-ui.table-card title="Áreas de especialización" subtitle="Ranking de áreas con mayor presencia." :items="$topAreasEspecializacion" empty-title="Sin áreas registradas" empty-message="Cuando existan áreas asignadas aparecerán aquí.">
            <table class="table table-hover align-middle">
                <thead><tr><th>Área</th><th class="text-end">Consultores</th></tr></thead>
                <tbody>
                    @foreach($topAreasEspecializacion as $area)
                        <tr><td class="fw-semibold">{{ $area->nombre }}</td><td class="text-end"><span class="badge badge-primary-soft">{{ $area->total }}</span></td></tr>
                    @endforeach
                </tbody>
            </table>
        </x-ui.table-card>
    </div>

    <div class="col-lg-6">
        <x-ui.table-card title="Idiomas más frecuentes" subtitle="Ranking de idiomas registrados." :items="$idiomasFrecuentes" empty-title="Sin idiomas registrados" empty-message="Cuando los consultores agreguen idiomas aparecerán aquí.">
            <table class="table table-hover align-middle">
                <thead><tr><th>Idioma</th><th class="text-end">Consultores</th></tr></thead>
                <tbody>
                    @foreach($idiomasFrecuentes as $idioma)
                        <tr><td class="fw-semibold">{{ $idioma->nombre }}</td><td class="text-end"><span class="badge badge-primary-soft">{{ $idioma->total }}</span></td></tr>
                    @endforeach
                </tbody>
            </table>
        </x-ui.table-card>
    </div>
</div>

<x-ui.table-card title="Últimos consultores registrados" subtitle="Perfiles más recientes según los filtros aplicados." :items="$ultimosConsultores" empty-title="No hay registros" empty-message="No se encontraron consultores para los filtros seleccionados.">
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>Consultor</th>
                <th>País</th>
                <th>Estado</th>
                <th class="text-end">Registro</th>
            </tr>
        </thead>
        <tbody>
            @foreach($ultimosConsultores as $consultor)
                <tr>
                    <td>
                        <div class="fw-semibold">{{ $consultor->nombres }} {{ $consultor->apellidos }}</div>
                        <small class="text-muted">ID {{ $consultor->id_consultor }}</small>
                    </td>
                    <td>{{ $consultor->nombre_pais ?? 'Sin país' }}</td>
                    <td>
                        @if($consultor->activo)
                            <span class="badge badge-success-soft">Activo</span>
                        @else
                            <span class="badge badge-warning-soft">Inactivo</span>
                        @endif
                    </td>
                    <td class="text-end">{{ $consultor->created_at ? \Carbon\Carbon::parse($consultor->created_at)->format('d/m/Y') : '—' }}</td>
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
    const sexo = @json($distribucionSexo);
    const registrosMes = @json($registrosPorMes);

    const chartDefaults = {
        responsive: true,
        maintainAspectRatio: false,
        plugins: { legend: { position: 'bottom' } },
        scales: { y: { beginAtZero: true, ticks: { precision: 0 } } }
    };

    const emptyData = [{ nombre: 'Sin datos', total: 0 }];
    const safe = items => (items && items.length) ? items : emptyData;

    new Chart(document.getElementById('chartRegistrosMes'), {
        type: 'line',
        data: {
            labels: safe(registrosMes).map(item => item.nombre),
            datasets: [{ label: 'Registros', data: safe(registrosMes).map(item => item.total), tension: .35, fill: true }]
        },
        options: chartDefaults
    });

    new Chart(document.getElementById('chartEstado'), {
        type: 'doughnut',
        data: {
            labels: ['Activos', 'Inactivos'],
            datasets: [{ data: [{{ $consultoresActivos }}, {{ $consultoresInactivos }}] }]
        },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });

    new Chart(document.getElementById('chartPaises'), {
        type: 'doughnut',
        data: { labels: safe(paises).map(item => item.nombre), datasets: [{ data: safe(paises).map(item => item.total) }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });

    new Chart(document.getElementById('chartHabilidades'), {
        type: 'bar',
        data: { labels: safe(habilidades).map(item => item.nombre), datasets: [{ label: 'Consultores', data: safe(habilidades).map(item => item.total) }] },
        options: { ...chartDefaults, indexAxis: 'y' }
    });

    new Chart(document.getElementById('chartSexo'), {
        type: 'doughnut',
        data: { labels: safe(sexo).map(item => item.nombre), datasets: [{ data: safe(sexo).map(item => item.total) }] },
        options: { responsive: true, maintainAspectRatio: false, plugins: { legend: { position: 'bottom' } } }
    });

    new Chart(document.getElementById('chartNivelAcademico'), {
        type: 'bar',
        data: { labels: safe(niveles).map(item => item.nombre), datasets: [{ label: 'Consultores', data: safe(niveles).map(item => item.total) }] },
        options: chartDefaults
    });

    new Chart(document.getElementById('chartDisponibilidad'), {
        type: 'bar',
        data: { labels: safe(disponibilidad).map(item => item.nombre), datasets: [{ label: 'Consultores', data: safe(disponibilidad).map(item => item.total) }] },
        options: chartDefaults
    });
});
</script>
@endpush
