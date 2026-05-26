@extends('layouts.app')

@section('title', 'Contacto consultor | Facilitadores FEPADE')
@section('page-title', 'Editar perfil')
@section('page-subtitle', 'Completa y actualiza tu información para que las organizaciones puedan encontrarte.')

@section('content')
@include('fac.consultores.partials._wizard', ['step' => 2, 'consultor' => $consultor])
@include('fac.consultores.partials._perfil_cards_styles')

<div class="perfil-panel mb-4">
    <div class="perfil-section-header">
        <div>
            <h4>Correos electrónicos</h4>
            <p class="text-muted mb-0">Registra los correos de contacto y marca uno como principal.</p>
        </div>
        <button class="btn btn-fepade" type="button" onclick="mostrarFormularioPerfil('form-email-nuevo')">+ Añadir correo</button>
    </div>

    <div id="form-email-nuevo" class="perfil-form-wrapper d-none">
        <form method="POST" action="{{ route('fac.consultores.contacto.emails.store', $consultor) }}" class="border rounded p-3 bg-light">
            @csrf
            <div class="row g-3 align-items-end">
                <div class="col-md-7">
                    <label class="form-label">Correo electrónico</label>
                    <input type="email" name="email" class="form-control" maxlength="150" placeholder="nombre@dominio.com" required>
                </div>
                <div class="col-md-3">
                    <div class="form-check form-switch">
                        <input class="form-check-input" type="checkbox" name="principal" value="1" id="email_principal_nuevo">
                        <label class="form-check-label" for="email_principal_nuevo">Principal</label>
                    </div>
                </div>
                <div class="col-md-2 d-flex gap-2">
                    <button type="button" class="btn btn-outline-secondary" onclick="cerrarFormulariosPerfil()">Cancelar</button>
                    <button type="submit" class="btn btn-fepade">Guardar</button>
                </div>
            </div>
        </form>
    </div>

    @forelse($consultor->emails as $email)
        <article class="perfil-card-item">
            <div class="perfil-card-icon">✉</div>
            <div class="perfil-card-body">
                <div class="d-flex justify-content-between gap-3">
                    <div>
                        <div class="perfil-card-title">{{ $email->email }}</div>
                        <div class="perfil-card-meta">{{ $email->principal ? 'Correo principal' : 'Correo secundario' }}</div>
                    </div>
                    <div class="d-flex gap-2">
                        <button class="btn btn-sm btn-link text-secondary" type="button" onclick="mostrarFormularioPerfil('form-email-{{ $email->id_email }}')">✎</button>
                        <form method="POST" action="{{ route('fac.consultores.contacto.emails.destroy', [$consultor, $email]) }}" onsubmit="return confirm('¿Deseas eliminar este correo?')">
                            @csrf @method('DELETE')
                            <button class="btn btn-sm btn-link text-danger" type="submit">🗑</button>
                        </form>
                    </div>
                </div>
                <div id="form-email-{{ $email->id_email }}" class="perfil-form-wrapper d-none mt-3">
                    <form method="POST" action="{{ route('fac.consultores.contacto.emails.update', [$consultor, $email]) }}" class="border rounded p-3 bg-light">
                        @csrf @method('PUT')
                        <div class="row g-3 align-items-end">
                            <div class="col-md-7"><label class="form-label">Correo electrónico</label><input type="email" name="email" value="{{ old('email', $email->email) }}" class="form-control" required></div>
                            <div class="col-md-3"><div class="form-check form-switch"><input class="form-check-input" type="checkbox" name="principal" value="1" {{ $email->principal ? 'checked' : '' }}><label class="form-check-label">Principal</label></div></div>
                            <div class="col-md-2 d-flex gap-2"><button type="button" class="btn btn-outline-secondary" onclick="cerrarFormulariosPerfil()">Cancelar</button><button type="submit" class="btn btn-fepade">Actualizar</button></div>
                        </div>
                    </form>
                </div>
            </div>
        </article>
    @empty
        <div class="text-muted border rounded p-4">No hay correos registrados.</div>
    @endforelse
</div>

