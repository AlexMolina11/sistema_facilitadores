@extends('layouts.app')

@php
    $esMiPerfil =
        filled(auth()->user()?->id_consultor)
        && (int) auth()->user()->id_consultor
            === (int) $consultor->id_consultor;
@endphp

@section(
    'title',
    $esMiPerfil
        ? 'Mis idiomas | Facilitadores FEPADE'
        : 'Idiomas | Facilitadores FEPADE'
)

@section(
    'page-title',
    $esMiPerfil
        ? 'Mis idiomas'
        : 'Idiomas del consultor'
)

@section(
    'page-subtitle',
    $esMiPerfil
        ? 'Registra los idiomas que dominas y su nivel correspondiente.'
        : 'Administra los idiomas y niveles registrados para el consultor.'
)

@section('content')

<!--<x-ui.page-header
    :title="$esMiPerfil
        ? 'Mis idiomas'
        : 'Idiomas'"
    :subtitle="$esMiPerfil
        ? 'Mantén actualizados tus idiomas, niveles de dominio y certificados.'
        : 'Mantén actualizados los idiomas, niveles de dominio y certificados del consultor.'"
/>-->

@include('fac.consultores.partials._fac_input_ux')
@include('fac.consultores.partials._wizard', ['step' => 6, 'consultor' => $consultor])

@php
    $idiomaFormError = old('_idioma_form_id');
@endphp
<div class="perfil-panel mb-4 idioma-panel">
    <div class="perfil-section-header">
        <div><h4>Idiomas</h4><p class="text-muted mb-0">Registra los idiomas que dominas, tu nivel y los certificados de respaldo.</p></div>
        <button type="button" class="btn btn-fepade" onclick="mostrarFormularioIdioma('crear-idioma')"><i class="fa-solid fa-plus me-1"></i> Añadir idioma</button>
    </div>
    <div class="row g-3 mb-4">
        <div class="col-md-6"><div class="idioma-stat"><strong>{{ $consultor->idiomas->count() }}</strong><span>Idiomas registrados</span></div></div>
        <div class="col-md-6"><div class="idioma-stat"><strong>{{ $consultor->idiomas->filter(fn($i) => filled($i->url_certificado))->count() }}</strong><span>Con certificado</span></div></div>
    </div>
    <div id="crear-idioma" class="perfil-form-wrapper {{ $idiomaFormError === 'crear' ? '' : 'd-none' }}">
        @include('fac.consultores.partials._idioma_form', ['consultor' => $consultor, 'catalogos' => $catalogos, 'idiomaConsultor' => null, 'idiomaFormError' => $idiomaFormError])
    </div>
    @forelse($consultor->idiomas as $idiomaConsultor)
        @php
            $idioma = $catalogos['idiomas']->firstWhere('id_idioma', $idiomaConsultor->id_idioma);
            $nivel = $catalogos['nivelesIdioma']->firstWhere('id_idioma_nivel', $idiomaConsultor->id_idioma_nivel);
            $idFormulario = 'editar-idioma-'.$idiomaConsultor->id_consultor_idioma;
        @endphp
        <article class="idioma-card">
            <div class="idioma-card-icon"><i class="fa-solid fa-language" aria-hidden="true"></i></div>
            <div class="idioma-card-content">
                <div class="d-flex flex-wrap justify-content-between align-items-start gap-3">
                    <div><h5 class="mb-1">{{ $idioma->nombre ?? 'Idioma' }}</h5><span class="idioma-level"><i class="fa-solid fa-chart-simple me-1"></i>{{ $nivel->nombre ?? 'Nivel no registrado' }}</span></div>
                    <div class="d-flex flex-wrap gap-2">
                        <button type="button" class="btn btn-sm btn-outline-secondary" onclick="mostrarFormularioIdioma('{{ $idFormulario }}')"><i class="fa-solid fa-pen me-1"></i> Editar</button>
                        <form method="POST" action="{{ route('fac.consultores.idiomas.destroy', [$consultor, $idiomaConsultor]) }}" onsubmit="return confirm('¿Deseas eliminar este idioma?')">@csrf @method('DELETE')<button type="submit" class="btn btn-sm btn-outline-danger"><i class="fa-solid fa-trash me-1"></i> Eliminar</button></form>
                    </div>
                </div>
                <div class="mt-3">
                    @if($idiomaConsultor->url_certificado)
                        <a href="{{ Storage::url($idiomaConsultor->url_certificado) }}" target="_blank" rel="noopener noreferrer" class="idioma-certificate"><i class="fa-regular fa-file-lines me-2"></i>Ver certificado <i class="fa-solid fa-arrow-up-right-from-square ms-1"></i></a>
                    @else
                        <span class="text-muted small"><i class="fa-regular fa-file me-1"></i> Sin certificado adjunto</span>
                    @endif
                </div>
                <div id="{{ $idFormulario }}" class="perfil-form-wrapper mt-3 {{ $idiomaFormError === $idFormulario ? '' : 'd-none' }}">
                    @include('fac.consultores.partials._idioma_form', ['consultor' => $consultor, 'catalogos' => $catalogos, 'idiomaConsultor' => $idiomaConsultor, 'idiomaFormError' => $idiomaFormError])
                </div>
            </div>
        </article>
    @empty
        <div class="idioma-empty"><i class="fa-solid fa-language fa-2x mb-2"></i><p class="mb-1 fw-semibold">Aún no hay idiomas registrados</p><p class="mb-0 small">Selecciona «Añadir idioma» para registrar el primero.</p></div>
    @endforelse
