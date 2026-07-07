@extends('layouts.app')

@section('title', 'Plantillas CV | Facilitadores FEPADE')
@section('page-title', 'Plantillas CV')
@section('page-subtitle', 'Catálogo de plantillas disponibles para exportación de hojas de vida.')

@section('content')

<x-ui.page-header title="Plantillas CV" subtitle="Administración de formatos de exportación PDF.">
    <a href="{{ route('fac.catalogos.cv-plantillas.create') }}" class="btn btn-fepade">
        Nueva plantilla
    </a>
</x-ui.page-header>

<div class="fepade-card">
    <div class="table-responsive">
        <table class="table table-hover align-middle">
            <thead>
                <tr>
                    <th>Orden</th>
                    <th>Código</th>
                    <th>Nombre</th>
                    <th>Vista Blade</th>
                    <th>Papel</th>
                    <th>Orientación</th>
                    <th>Verificación</th>
                    <th>Estado</th>
                    <th class="text-end">Acciones</th>
                </tr>
            </thead>
            <tbody>
                @forelse($plantillas as $plantilla)
                    <tr>
                        <td>{{ $plantilla->orden }}</td>
                        <td><code>{{ $plantilla->codigo }}</code></td>
                        <td>
                            <strong>{{ $plantilla->nombre }}</strong>
                            @if($plantilla->descripcion)
                                <div class="text-muted small">{{ $plantilla->descripcion }}</div>
                            @endif
                        </td>
                        <td><code>{{ $plantilla->vista_blade }}</code></td>
                        <td>{{ strtoupper($plantilla->tamanio_papel) }}</td>
                        <td>{{ $plantilla->orientacion === 'portrait' ? 'Vertical' : 'Horizontal' }}</td>
                        <td>
                            @if($plantilla->vista_verificada)
                                <span class="badge badge-success-soft">Verificada</span>
                                @if($plantilla->fecha_verificacion)
                                    <div class="text-muted small">
                                        {{ $plantilla->fecha_verificacion->format('d/m/Y H:i') }}
                                    </div>
                                @endif
                            @else
                                <span class="badge badge-warning-soft">Pendiente</span>
                            @endif
                        </td>
                        <td>
                            @if($plantilla->activa)
                                <span class="badge badge-success-soft">Activa</span>
                            @else
                                <span class="badge badge-muted-soft">Inactiva</span>
                            @endif
                        </td>
                        <td class="text-end">
                            <div class="fepade-actions">
                                <form method="POST" action="{{ route('fac.catalogos.cv-plantillas.verificar-vista', $plantilla) }}">
                                    @csrf
                                    <button class="btn btn-sm btn-outline-secondary" title="Verificar vista">
                                        <i class="fa-solid fa-file-code"></i>
                                    </button>
                                </form>

                                <form method="POST" action="{{ route('fac.catalogos.cv-plantillas.toggle', $plantilla) }}">
                                    @csrf
                                    <button 
                                        class="btn btn-sm btn-outline-primary" 
                                        title="Activar / desactivar"
                                        @disabled(! $plantilla->vista_verificada && ! $plantilla->activa)
                                    >
                                        <i class="fa-solid fa-power-off"></i>
                                    </button>
                                </form>

                                <a href="{{ route('fac.catalogos.cv-plantillas.edit', $plantilla) }}" class="btn btn-sm btn-outline-primary">
                                    <i class="fa-solid fa-pen"></i>
                                </a>

                                <form method="POST" action="{{ route('fac.catalogos.cv-plantillas.destroy', $plantilla) }}" onsubmit="return confirm('¿Eliminar esta plantilla?')">
                                    @csrf
                                    @method('DELETE')
                                    <button class="btn btn-sm btn-outline-danger">
                                        <i class="fa-solid fa-trash"></i>
                                    </button>
                                </form>
                            </div>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="8" class="text-center text-muted py-4">
                            No hay plantillas registradas.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($plantillas->hasPages())
        <div class="pagination-wrapper">
            {{ $plantillas->links('pagination::bootstrap-5') }}
        </div>
    @endif
</div>

@endsection