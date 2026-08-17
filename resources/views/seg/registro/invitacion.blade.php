<!DOCTYPE html>
<html lang="es">

<head>

    <meta charset="UTF-8">

    <meta
        name="viewport"
        content="width=device-width, initial-scale=1"
    >

    <title>
        Crear acceso | Facilitadores FEPADE
    </title>

    <link
        rel="stylesheet"
        href="{{ asset('css/fepade.css') }}"
    >

    <link
        rel="stylesheet"
        href="{{ asset('css/fepade-ux-unificado.css') }}"
    >

    <link
        rel="stylesheet"
        href="https://cdnjs.cloudflare.com/ajax/libs/font-awesome/6.5.2/css/all.min.css"
    >

</head>


<body>

<div class="registro-invitacion-page">

    <div class="registro-invitacion-card">

        {{-- HEADER --}}
        <div class="registro-invitacion-header">

            <div class="registro-invitacion-logo">

                <img
                    src="{{ asset('img/logo-fepade-horizontal-negro.png') }}"
                    alt="FEPADE"
                >

            </div>

        </div>


        <div class="registro-invitacion-accent"></div>


        {{-- BODY --}}
        <div class="registro-invitacion-body">

            <div class="registro-invitacion-kicker">
                Sistema de Facilitadores
            </div>

            <h1>
                Crea tus credenciales de acceso
            </h1>

            <p class="registro-invitacion-description">
                Verifica tus datos y establece la contraseña
                que utilizarás para ingresar al sistema.
            </p>


            @if($errors->any())

                <div class="alert alert-danger">

                    <strong>
                        No fue posible completar el registro.
                    </strong>

                    <ul class="mb-0 mt-2">

                        @foreach($errors->all() as $error)
                            <li>
                                {{ $error }}
                            </li>
                        @endforeach

                    </ul>

                </div>

            @endif


            {{-- DATOS PRECARGADOS --}}
            <div class="registro-invitacion-section">

                <div class="registro-invitacion-section-title">

                    <span class="registro-invitacion-section-icon">
                        <i class="fa-solid fa-user"></i>
                    </span>

                    <div>
                        <h2>
                            Tus datos
                        </h2>

                        <p>
                            Esta información proviene de tu perfil
                            registrado en FEPADE.
                        </p>
                    </div>

                </div>


                <div class="registro-invitacion-grid">

                    <div>
                        <label class="form-label">
                            Nombres
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ $consultor->nombres }}"
                            readonly
                        >
                    </div>


                    <div>
                        <label class="form-label">
                            Apellidos
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ $consultor->apellidos }}"
                            readonly
                        >
                    </div>


                    <div>
                        <label class="form-label">
                            Tipo de documento
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ $consultor->tipo_identificacion ?? $documento?->tipoDocumento?->nombre ?? 'No registrado' }}"
                            readonly
                        >
                    </div>


                    <div>
                        <label class="form-label">
                            Número de documento
                        </label>

                        <input
                            type="text"
                            class="form-control"
                            value="{{ $consultor->numero_identificacion ?? $documento?->numero ?? 'No registrado' }}"
                            readonly
                        >
                    </div>


                    <div class="registro-invitacion-wide">
                        <label class="form-label">
                            Correo electrónico
                        </label>

                        <input
                            type="email"
                            class="form-control"
                            value="{{ $correoPrincipal }}"
                            readonly
                        >
                    </div>

                </div>

            </div>


            {{-- FORMULARIO --}}
            <form
                method="POST"
                action="{{ route('registro.invitacion.store', $invitacion->token) }}"
            >

                @csrf


                <div class="registro-invitacion-section">

                    <div class="registro-invitacion-section-title">

                        <span class="registro-invitacion-section-icon">
                            <i class="fa-solid fa-lock"></i>
                        </span>

                        <div>
                            <h2>
                                Credenciales de acceso
                            </h2>

                            <p>
                                Define una contraseña segura para tu cuenta.
                            </p>
                        </div>

                    </div>


                    <div class="registro-invitacion-grid">

                        <div>
                            <label
                                for="password"
                                class="form-label"
                            >
                                Contraseña
                            </label>

                            <div class="input-group">

                                <input
                                    type="password"
                                    name="password"
                                    id="password"
                                    class="form-control"
                                    required
                                    autocomplete="new-password"
                                >

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary js-toggle-password"
                                    data-target="password"
                                >
                                    <i class="fa-regular fa-eye"></i>
                                </button>

                            </div>
                        </div>


                        <div>
                            <label
                                for="password_confirmation"
                                class="form-label"
                            >
                                Confirmar contraseña
                            </label>

                            <div class="input-group">

                                <input
                                    type="password"
                                    name="password_confirmation"
                                    id="password_confirmation"
                                    class="form-control"
                                    required
                                    autocomplete="new-password"
                                >

                                <button
                                    type="button"
                                    class="btn btn-outline-secondary js-toggle-password"
                                    data-target="password_confirmation"
                                >
                                    <i class="fa-regular fa-eye"></i>
                                </button>

                            </div>
                        </div>


                        <div class="registro-invitacion-wide">

                            <div class="registro-password-help">

                                <strong>
                                    La contraseña debe contener:
                                </strong>

                                <span>
                                    <i class="fa-solid fa-check"></i>
                                    Al menos 8 caracteres
                                </span>

                                <span>
                                    <i class="fa-solid fa-check"></i>
                                    Mayúsculas y minúsculas
                                </span>

                                <span>
                                    <i class="fa-solid fa-check"></i>
                                    Al menos un número
                                </span>

                            </div>

                        </div>

                    </div>

                </div>


                {{-- TÉRMINOS --}}
                <div class="registro-terminos-box">

                    <div>

                        <strong>
                            Términos y políticas de uso
                        </strong>

                        <p>
                            Antes de crear tu cuenta debes leer
                            y aceptar las condiciones de uso del sistema.
                        </p>

                    </div>


                    <button
                        type="button"
                        class="btn btn-outline-primary"
                        data-bs-toggle="modal"
                        data-bs-target="#modalTerminos"
                    >
                        <i class="fa-solid fa-file-lines me-1"></i>
                        Leer términos
                    </button>

                </div>


                <div class="form-check mt-3">

                    <input
                        class="form-check-input"
                        type="checkbox"
                        value="1"
                        id="acepta_terminos"
                        name="acepta_terminos"
                        @checked(old('acepta_terminos'))
                        required
                    >

                    <label
                        class="form-check-label"
                        for="acepta_terminos"
                    >
                        Confirmo que he leído y acepto los
                        términos y políticas de uso.
                    </label>

                </div>


                <div class="registro-invitacion-actions">

                    <button
                        type="submit"
                        class="btn btn-fepade"
                    >
                        <i class="fa-solid fa-user-check me-1"></i>
                        Crear mis credenciales
                    </button>

                </div>

            </form>

        </div>

    </div>

