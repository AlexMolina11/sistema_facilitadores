@extends('layouts.app')

@section('title', 'Bitácora | Facilitadores FEPADE')
@section('page-title', 'Bitácora')
@section('page-subtitle', 'Registro de eventos críticos del sistema')

@section('content')

<x-ui.page-header
    title="Bitácora"
    subtitle="Consulta de accesos, usuarios, roles, permisos y acciones críticas del sistema."
/>

<x-ui.page-card class="mb-4">
    <form method="GET" action="{{ route('seg.bitacora.index') }}" class="row g-3 align-items-end">
        <div class="col-lg-3 col-md-6">
            <label class="form-label">Búsqueda general</label>
            <input
                type="text"
                name="q"
                value="{{ request('q') }}"
                class="form-control"
                placeholder="Usuario, evento, IP o navegador"
            >
        </div>

        <div class="col-lg-2 col-md-6">
            <label class="form-label">Tipo</label>
            <select name="tipo" class="form-select">
                <option value="">Todos los tipos</option>
                @foreach($tipos as $tipo)
                    <option value="{{ $tipo }}" @selected(request('tipo') === $tipo)>
                        {{ $tipo }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-3 col-md-6">
            <label class="form-label">Evento</label>
            <select name="evento" class="form-select">
                <option value="">Todos los eventos</option>
                @foreach($eventos as $evento)
                    <option value="{{ $evento }}" @selected(request('evento') === $evento)>
                        {{ $evento }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-2 col-md-6">
            <label class="form-label">Usuario</label>
            <select name="id_usuario" class="form-select">
                <option value="">Todos</option>
                @foreach($usuarios as $usuario)
                    @php
                        $nombreUsuario = trim(($usuario->nombres ?? '') . ' ' . ($usuario->apellidos ?? '')) ?: $usuario->email;
                    @endphp

                    <option
                        value="{{ $usuario->id_usuario }}"
                        @selected((string) request('id_usuario') === (string) $usuario->id_usuario)
                    >
                        {{ $nombreUsuario }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-lg-2 col-md-6">
            <label class="form-label">IP</label>
            <input
                type="text"
                name="ip"
                value="{{ request('ip') }}"
                class="form-control"
                placeholder="127.0.0.1"
            >
        </div>

        <div class="col-lg-2 col-md-6">
            <label class="form-label">Desde</label>
            <input
                type="date"
                name="desde"
                value="{{ request('desde') }}"
                class="form-control"
            >
        </div>

        <div class="col-lg-2 col-md-6">
            <label class="form-label">Hasta</label>
            <input
                type="date"
                name="hasta"
                value="{{ request('hasta') }}"
                class="form-control"
            >
        </div>

        <div class="col-lg-3 col-md-6 fepade-filter-actions">
            <button class="btn btn-navy w-100">
                <i class="fa-solid fa-filter me-1"></i>
                Filtrar
            </button>

            <a href="{{ route('seg.bitacora.index') }}" class="btn btn-outline-secondary w-100">
                Limpiar
            </a>
        </div>
    </form>
</x-ui.page-card>

<x-ui.table-card
    title="Eventos registrados"
    subtitle="{{ $bitacoras->total() }} registro(s) encontrado(s)"
    :items="$bitacoras"
    empty-title="No hay registros de bitácora."
    empty-message="Todavía no se han registrado eventos con los criterios seleccionados."
>
    <table class="table table-hover align-middle">
        <thead>
            <tr>
                <th>Fecha y hora</th>
                <th>Usuario</th>
                <th>Tipo</th>
                <th>Evento</th>
                <th>IP</th>
                <th>Navegador</th>
            </tr>
        </thead>

        <tbody>
            @foreach($bitacoras as $bitacora)
                @php
                    $tipoEvento = str_contains($bitacora->evento, ':')
                        ? trim(Str::before($bitacora->evento, ':'))
                        : 'General';
                @endphp

                <tr>
                    <td class="text-nowrap">
                        <div class="fw-semibold">
                            {{ optional($bitacora->fecha_evento)->format('d/m/Y') }}
                        </div>
                        <div class="text-muted small">
                            {{ optional($bitacora->fecha_evento)->format('h:i A') }}
                        </div>
                    </td>

                    <td>
                        @if($bitacora->usuario)
                            <div class="fw-semibold">
                                {{ trim(($bitacora->usuario->nombres ?? '') . ' ' . ($bitacora->usuario->apellidos ?? '')) ?: 'Usuario registrado' }}
                            </div>
                            <div class="text-muted small">
                                {{ $bitacora->usuario->email }}
                            </div>
                        @else
                            <span class="badge badge-muted-soft">
                                Sin usuario
                            </span>
                        @endif
                    </td>

                    <td>
                        @if($tipoEvento === 'Seguridad')
                            <span class="badge badge-primary-soft">{{ $tipoEvento }}</span>
                        @elseif($tipoEvento === 'Usuarios')
                            <span class="badge badge-module-soft">{{ $tipoEvento }}</span>
                        @elseif($tipoEvento === 'Roles')
                            <span class="badge badge-success-soft">{{ $tipoEvento }}</span>
                        @elseif($tipoEvento === 'Permisos')
                            <span class="badge badge-warning-soft">{{ $tipoEvento }}</span>
                        @else
                            <span class="badge badge-muted-soft">{{ $tipoEvento }}</span>
                        @endif
                    </td>

                    <td>
                        <div class="fw-semibold">
                            {{ $bitacora->evento }}
                        </div>
                    </td>

                    <td>
                        <code class="fepade-code">
                            {{ $bitacora->ip ?? 'No registrada' }}
                        </code>
                    </td>

                    <td class="small text-muted">
                        {{ Str::limit($bitacora->user_agent, 90) }}
                    </td>
                </tr>
            @endforeach
        </tbody>
    </table>
</x-ui.table-card>

@endsection