@extends('layouts.app')

@section('title', 'Dashboard | Facilitadores FEPADE')
@section('page-title', 'Dashboard')
@section('page-subtitle', 'Resumen general del sistema de facilitadores')

@section('content')

<x-ui.page-header 
    title="Panel de control"
    subtitle="Vista general para la administración de consultores, invitaciones y catálogos."
/>

<div class="row g-4 mb-4">
    <div class="col-md-3">
        <x-ui.stat-card label="Consultores registrados" value="0" description="Total de perfiles creados" />
    </div>

    <div class="col-md-3">
        <x-ui.stat-card label="Pendientes de revisión" value="0" description="Perfiles por validar" />
    </div>

    <div class="col-md-3">
        <x-ui.stat-card label="Invitaciones activas" value="0" description="Links disponibles" />
    </div>

    <div class="col-md-3">
        <x-ui.stat-card label="Catálogos" value="17" description="Catálogos configurables" />
    </div>
</div>

<div class="row g-4">
    <div class="col-lg-8">
        <div class="fepade-card">
            <h5 class="fw-bold mb-3">Actividad reciente</h5>

            <div class="table-responsive">
                <table class="table align-middle">
                    <thead>
                        <tr>
                            <th>Fecha</th>
                            <th>Actividad</th>
                            <th>Usuario</th>
                            <th>Estado</th>
                        </tr>
                    </thead>
                    <tbody>
                        <tr>
                            <td>Hoy</td>
                            <td>Configuración base del sistema</td>
                            <td>Administrador</td>
                            <td><span class="badge bg-success">Completado</span></td>
                        </tr>
                        <tr>
                            <td>Hoy</td>
                            <td>Preparación de vistas demo</td>
                            <td>Equipo FEPADE</td>
                            <td><span class="badge bg-warning text-dark">En proceso</span></td>
                        </tr>
                    </tbody>
                </table>
            </div>
        </div>
    </div>

    <div class="col-lg-4">
        <div class="fepade-card">
            <h5 class="fw-bold mb-3">Accesos rápidos</h5>

            <div class="d-grid gap-2">
                <a href="#" class="btn btn-fepade">Nuevo consultor</a>
                <a href="#" class="btn btn-outline-secondary">Crear invitación</a>
                <a href="#" class="btn btn-outline-secondary">Buscar consultores</a>
                <a href="#" class="btn btn-outline-secondary">Revisar perfiles</a>
            </div>
        </div>
    </div>
</div>

@endsection