<div class="perfil-panel mb-4">
    <div class="perfil-section-header">
        <div><h4>Teléfonos</h4><p class="text-muted mb-0">Registra teléfonos personales, institucionales o de contacto.</p></div>
        <button class="btn btn-fepade" type="button" onclick="mostrarFormularioPerfil('form-telefono-nuevo')">+ Añadir teléfono</button>
    </div>
    <div id="form-telefono-nuevo" class="perfil-form-wrapper d-none">
        <form method="POST" action="{{ route('fac.consultores.contacto.telefonos.store', $consultor) }}" class="border rounded p-3 bg-light">
            @csrf
            <div class="row g-3 align-items-end">
                <div class="col-md-4"><label class="form-label">Tipo</label><select name="id_tipo_telefono" class="form-select" required><option value="">Seleccione</option>@foreach($catalogos['tiposTelefono'] as $tipo)<option value="{{ $tipo->id_tipo_telefono }}">{{ $tipo->nombre }}</option>@endforeach</select></div>
                <div class="col-md-4"><label class="form-label">Número</label><input type="text" name="numero_telefono" class="form-control" required placeholder="7777-7777"></div>
                <div class="col-md-2"><label class="form-label">Extensión</label><input type="text" name="extension" class="form-control" maxlength="10"></div>
                <div class="col-md-2 d-flex gap-2"><button type="button" class="btn btn-outline-secondary" onclick="cerrarFormulariosPerfil()">Cancelar</button><button type="submit" class="btn btn-fepade">Guardar</button></div>
            </div>
        </form>
    </div>

    @forelse($consultor->telefonos as $telefono)
        @php $tipoTelefono = $catalogos['tiposTelefono']->firstWhere('id_tipo_telefono', $telefono->id_tipo_telefono); @endphp
        <article class="perfil-card-item">
            <div class="perfil-card-icon">☎</div>
            <div class="perfil-card-body">
                <div class="d-flex justify-content-between gap-3">
                    <div><div class="perfil-card-title">{{ $telefono->numero_telefono }}</div><div class="perfil-card-meta">{{ $tipoTelefono->nombre ?? 'Tipo no registrado' }} @if($telefono->extension) · Ext. {{ $telefono->extension }} @endif</div></div>
                    <div class="d-flex gap-2"><button class="btn btn-sm btn-link text-secondary" type="button" onclick="mostrarFormularioPerfil('form-telefono-{{ $telefono->id_consultor_telefono }}')">✎</button><form method="POST" action="{{ route('fac.consultores.contacto.telefonos.destroy', [$consultor, $telefono]) }}" onsubmit="return confirm('¿Deseas eliminar este teléfono?')">@csrf @method('DELETE')<button class="btn btn-sm btn-link text-danger" type="submit">🗑</button></form></div>
                </div>
                <div id="form-telefono-{{ $telefono->id_consultor_telefono }}" class="perfil-form-wrapper d-none mt-3">
                    <form method="POST" action="{{ route('fac.consultores.contacto.telefonos.update', [$consultor, $telefono]) }}" class="border rounded p-3 bg-light">
                        @csrf @method('PUT')
                        <div class="row g-3 align-items-end">
                            <div class="col-md-4"><label class="form-label">Tipo</label><select name="id_tipo_telefono" class="form-select" required><option value="">Seleccione</option>@foreach($catalogos['tiposTelefono'] as $tipo)<option value="{{ $tipo->id_tipo_telefono }}" {{ $telefono->id_tipo_telefono == $tipo->id_tipo_telefono ? 'selected' : '' }}>{{ $tipo->nombre }}</option>@endforeach</select></div>
                            <div class="col-md-4"><label class="form-label">Número</label><input type="text" name="numero_telefono" value="{{ $telefono->numero_telefono }}" class="form-control" required></div>
                            <div class="col-md-2"><label class="form-label">Extensión</label><input type="text" name="extension" value="{{ $telefono->extension }}" class="form-control" maxlength="10"></div>
                            <div class="col-md-2 d-flex gap-2"><button type="button" class="btn btn-outline-secondary" onclick="cerrarFormulariosPerfil()">Cancelar</button><button type="submit" class="btn btn-fepade">Actualizar</button></div>
                        </div>
                    </form>
                </div>
            </div>
        </article>
    @empty
        <div class="text-muted border rounded p-4">No hay teléfonos registrados.</div>
    @endforelse
</div>

