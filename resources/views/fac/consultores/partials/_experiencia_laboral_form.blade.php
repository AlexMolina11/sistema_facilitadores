@php
    $esEdicion = isset($experiencia) && $experiencia;
    $clave = $esEdicion ? (string) $experiencia->id_experiencia : 'nuevo';
    $usarOld = session()->has('_old_input') && old('_experiencia_form') === $clave;
    $valor = fn ($campo, $predeterminado = '') => $usarOld ? old($campo, $predeterminado) : ($predeterminado ?? '');
    $actual = $usarOld ? (bool) old('trabajo_actual', false) : (bool) ($experiencia->trabajo_actual ?? false);
    $erroresForm = $errors->any() && $usarOld;
@endphp

<form method="POST"
      action="{{ $esEdicion ? route('fac.consultores.experiencia.laboral.update', [$consultor, $experiencia]) : route('fac.consultores.experiencia.laboral.store', $consultor) }}"
      enctype="multipart/form-data" class="js-experiencia-form border rounded-3 p-3 mb-3 bg-white"
      data-form-key="{{ $clave }}" novalidate>
    @csrf
    @if($esEdicion) @method('PUT') @endif
    <input type="hidden" name="_experiencia_form" value="{{ $clave }}">

    @if($erroresForm)
        <div class="alert alert-danger" role="alert">
            <strong>Revisa los datos antes de guardar.</strong>
            <ul class="mb-0 mt-2">
                @foreach($errors->all() as $mensaje)<li>{{ $mensaje }}</li>@endforeach
            </ul>
        </div>
    @endif

    <p class="text-muted mb-3">Completa tres pasos sencillos. Los campos con <span class="text-danger">*</span> son obligatorios.</p>
    <div class="d-flex flex-wrap gap-2 mb-3" aria-label="Progreso del formulario">
        <span class="badge rounded-pill bg-primary js-exp-indicador" data-indicador="1">1. Puesto</span>
        <span class="badge rounded-pill bg-secondary js-exp-indicador" data-indicador="2">2. Fechas</span>
        <span class="badge rounded-pill bg-secondary js-exp-indicador" data-indicador="3">3. Contacto y evidencia</span>
    </div>

    <section class="js-exp-paso" data-paso="1" aria-label="Puesto e institución">
        <h4 class="h6 mb-3">Paso 1 de 3 · ¿Dónde trabajaste?</h4>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="exp_empresa_{{ $clave }}">Empresa o institución <span class="text-danger">*</span></label>
                <input id="exp_empresa_{{ $clave }}" type="text" name="empresa" value="{{ $valor('empresa', $experiencia->empresa ?? '') }}" class="form-control" maxlength="150" required autocomplete="organization" placeholder="Ej. FEPADE">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="exp_cargo_{{ $clave }}">Cargo desempeñado <span class="text-danger">*</span></label>
                <input id="exp_cargo_{{ $clave }}" type="text" name="cargo" value="{{ $valor('cargo', $experiencia->cargo ?? '') }}" class="form-control" maxlength="100" required placeholder="Ej. Analista de sistemas">
            </div>
            <div class="col-12">
                <label class="form-label" for="exp_descripcion_{{ $clave }}">Descripción de funciones o experiencia</label>
                <textarea id="exp_descripcion_{{ $clave }}" name="descripcion" rows="5" class="form-control" maxlength="500" placeholder="Describe tus responsabilidades principales en frases sencillas.">{{ $valor('descripcion', $experiencia->descripcion ?? '') }}</textarea>
                <div class="form-text">Máximo 500 caracteres. Puedes resumir tus principales actividades.</div>
            </div>
        </div>
    </section>

    <section class="js-exp-paso d-none" data-paso="2" aria-label="Período laboral">
        <h4 class="h6 mb-3">Paso 2 de 3 · ¿Cuándo trabajaste?</h4>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="exp_desde_{{ $clave }}">Fecha de inicio</label>
                <input id="exp_desde_{{ $clave }}" type="date" name="desde" value="{{ $valor('desde', $experiencia?->desde?->format('Y-m-d') ?? '') }}" max="{{ now()->toDateString() }}" class="form-control js-exp-desde">
                <div class="form-text">Puedes registrar trabajos anteriores y actuales.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="exp_hasta_{{ $clave }}">Fecha de finalización</label>
                <input id="exp_hasta_{{ $clave }}" type="date" name="hasta" value="{{ $valor('hasta', $experiencia?->hasta?->format('Y-m-d') ?? '') }}" max="{{ now()->toDateString() }}" class="form-control js-fecha-hasta">
            </div>
            <div class="col-12">
                <div class="form-check form-switch">
                    <input class="form-check-input js-trabajo-actual" type="checkbox" name="trabajo_actual" value="1" id="exp_actual_{{ $clave }}" {{ $actual ? 'checked' : '' }}>
                    <label class="form-check-label" for="exp_actual_{{ $clave }}">Actualmente trabajo aquí</label>
                </div>
                <div class="form-text">Si marcas esta opción, no necesitas indicar una fecha final.</div>
            </div>
            <div class="col-12"><div class="alert alert-info mb-0">Puedes registrar dos empleos durante las mismas fechas si realmente los desempeñaste al mismo tiempo.</div></div>
        </div>
    </section>

    <section class="js-exp-paso d-none" data-paso="3" aria-label="Contacto y evidencia">
        <h4 class="h6 mb-3">Paso 3 de 3 · Información complementaria</h4>
        <p class="text-muted">Estos datos son opcionales. Si ingresas un correo o teléfono, verificaremos que tenga un formato válido.</p>
        <div class="row g-3">
            <div class="col-md-6">
                <label class="form-label" for="exp_jefe_{{ $clave }}">Nombre del jefe inmediato</label>
                <input id="exp_jefe_{{ $clave }}" type="text" name="jefe_nombre" value="{{ $valor('jefe_nombre', $experiencia->jefe_nombre ?? '') }}" class="form-control" maxlength="150" autocomplete="off">
            </div>
            <div class="col-md-6">
                <label class="form-label" for="exp_email_{{ $clave }}">Correo del jefe inmediato</label>
                <input id="exp_email_{{ $clave }}" type="email" name="jefe_email" value="{{ $valor('jefe_email', $experiencia->jefe_email ?? '') }}" class="form-control js-exp-email" placeholder="nombre@dominio.com" maxlength="120" autocomplete="off" inputmode="email">
                <div class="form-text">Ejemplo: contacto@institucion.org</div>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="exp_tel_{{ $clave }}">Teléfono del jefe inmediato</label>
                <input id="exp_tel_{{ $clave }}" type="tel" name="jefe_telefono" value="{{ $valor('jefe_telefono', $experiencia->jefe_telefono ?? '') }}" class="form-control js-exp-telefono" maxlength="20" autocomplete="off" inputmode="tel">
                <div class="form-text">Selecciona el país e ingresa el número. Se guardará con el prefijo internacional.</div>
            </div>
            <div class="col-md-6">
                <label class="form-label" for="exp_evidencia_{{ $clave }}">Evidencia laboral</label>
                <input id="exp_evidencia_{{ $clave }}" type="file" name="evidencia" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
                <div class="form-text">PDF, JPG, JPEG, PNG o WEBP. Máximo 5 MB.</div>
                @if($esEdicion && $experiencia->url_evidencia)
                    <a class="d-inline-block mt-2" href="{{ \Illuminate\Support\Facades\Storage::url($experiencia->url_evidencia) }}" target="_blank" rel="noopener noreferrer">Ver evidencia actual (abre otra pestaña)</a>
                @endif
            </div>
        </div>
    </section>

    <div class="alert alert-danger mt-3 d-none js-exp-error" role="alert" aria-live="polite"></div>
    <div class="d-flex flex-wrap justify-content-between gap-2 mt-4">
        <button type="button" class="btn btn-outline-secondary js-exp-atras d-none" style="min-height:44px">← Anterior</button>
        <div class="d-flex gap-2 ms-auto">
            <button type="button" class="btn btn-outline-secondary js-exp-cancelar" style="min-height:44px">Cancelar</button>
            <button type="button" class="btn btn-fepade js-exp-siguiente" style="min-height:44px">Siguiente →</button>
            <button type="submit" class="btn btn-fepade js-exp-guardar d-none" style="min-height:44px">{{ $esEdicion ? 'Actualizar experiencia' : 'Guardar experiencia' }}</button>
        </div>
    </div>
