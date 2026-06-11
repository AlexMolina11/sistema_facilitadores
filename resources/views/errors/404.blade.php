@extends('layouts.app')

@section('content')

<div class="text-center py-5">

    <h1 class="display-1 fw-bold">404</h1>

    <h3>Página no encontrada</h3>

    <p class="text-muted">
        El recurso solicitado no existe o fue movido.
    </p>

    <a href="{{ url('/') }}" class="btn btn-primary">
        Ir al inicio
    </a>

</div>

@endsection