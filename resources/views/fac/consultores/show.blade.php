@extends('layouts.app')

@section('title', 'Expediente consultor | Facilitadores FEPADE')
@section('page-title', 'Expediente consultor')
@section('page-subtitle', 'Vista resumen del perfil del consultor')

@section('content')

<x-ui.page-header title="Expediente del consultor" subtitle="Información general del perfil registrado.">
    <div class="d-flex gap-2">
        <a href="{{ route('fac.consultores.edit', $consultor) }}" class="btn btn-fepade">
            Editar
        </a>

        <a href="{{ route('fac.consultores.index') }}" class="btn btn-outline-secondary">
            Volver
        </a>
    </div>
</x-ui.page-header>

<div class="row g-4">
    <div class="col-lg-4">
        <div class="fepade-card h-100">
            <div class="text-center">
                <div class="rounded-circle bg-light d-inline-flex align-items-center justify-content-center mb-3" style="width: 110px; height: 110px;">
                    <span class="fw-bold fs-2">
                        {{ strtoupper(substr($consultor->nombres, 0, 1) . substr($consultor->apellidos, 0, 1)) }}
                    </span>
                </div>

                <h4 class="mb-1">{{ $consultor->nombre_completo }}</h4>
                <p class="text-muted mb-3">{{ $consultor->nacionalidad ?? 'Nacionalidad no registrada' }}</p>

                @if($consultor->activo)
                    <span class="badge badge-success-soft">Activo</span>
                @else
                    <span class="badge badge-warning-soft">Inactivo</span>
                @endif
            </div>
        </div>
    </div>

    <div class="col-lg-8">
        <div class="fepade-card">
            <h5 class="mb-4">Datos personales</h5>

            <div class="row g-3">
                <div class="col-md-6">
                    <small class="text-muted">Tipo de identificación</small>
                    <div class="fw-semibold">{{ $consultor->tipo_identificacion ?? 'No registrado' }}</div>
                </div>

                <div class="col-md-6">
                    <small class="text-muted">Número de identificación</small>
                    <div class="fw-semibold">{{ $consultor->numero_identificacion ?? 'No registrado' }}</div>
                </div>

                <div class="col-md-6">
                    <small class="text-muted">NIT</small>
                    <div class="fw-semibold">{{ $consultor->nit ?? 'No registrado' }}</div>
                </div>

                <div class="col-md-6">
                    <small class="text-muted">NRC</small>
                    <div class="fw-semibold">{{ $consultor->nrc ?? 'No registrado' }}</div>
                </div>

                <div class="col-md-6">
                    <small class="text-muted">Sexo</small>
                    <div class="fw-semibold">{{ $consultor->sexo ?? 'No registrado' }}</div>
                </div>

                <div class="col-md-6">
                    <small class="text-muted">Fecha de nacimiento</small>
                    <div class="fw-semibold">
                        {{ $consultor->fecha_nacimiento ? $consultor->fecha_nacimiento->format('d/m/Y') : 'No registrada' }}
                    </div>
                </div>

                <div class="col-12">
                    <small class="text-muted">Dirección de residencia</small>
                    <div class="fw-semibold">{{ $consultor->direccion_residencia ?? 'No registrada' }}</div>
                </div>

                <div class="col-12">
                    <small class="text-muted">Contacto de emergencia</small>
                    <div class="fw-semibold">{{ $consultor->emergencia_contacto ?? 'No registrado' }}</div>
                </div>
            </div>
        </div>
    </div>
</div>

<div class="fepade-card mt-4">
    <h5 class="mb-3">Secciones del expediente</h5>

    <div class="row g-3">
        <div class="col-md-3">
            <div class="border rounded p-3 h-100">
                <strong>Formación académica</strong>
                <p class="text-muted small mb-0">Pendiente de integrar.</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="border rounded p-3 h-100">
                <strong>Experiencia laboral</strong>
                <p class="text-muted small mb-0">Pendiente de integrar.</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="border rounded p-3 h-100">
                <strong>Idiomas y habilidades</strong>
                <p class="text-muted small mb-0">Pendiente de integrar.</p>
            </div>
        </div>

        <div class="col-md-3">
            <div class="border rounded p-3 h-100">
                <strong>Documentos</strong>
                <p class="text-muted small mb-0">Pendiente de integrar.</p>
            </div>
        </div>
    </div>
</div>

@endsection