</div>


{{-- MODAL TÉRMINOS --}}
<div
    class="modal fade"
    id="modalTerminos"
    tabindex="-1"
    aria-hidden="true"
>

    <div class="modal-dialog modal-lg modal-dialog-scrollable">

        <div class="modal-content invitacion-modal">

            <div class="modal-header">

                <div>
                    <h5 class="modal-title">
                        Términos y políticas de uso
                    </h5>

                    <small class="text-muted">
                        Sistema de Facilitadores FEPADE
                    </small>
                </div>

                <button
                    type="button"
                    class="btn-close"
                    data-bs-dismiss="modal"
                ></button>

            </div>


            <div class="modal-body">

                <div class="registro-terminos-content">

                    <div class="alert alert-info">

                        Este contenido deberá sustituirse por
                        los términos institucionales oficialmente aprobados
                        por FEPADE antes de la puesta en producción.

                    </div>

                    <h6>
                        Uso de la cuenta
                    </h6>

                    <p>
                        Las credenciales de acceso son personales
                        y no deben compartirse con terceros.
                    </p>

                    <h6>
                        Actualización de información
                    </h6>

                    <p>
                        El usuario es responsable de mantener actualizada
                        la información profesional que puede modificar
                        dentro de su perfil.
                    </p>

                    <h6>
                        Seguridad
                    </h6>

                    <p>
                        El usuario deberá proteger su contraseña
                        y utilizar el sistema únicamente para los fines
                        institucionales establecidos.
                    </p>

                    <h6>
                        Información institucional
                    </h6>

                    <p>
                        Los registros provenientes de sistemas
                        institucionales FEPADE pueden ser de solo lectura
                        y no podrán ser modificados directamente
                        por el consultor.
                    </p>

                </div>

            </div>


            <div class="modal-footer">

                <button
                    type="button"
                    class="btn btn-fepade"
                    data-bs-dismiss="modal"
                >
                    He leído los términos
                </button>

            </div>

        </div>

    </div>

</div>


<script
    src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.3/dist/js/bootstrap.bundle.min.js"
></script>


<script>
document.addEventListener('DOMContentLoaded', function () {

    document
        .querySelectorAll('.js-toggle-password')
        .forEach(function (button) {

            button.addEventListener('click', function () {

                const input = document.getElementById(
                    this.dataset.target
                );

                if (!input) {
                    return;
                }

                const icon = this.querySelector('i');

                if (input.type === 'password') {

                    input.type = 'text';

                    icon?.classList.remove('fa-eye');
                    icon?.classList.add('fa-eye-slash');

                } else {

                    input.type = 'password';

                    icon?.classList.remove('fa-eye-slash');
                    icon?.classList.add('fa-eye');

                }

            });

        });

});
</script>

</body>
</html>