</div>
@include(
    'fac.consultores.partials._wizard_actions',
    [
        'consultor' =>
            $consultor,

        'anterior' =>
            route(
                'fac.consultores.habilidades.edit',
                $consultor
            ),

        'continuarRoute' =>
            route(
                'fac.consultores.idiomas.continuar',
                $consultor
            ),

        'continuarLabel' =>
            'Continuar a Referencias',
    ]
)
@endsection
@push('styles')
<style>
.idioma-panel .perfil-section-header h4 {color:#102237;font-weight:700}
.idioma-stat {background:#f7fafc;border:1px solid #dbe3ea;border-radius:12px;padding:16px 18px;display:flex;flex-direction:column;gap:4px}
.idioma-stat strong {font-size:25px;color:#225272;line-height:1.1}.idioma-stat span {color:#64748b;font-size:13px}
.idioma-card {display:flex;gap:16px;background:white;border:1px solid #dce3eb;border-radius:14px;padding:18px;margin-bottom:14px}
.idioma-card-icon {width:42px;height:42px;flex:0 0 42px;border-radius:50%;background:#e1f7f2;color:#009f83;display:grid;place-items:center;font-size:19px}
.idioma-card-content {min-width:0;flex:1}.idioma-card h5 {font-size:17px;font-weight:700;color:#102237}.idioma-level {font-size:13px;color:#53657a}
.idioma-certificate {display:inline-flex;align-items:center;font-size:13px;font-weight:600}
.idioma-empty {text-align:center;border:1px dashed #cbd5e1;border-radius:12px;padding:30px;color:#64748b}
.idioma-guide {background:#fff;border:1px solid #dce3eb;border-radius:12px;padding:16px;margin-top:14px}
.idioma-combobox {position:relative}
.idioma-combobox-control {display:flex;align-items:center;gap:9px;border:1px solid #dce3eb;background:#f8fafc;border-radius:9px;min-height:40px;padding:0 12px;transition:border-color .15s,box-shadow .15s}
.idioma-combobox-control:focus-within,.idioma-combobox.is-open .idioma-combobox-control {border-color:#00bf92;box-shadow:0 0 0 .2rem rgba(0,191,146,.12);background:#fff}
.idioma-combobox-control.is-invalid {border-color:#dc3545}
.idioma-combobox-icon {color:#64748b}
.idioma-combobox-input {flex:1;min-width:0;border:0;background:transparent;outline:0;padding:9px 0;color:#102237;font-size:14px}
.idioma-combobox-toggle {border:0;background:transparent;color:#64748b;padding:6px;cursor:pointer}
.idioma-combobox-menu {position:absolute;z-index:1055;left:0;right:0;top:calc(100% + 5px);max-height:230px;overflow:auto;background:#fff;border:1px solid #dce3eb;border-radius:9px;box-shadow:0 12px 28px rgba(16,34,55,.14);padding:5px}
.idioma-combobox-option {display:block;width:100%;text-align:left;background:transparent;border:0;border-radius:6px;padding:9px 11px;color:#102237;font-size:14px;cursor:pointer}
.idioma-combobox-option:hover,.idioma-combobox-option.is-active {background:#e8f8f4}
.idioma-combobox-option[aria-selected=true] {font-weight:700;color:#008b72}
.idioma-combobox-empty {padding:12px;color:#64748b;font-size:13px}
.idioma-native-select {position:absolute!important;width:1px!important;height:1px!important;opacity:0!important;pointer-events:none!important;clip-path:inset(50%)}
.idioma-file-box {border:1px solid #dce3eb;background:#f8fafc;border-radius:10px;padding:16px}
.idioma-file-box input[type=file] {position:absolute;width:1px;height:1px;opacity:0;overflow:hidden;clip-path:inset(50%)}
.idioma-file-box input[type=file]:focus-visible + .idioma-file-button {outline:3px solid #80d7c6;outline-offset:2px}
.idioma-file-button {cursor:pointer;display:inline-flex;align-items:center;gap:8px}
.idioma-file-name {font-size:13px;color:#53657a;overflow-wrap:anywhere}
@media(max-width:575px){.idioma-card{flex-direction:column}.idioma-card-content{width:100%}}
</style>
@endpush
@push('scripts')
<script>
function mostrarFormularioIdioma(id){document.querySelectorAll('.idioma-panel .perfil-form-wrapper').forEach(el=>el.classList.add('d-none'));const t=document.getElementById(id);if(t){t.classList.remove('d-none');t.scrollIntoView({behavior:'smooth',block:'nearest'});}}
function cerrarFormulariosPerfil(){document.querySelectorAll('.idioma-panel .perfil-form-wrapper').forEach(el=>el.classList.add('d-none'));}
document.addEventListener('DOMContentLoaded', () => {
    // Ambos combobox conservan sus selects originales (name, value y validación HTML).
    document.querySelectorAll('[data-idioma-combobox]').forEach(combo => {
        const select = combo.querySelector('select.idioma-native-select');
        const input = combo.querySelector('.idioma-combobox-input');
        const toggle = combo.querySelector('.idioma-combobox-toggle');
        const menu = combo.querySelector('.idioma-combobox-menu');
        if (!select || !input || !menu) return;
        const entries = Array.from(select.options).filter(o => o.value).map(o => ({value:o.value,label:o.textContent.trim()}));
        const normalize = value => (value || '').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLocaleLowerCase('es').trim();
        let open = false, active = -1, matches = [];
        const currentLabel = () => entries.find(o => o.value === select.value)?.label || '';
        const close = () => {open=false;menu.hidden=true;combo.classList.remove('is-open');input.setAttribute('aria-expanded','false');input.value=currentLabel();active=-1;};
        const choose = entry => {select.value=entry.value;select.dispatchEvent(new Event('change',{bubbles:true}));close();input.focus();};
        const draw = (query='') => {
            matches=entries.filter(o=>normalize(o.label).includes(normalize(query)));
            menu.replaceChildren();active=-1;
            if (!matches.length) {const empty=document.createElement('div');empty.className='idioma-combobox-empty';empty.textContent=combo.dataset.emptyMessage || 'No se encontraron opciones';menu.append(empty);return;}
            matches.forEach((entry,i)=>{
                const btn=document.createElement('button');btn.type='button';btn.className='idioma-combobox-option';btn.setAttribute('role','option');btn.setAttribute('aria-selected',String(entry.value===select.value));btn.textContent=entry.label;
                btn.addEventListener('mousedown',e=>e.preventDefault());btn.addEventListener('click',()=>choose(entry));menu.append(btn);
            });
        };
        const show = (query='') => {open=true;menu.hidden=false;combo.classList.add('is-open');input.setAttribute('aria-expanded','true');draw(query);};
        input.value=currentLabel();
        input.addEventListener('focus',()=>{input.select();show('');});
        input.addEventListener('input',()=>show(input.value));
        toggle.addEventListener('click',()=>{if(open){close();}else{input.focus();input.select();show('');}});
        input.addEventListener('keydown',e=>{
            if(e.key==='Escape'){close();input.blur();return;}
            if(e.key==='Tab'){close();return;}
            if(e.key==='ArrowDown'||e.key==='ArrowUp'){
                e.preventDefault();if(!open)show('');if(!matches.length)return;
                active=(active+(e.key==='ArrowDown'?1:-1)+matches.length)%matches.length;
                Array.from(menu.children).forEach((node,i)=>node.classList.toggle('is-active',i===active));
                menu.children[active]?.scrollIntoView({block:'nearest'});
            }
            if(e.key==='Enter'&&open){e.preventDefault();if(matches.length===1)choose(matches[0]);else if(active>=0)choose(matches[active]);}
        });
        document.addEventListener('pointerdown',e=>{if(!combo.contains(e.target)&&open)close();});
        select.form?.addEventListener('submit',e=>{
            if(!select.value){e.preventDefault();show('');input.focus();input.setCustomValidity(combo.dataset.requiredMessage || 'Selecciona una opción de la lista.');input.reportValidity();input.setCustomValidity('');}
        });
    });
    document.querySelectorAll('.idioma-file-box input[type="file"]').forEach(input => {
        input.addEventListener('change', () => {
            const label = input.closest('.idioma-file-box')?.querySelector('[data-idioma-file-label]');
            if (label) label.textContent = input.files?.length ? input.files[0].name : 'Ningún archivo nuevo seleccionado';
        });
    });
});
</script>
@endpush
