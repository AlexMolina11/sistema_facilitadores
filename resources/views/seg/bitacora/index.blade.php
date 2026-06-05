@extends('layouts.app')

@section('title', 'Bitácora de accesos | Facilitadores FEPADE')
@section('page-title', 'Bitácora de accesos')
@section('page-subtitle', 'Registro de eventos de inicio y cierre de sesión')

@section('content')

<x-ui.page-header
    title="Bitácora de accesos"
    subtitle="Consulta de inicios de sesión, cierres de sesión e intentos fallidos."
/>

<div class="card mb-4">
    <div class="card-body">
        <form method="GET" class="row g-3">
            <div class="col-md-3">
                <label class="form-label">Evento</label>
                <input type="text" name="evento" value="{{ request('evento') }}" class="form-control">
            </div>

            <div class="col-md-3">
                <label class="form-label">IP</label>
                <input type="text" name="ip" value="{{ request('ip') }}" class="form-control">
            </div>

            <div class="col-md-2">
                <label class="form-label">Desde</label>
                <input type="date" name="desde" value="{{ request('desde') }}" class="form-control">
            </div>

            <div class="col-md-2">
                <label class="form-label">Hasta</label>
                <input type="date" name="hasta" value="{{ request('hasta') }}" class="form-control">
            </div>

            <div class="col-md-2 d-flex align-items-end">
                <button class="btn btn-primary w-100">
                    Filtrar
                </button>
            </div>
        </form>
    </div>
</div>

<div class="card">
    <div class="table-responsive">
        <table class="table table-hover align-middle mb-0">
            <thead>
                <tr>
                    <th>Fecha</th>
                    <th>Usuario</th>
                    <th>Evento</th>
                    <th>IP</th>
                    <th>Navegador</th>
                </tr>
            </thead>

            <tbody>
                @forelse($bitacoras as $bitacora)
                    <tr>
                        <td>{{ optional($bitacora->fecha_evento)->format('d/m/Y H:i') }}</td>
                        <td>
                            {{ $bitacora->usuario?->name ?? 'Sin usuario' }}
                            <br>
                            <small class="text-muted">{{ $bitacora->usuario?->email }}</small>
                        </td>
                        <td>
                            <span class="badge bg-secondary">
                                {{ $bitacora->evento }}
                            </span>
                        </td>
                        <td>{{ $bitacora->ip }}</td>
                        <td class="small text-muted">
                            {{ Str::limit($bitacora->user_agent, 80) }}
                        </td>
                    </tr>
                @empty
                    <tr>
                        <td colspan="5" class="text-center text-muted py-4">
                            No hay registros de bitácora.
                        </td>
                    </tr>
                @endforelse
            </tbody>
        </table>
    </div>

    @if($bitacoras->hasPages())
        <div class="card-footer">
            {{ $bitacoras->links() }}
        </div>
    @endif
</div>

@endsection