</form>

@once
    @push('styles')
        <link rel="stylesheet" href="{{ asset('vendor/intl-tel-input/css/intlTelInput.min.css') }}">
        <style>.js-experiencia-form .iti { width: 100%; } .js-experiencia-form .form-control { min-height:44px; } .js-experiencia-form .form-check-input { cursor:pointer; }</style>
    @endpush
    @push('scripts')
        <script src="{{ asset('vendor/intl-tel-input/js/intlTelInputWithUtils.min.js') }}"></script>
        <script>
        document.addEventListener('DOMContentLoaded', function () {
            const forms = Array.from(document.querySelectorAll('.js-experiencia-form'));
            const regexCorreo = /^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/;
            forms.forEach(function (form) {
                let paso = 1;
                const actual = form.querySelector('.js-trabajo-actual');
                const desde = form.querySelector('.js-exp-desde');
                const hasta = form.querySelector('.js-fecha-hasta');
                const telefono = form.querySelector('.js-exp-telefono');
                const correo = form.querySelector('.js-exp-email');
                const error = form.querySelector('.js-exp-error');
                const iti = window.intlTelInput ? window.intlTelInput(telefono, {
                    initialCountry: 'sv',
                    separateDialCode: true,
                    nationalMode: true,
                    autoPlaceholder: 'polite'
                }) : null;

                function mostrarError(mensaje, campo) {
                    error.textContent = mensaje;
                    error.classList.remove('d-none');
                    if (campo) { campo.focus(); }
                    return false;
                }
                function limpiarError() { error.textContent = ''; error.classList.add('d-none'); }
                function sincronizarActual() {
                    hasta.disabled = actual.checked;
                    if (actual.checked) hasta.value = '';
                }
                function render() {
                    form.querySelectorAll('.js-exp-paso').forEach(function (panel) {
                        panel.classList.toggle('d-none', Number(panel.dataset.paso) !== paso);
                    });
                    form.querySelectorAll('.js-exp-indicador').forEach(function (badge) {
                        const activo = Number(badge.dataset.indicador) === paso;
                        badge.classList.toggle('bg-primary', activo);
                        badge.classList.toggle('bg-secondary', !activo);
                    });
                    form.querySelector('.js-exp-atras').classList.toggle('d-none', paso === 1);
                    form.querySelector('.js-exp-siguiente').classList.toggle('d-none', paso === 3);
                    form.querySelector('.js-exp-guardar').classList.toggle('d-none', paso !== 3);
                    limpiarError();
                }
                function validarPaso(numero) {
                    if (numero === 1) {
                        for (const nombre of ['empresa','cargo']) {
                            const campo = form.elements.namedItem(nombre);
                            if (!campo.value.trim()) return mostrarError('Completa el campo ' + (nombre === 'empresa' ? 'Empresa o institución.' : 'Cargo desempeñado.'), campo);
                            if (!campo.checkValidity()) return mostrarError('Revisa el campo ' + nombre + '.', campo);
                        }
                    }
                    if (numero === 2) {
                        if (desde.value && desde.value > desde.max) return mostrarError('La fecha de inicio no puede ser futura.', desde);
                        if (!actual.checked && hasta.value && hasta.value > hasta.max) return mostrarError('La fecha final no puede ser futura.', hasta);
                        if (!actual.checked && desde.value && hasta.value && hasta.value < desde.value) return mostrarError('La fecha final no puede ser anterior a la fecha de inicio.', hasta);
                    }
                    if (numero === 3) {
                        if (correo.value.trim() && (!regexCorreo.test(correo.value.trim()) || correo.value.length > 120)) return mostrarError('Escribe un correo válido, por ejemplo nombre@dominio.com.', correo);
                        if (telefono.value.trim()) {
                            if (!iti) return mostrarError('No se pudo cargar el selector telefónico. Actualiza la página e inténtalo nuevamente.', telefono);
                            if (!iti.isValidNumber()) return mostrarError('El teléfono no es válido para el país seleccionado.', telefono);
                        }
                        const archivo = form.querySelector('[name="evidencia"]').files[0];
                        if (archivo && archivo.size > 5 * 1024 * 1024) return mostrarError('La evidencia no puede superar los 5 MB.', form.querySelector('[name="evidencia"]'));
                    }
                    limpiarError(); return true;
                }
                actual.addEventListener('change', sincronizarActual);
                desde.addEventListener('change', function () { hasta.min = desde.value || ''; });
                form.querySelector('.js-exp-siguiente').addEventListener('click', function () { if (validarPaso(paso)) { paso++; render(); } });
                form.querySelector('.js-exp-atras').addEventListener('click', function () { paso--; render(); });
                form.querySelector('.js-exp-cancelar').addEventListener('click', function () { if (typeof cerrarFormulariosPerfil === 'function') cerrarFormulariosPerfil(); });
                form.addEventListener('submit', function (evento) {
                    for (let i = 1; i <= 3; i++) {
                        if (!validarPaso(i)) { evento.preventDefault(); paso = i; render(); validarPaso(i); return; }
                    }
                    const lista = window.experienciasLaboralesComparacion || [];
                    const normalizar = texto => (texto || '').trim().replace(/\s+/g, ' ').toLocaleLowerCase('es');
                    const empresa = normalizar(form.elements.namedItem('empresa').value);
                    const cargo = normalizar(form.elements.namedItem('cargo').value);
                    const desdeValor = desde.value || null;
                    const hastaValor = actual.checked ? null : (hasta.value || null);
                    const idActual = form.dataset.formKey === 'nuevo' ? null : Number(form.dataset.formKey);
                    const coincide = lista.some(item =>
                        item.id !== idActual &&
                        normalizar(item.empresa) === empresa &&
                        normalizar(item.cargo) === cargo &&
                        (item.desde || null) === desdeValor &&
                        Boolean(item.trabajo_actual) === actual.checked &&
                        (actual.checked || (item.hasta || null) === hastaValor)
                    );
                    if (coincide && !window.confirm('Ya existe una experiencia con la misma empresa, cargo y período. ¿Deseas guardarla de todos modos? Si son cargos reales simultáneos, puedes continuar.')) {
                        evento.preventDefault();
                        return;
                    }
                    if (telefono.value.trim() && iti) telefono.value = iti.getNumber();
                });
                sincronizarActual();
                hasta.min = desde.value || '';
                if (form.querySelector('.alert-danger:not(.js-exp-error)')) {
                    const campoError = @json($usarOld ? $errors->keys()[0] ?? '' : '');
                    paso = ['desde', 'hasta', 'trabajo_actual'].includes(campoError) ? 2 :
                        ['jefe_nombre', 'jefe_email', 'jefe_telefono', 'evidencia'].includes(campoError) ? 3 : 1;
                }
                render();
            });
            const viejo = document.querySelector('.js-experiencia-form .alert-danger:not(.js-exp-error)');
            if (viejo) {
                const form = viejo.closest('.js-experiencia-form');
                const editor = form.closest('.perfil-form-wrapper');
                if (editor && typeof mostrarFormularioPerfil === 'function') mostrarFormularioPerfil(editor.id);
            }
        });
        </script>
    @endpush
@endonce
