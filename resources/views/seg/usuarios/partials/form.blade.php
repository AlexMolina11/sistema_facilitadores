<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nombres</label>
        <input type="text"
               name="nombres"
               value="{{ old('nombres', $usuario->nombres ?? '') }}"
               class="form-control @error('nombres') is-invalid @enderror"
               autocomplete="given-name"
               required>

        @error('nombres')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Apellidos</label>
        <input type="text"
               name="apellidos"
               value="{{ old('apellidos', $usuario->apellidos ?? '') }}"
               class="form-control @error('apellidos') is-invalid @enderror"
               autocomplete="family-name"
               required>

        @error('apellidos')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Correo electrónico</label>
        <input type="email"
               name="email"
               value="{{ old('email', $usuario->email ?? '') }}"
               class="form-control @error('email') is-invalid @enderror"
               autocomplete="email"
               required>

        @error('email')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">
            Contraseña
            @if($usuario)
                <span class="text-muted small">(dejar vacío para no cambiar)</span>
            @endif
        </label>

        <input type="password"
               name="password"
               value=""
               class="form-control js-clear-password @error('password') is-invalid @enderror"
               autocomplete="new-password"
               autocapitalize="off"
               spellcheck="false"
               data-lpignore="true"
               data-1p-ignore="true"
               @if(!$usuario) required @endif>

        <div class="form-text">
            Mínimo 8 caracteres, mayúsculas, minúsculas, número y símbolo.
        </div>

        @error('password')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Confirmar contraseña</label>
        <input type="password"
               name="password_confirmation"
               value=""
               class="form-control js-clear-password @error('password_confirmation') is-invalid @enderror"
               autocomplete="new-password"
               autocapitalize="off"
               spellcheck="false"
               data-lpignore="true"
               data-1p-ignore="true"
               @if(!$usuario) required @endif>

        @error('password_confirmation')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Consultor asociado</label>
        <select name="id_consultor"
                class="form-select @error('id_consultor') is-invalid @enderror">
            <option value="">Sin consultor asociado</option>

            @foreach($consultores as $consultor)
                <option value="{{ $consultor->id_consultor }}"
                    @selected(old('id_consultor', $usuario->id_consultor ?? '') == $consultor->id_consultor)>
                    {{ $consultor->nombres }} {{ $consultor->apellidos }}
                </option>
            @endforeach
        </select>

        @error('id_consultor')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Estado</label>
        <select name="activo" class="form-select">
            <option value="1" @selected(old('activo', $usuario->activo ?? 1) == 1)>Activo</option>
            <option value="0" @selected(old('activo', $usuario->activo ?? 1) == 0)>Inactivo</option>
        </select>
    </div>

    <div class="col-12">
        <label class="form-label">Roles</label>

        <div class="row g-2">
            @foreach($roles as $rol)
                <div class="col-md-4">
                    <label class="border rounded p-3 d-flex gap-2 align-items-center">
                        <input type="checkbox"
                               name="roles[]"
                               value="{{ $rol->id_rol }}"
                               @checked(in_array($rol->id_rol, old('roles', $rolesSeleccionados ?? [])))>

                        <span>{{ $rol->nombre }}</span>
                    </label>
                </div>
            @endforeach
        </div>

        @error('roles')
            <div class="text-danger small mt-1">{{ $message }}</div>
        @enderror
    </div>
</div>

@if($usuario)
    @push('scripts')
        <script>
            document.addEventListener('DOMContentLoaded', function () {
                document.querySelectorAll('.js-clear-password').forEach(function (input) {
                    input.value = '';
                    setTimeout(function () {
                        input.value = '';
                    }, 300);
                });
            });
        </script>
    @endpush
@endif