<div class="perfil-panel mb-4">
    <div class="perfil-section-header">
        <div><h4>Redes sociales / enlaces</h4><p class="text-muted mb-0">Agrega LinkedIn, portafolios, sitios web u otros enlaces profesionales.</p></div>
        <button class="btn btn-fepade" type="button" onclick="mostrarFormularioPerfil('form-red-nueva')">+ Añadir enlace</button>
    </div>
    <div id="form-red-nueva" class="perfil-form-wrapper d-none">
        <form method="POST" action="{{ route('fac.consultores.contacto.redes.store', $consultor) }}" class="border rounded p-3 bg-light">
            @csrf
            <div class="row g-3 align-items-end"><div class="col-md-4"><label class="form-label">Tipo</label><select name="id_tipo_red_social" class="form-select" required><option value="">Seleccione</option>@foreach($catalogos['tiposRedSocial'] as $tipo)<option value="{{ $tipo->id_tipo_red_social }}">{{ $tipo->nombre }}</option>@endforeach</select></div><div class="col-md-6"><label class="form-label">Enlace</label><input type="url" name="enlace" class="form-control" placeholder="https://..." required></div><div class="col-md-2 d-flex gap-2"><button type="button" class="btn btn-outline-secondary" onclick="cerrarFormulariosPerfil()">Cancelar</button><button type="submit" class="btn btn-fepade">Guardar</button></div></div>
        </form>
    </div>
    @forelse($consultor->redesSociales as $red)
        @php $tipoRed = $catalogos['tiposRedSocial']->firstWhere('id_tipo_red_social', $red->id_tipo_red_social); @endphp
        <article class="perfil-card-item"><div class="perfil-card-icon">🔗</div><div class="perfil-card-body"><div class="d-flex justify-content-between gap-3"><div><div class="perfil-card-title">{{ $tipoRed->nombre ?? 'Enlace' }}</div><a href="{{ $red->enlace }}" target="_blank">{{ $red->enlace }}</a></div><div><form method="POST" action="{{ route('fac.consultores.contacto.redes.destroy', [$consultor, $red]) }}" onsubmit="return confirm('¿Deseas eliminar este enlace?')">@csrf @method('DELETE')<button class="btn btn-sm btn-link text-danger" type="submit">🗑</button></form></div></div></div></article>
    @empty
        <div class="text-muted border rounded p-4">No hay enlaces registrados.</div>
    @endforelse
</div>

<div class="perfil-panel mb-4">
    <div class="perfil-section-header"><div><h4>Contacto de emergencia</h4><p class="text-muted mb-0">Registra contactos para emergencias.</p></div><button class="btn btn-fepade" type="button" onclick="mostrarFormularioPerfil('form-emergencia-nueva')">+ Añadir contacto</button></div>
    <div id="form-emergencia-nueva" class="perfil-form-wrapper d-none">
        <form method="POST" action="{{ route('fac.consultores.contacto.emergencias.store', $consultor) }}" class="border rounded p-3 bg-light">@csrf<div class="row g-3 align-items-end"><div class="col-md-4"><label class="form-label">Nombre</label><input type="text" name="nombre" class="form-control" required></div><div class="col-md-3"><label class="form-label">Teléfono</label><input type="text" name="telefono" class="form-control" required></div><div class="col-md-3"><label class="form-label">Correo</label><input type="email" name="correo" class="form-control"></div><div class="col-md-2 d-flex gap-2"><button type="button" class="btn btn-outline-secondary" onclick="cerrarFormulariosPerfil()">Cancelar</button><button type="submit" class="btn btn-fepade">Guardar</button></div></div></form>
    </div>
    @forelse($consultor->emergencias as $emergencia)
        <article class="perfil-card-item"><div class="perfil-card-icon">☂</div><div class="perfil-card-body"><div class="d-flex justify-content-between"><div><div class="perfil-card-title">{{ $emergencia->nombre }}</div><div class="perfil-card-meta">{{ $emergencia->telefono }} @if($emergencia->correo) · {{ $emergencia->correo }} @endif</div></div><form method="POST" action="{{ route('fac.consultores.contacto.emergencias.destroy', [$consultor, $emergencia]) }}" onsubmit="return confirm('¿Deseas eliminar este contacto?')">@csrf @method('DELETE')<button class="btn btn-sm btn-link text-danger" type="submit">🗑</button></form></div></div></article>
    @empty
        <div class="text-muted border rounded p-4">No hay contactos de emergencia registrados.</div>
    @endforelse
</div>

<form method="POST" action="{{ route('fac.consultores.contacto.continuar', $consultor) }}" class="mb-5">
    @csrf
    <div class="d-flex justify-content-between">
        <a href="{{ route('fac.consultores.edit', $consultor) }}" class="btn btn-outline-secondary">Anterior: Perfil Personal</a>
        <button type="submit" class="btn btn-fepade">Guardar y continuar</button>
    </div>
</form>
@endsection

@push('scripts')
<script>
    function mostrarFormularioPerfil(id) {
        document.querySelectorAll('.perfil-form-wrapper').forEach(el => el.classList.add('d-none'));
        const target = document.getElementById(id);
        if (target) target.classList.remove('d-none');
    }

    function cerrarFormulariosPerfil() {
        document.querySelectorAll('.perfil-form-wrapper').forEach(el => el.classList.add('d-none'));
    }
</script>
@endpush
