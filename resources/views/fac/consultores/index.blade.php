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
            <p>Registra, edita y gestiona los datos de los consultores.</p>
        </div>
    </div>

    <div class="busqueda-hero-actions">
        <a href="{{ route('fac.consultores.create') }}" class="btn btn-outline-light">
            <i class="fas fa-plus me-1"></i> Nuevo consultor
        </a>
    </div>
</section>

<div class="fepade-card mb-4">
    <form method="GET" action="{{ route('fac.consultores.index') }}" class="row g-3 align-items-end">
        <div class="col-md-6">
            <label class="form-label">Buscar</label>
            <input 
                type="text" 
                name="buscar" 
                value="{{ $buscar }}" 
                class="form-control" 
                placeholder="Nombre, apellido, DUI, NIT"
            >
        </div>

        <div class="col-md-3">
            <label class="form-label">Estado</label>
            <select name="estado" class="form-select">
                <option value="">Todos</option>
                <option value="1" {{ $estado === '1' ? 'selected' : '' }}>Activos</option>
                <option value="0" {{ $estado === '0' ? 'selected' : '' }}>Inactivos</option>
            </select>
        </div>

        <div class="col-md-3 d-flex gap-2">
            <button type="submit" class="btn btn-navy w-100">Buscar</button>
            <a href="{{ route('fac.consultores.index') }}" class="btn btn-outline-secondary w-100">Limpiar</a>
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
                        $porcentajePerfil = $avancePerfil['porcentaje'] ?? 0;

                        $claseAvance = match (true) {
                            $porcentajePerfil >= 85 => 'bg-success',
                            $porcentajePerfil >= 60 => 'bg-warning',
                            default => 'bg-danger',
                        };
                    @endphp
                    <tr>
                        <td>
                            <div class="fw-semibold">{{ $consultor->nombre_completo }}</div>
                            <div class="text-muted small">{{ $consultor->direccion_residencia ?? 'Sin dirección registrada' }}</div>
                        </td>

                        @php
                            $documentoPrincipal =
                                $consultor->documentos
                                    ->first();

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

                        <td>
                            <div>
                                {{ $tipoIdentificacion }}
                            </div>

                            <div class="text-muted small">
                                {{ $numeroIdentificacion ?? 'No registrado' }}
                            </div>
                        </td>

                        <td>{{ $consultor->nacionalidad ?? 'No registrada' }}</td>

                        <td>
                            @if($consultor->activo)
                                <span class="badge badge-success-soft">Activo</span>
                            @else
                                <span class="badge badge-warning-soft">Inactivo</span>
                            @endif
                        </td>

                        <td>
                            <div class="mt-2" style="max-width: 260px;">
                                <div class="d-flex justify-content-between align-items-center small mb-1">
                                    <span class="text-muted">Perfil completado</span>
                                    <strong>{{ $porcentajePerfil }}%</strong>
                                </div>

                                <div class="progress" style="height: 8px;">
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

                        <td class="text-end">
                            @if(!$consultor->usuario)
                                <form
                                    action="{{ route('seg.invitaciones.store', $consultor) }}"
                                    method="POST"
                                    class="d-inline"
                                >
                                    @csrf

                                    <input
                                        type="hidden"
                                        name="duracion_horas"
                                        value="24"
                                    >

                                    <input
                                        type="hidden"
                                        name="max_usos"
                                        value="1"
                                    >

                                    <button
                                        type="submit"
                                        class="btn btn-sm btn-outline-primary"
                                    >
                                        <i class="fa-solid fa-envelope me-1"></i>
                                        Generar invitación
                                    </button>
                                </form>
                            @endif
                            <a href="{{ route('fac.consultores.show', $consultor) }}" class="btn btn-sm btn-outline-primary">
                                Ver
                            </a>

                            <a href="{{ route('fac.consultores.edit', $consultor) }}" class="btn btn-sm btn-outline-secondary">
                                Editar
                            </a>

                            <form 
                                action="{{ route('fac.consultores.destroy', $consultor) }}" 
                                method="POST" 
                                class="d-inline"
                                onsubmit="return confirm('¿Deseas eliminar este consultor?')"
                            >
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-sm btn-outline-danger">
                                    Eliminar
                                </button>
                            </form>
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
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