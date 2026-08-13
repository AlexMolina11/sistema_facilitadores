@extends('layouts.app')

@section('title', 'Invitación | Facilitadores FEPADE')
@section('page-title', 'Invitación')
@section('page-subtitle', 'Detalle de invitación creada')

@section('content')

<div class="container-fluid">

    <div class="card">
        <div class="card-body">

            <h5 class="mb-4">
                Invitación creada correctamente
            </h5>

            <dl class="row">

                <dt class="col-sm-3">Consultor</dt>
                <dd class="col-sm-9">
                    {{ $invitacion->consultor?->nombre_completo }}
                </dd>

                <dt class="col-sm-3">Rol</dt>
                <dd class="col-sm-9">
                    {{ $invitacion->rol?->nombre }}
                </dd>

                <dt class="col-sm-3">Estado</dt>
                <dd class="col-sm-9">
                    {{ $invitacion->estado() }}
                </dd>

                <dt class="col-sm-3">Duración</dt>
                <dd class="col-sm-9">
                    @if($invitacion->duracion_horas === null)
                        Ilimitada
                    @else
                        {{ $invitacion->duracion_horas }} horas
                    @endif
                </dd>

                <dt class="col-sm-3">Máximo de usos</dt>
                <dd class="col-sm-9">
                    {{ $invitacion->max_usos ?? 'Ilimitados' }}
                </dd>

                <dt class="col-sm-3">Expiración</dt>
                <dd class="col-sm-9">
                    {{ $invitacion->fecha_expiracion?->format('d/m/Y h:i A') ?? 'Sin expiración' }}
                </dd>

                <dt class="col-sm-3">URL</dt>
                <dd class="col-sm-9">
                    <input
                        type="text"
                        class="form-control"
                        readonly
                        value="{{ $invitacion->url_invitacion }}"
                    >
                </dd>

            </dl>

        </div>
    </div>

</div>

@endsection