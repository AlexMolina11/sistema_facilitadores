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
        ? 'Mis referencias | Facilitadores FEPADE'
        : 'Referencias | Facilitadores FEPADE'
)

@section(
    'page-title',
    $esMiPerfil
        ? 'Mis referencias'
        : 'Referencias del consultor'
)

@section(
    'page-subtitle',
    $esMiPerfil
        ? 'Administra las referencias personales y profesionales de tu expediente.'
        : 'Administra las referencias personales y profesionales del consultor.'
)

@push('styles')
<link rel="stylesheet" href="{{ asset('vendor/intl-tel-input/css/intlTelInput.min.css') }}">
<style>
    .referencia-form .iti { width: 100%; }
    .referencia-form .iti input { width: 100%; }
    .fac-referencias-panel { padding: 20px; }
    .fac-referencias-header { display: flex; justify-content: space-between; align-items: center; gap: 12px; flex-wrap: wrap; margin-bottom: 16px; }
    .fac-referencias-header h4 { font-size: 1.15rem; font-weight: 650; }
    .fac-ref-icon { font-size: 1rem; color: #1677ee; }
    .fac-ref-card { padding: 16px; margin-bottom: 12px; align-items: flex-start; }
    .fac-ref-avatar { flex: 0 0 auto; }
    .fac-ref-actions { display: flex; flex: 0 0 auto; align-items: center; flex-wrap: nowrap !important; gap: 7px !important; }
    .fac-ref-actions form { display: inline-flex; margin: 0; flex: 0 0 auto; }
    .fac-ref-actions .btn { display: inline-flex; align-items: center; justify-content: center; gap: 4px; white-space: nowrap; flex: 0 0 auto; min-height: 34px; padding: 6px 10px; line-height: 1.2; width: auto; }
    .fac-ref-actions .btn i { flex: 0 0 auto; margin-right: 2px !important; }
    .fac-ref-card .perfil-card-body > .d-flex:first-child { gap: 12px !important; }
    .referencia-form .col-12.d-flex.justify-content-end { flex-wrap: wrap; }
    .referencia-form .col-12.d-flex.justify-content-end .btn { white-space: nowrap; width: auto; flex: 0 0 auto; }
    .fac-ref-contact { display: flex; flex-wrap: wrap; gap: 6px 18px; font-size: .88rem; color: #586575; }
    .fac-ref-empty { border: 1px dashed #d5dce4; border-radius: 12px; background: #f8fafc; }
    .fac-ref-empty > i { font-size: 1.4rem; color: #9aa7b6; }
    .fac-referencias-grid .perfil-card-body { min-width: 0; }
    .fac-referencias-grid .perfil-form-wrapper { width: 100%; }
    .referencia-form { background: #fff !important; border-color: #dce3e9 !important; border-radius: 12px !important; }
    .referencia-form .form-control, .referencia-form .form-select { border-radius: 9px; min-height: 40px; }
    .referencia-form .form-label { font-weight: 600; }

.fac-ref-combobox{position:relative}
.fac-ref-combobox-control{display:flex;align-items:center;gap:9px;border:1px solid #dce3eb;background:#f8fafc;border-radius:9px;min-height:40px;padding:0 12px;transition:border-color .15s,box-shadow .15s}
.fac-ref-combobox-control:focus-within,.fac-ref-combobox.is-open .fac-ref-combobox-control{border-color:#00bf92;box-shadow:0 0 0 .2rem rgba(0,191,146,.12);background:#fff}
.fac-ref-combobox-control.is-invalid{border-color:#dc3545}
.fac-ref-combobox-icon{color:#64748b}
.fac-ref-combobox-input{flex:1;min-width:0;border:0;background:transparent;outline:0;padding:9px 0;color:#102237;font-size:14px}
.fac-ref-combobox-toggle{border:0;background:transparent;color:#64748b;padding:6px;cursor:pointer}
.fac-ref-combobox-menu{position:absolute;z-index:1055;left:0;right:0;top:calc(100% + 5px);max-height:230px;overflow:auto;background:#fff;border:1px solid #dce3eb;border-radius:9px;box-shadow:0 12px 28px rgba(16,34,55,.14);padding:5px}
.fac-ref-combobox-option{display:block;width:100%;text-align:left;background:transparent;border:0;border-radius:6px;padding:9px 11px;color:#102237;font-size:14px;cursor:pointer}
.fac-ref-combobox-option:hover,.fac-ref-combobox-option.is-active{background:#e8f8f4}
.fac-ref-combobox-option[aria-selected=true]{font-weight:700;color:#008b72}
.fac-ref-combobox-empty{padding:12px;color:#64748b;font-size:13px}
.fac-ref-native-select{position:absolute!important;width:1px!important;height:1px!important;opacity:0!important;pointer-events:none!important;clip-path:inset(50%)}

    @media (max-width: 575.98px) { .fac-referencias-panel { padding: 14px; } .fac-ref-card { padding: 12px; } }
</style>
@endpush

@section('content')

<x-ui.page-header
    :title="$esMiPerfil
        ? 'Mis referencias'
        : 'Referencias'"
    :subtitle="$esMiPerfil
        ? 'Registra contactos que puedan respaldar tu trayectoria personal o profesional.'
        : 'Registra contactos de referencia asociados al expediente del consultor.'"
/>

@include('fac.consultores.partials._wizard', ['step' => 7, 'consultor' => $consultor])

<div class="row g-4 fac-referencias-grid mb-4">
@foreach($catalogos['tiposReferencia'] as $tipoReferencia)
    @php
        $items = $referenciasPorTipo->get($tipoReferencia->id_tipo_referencia, collect());
        $nombreTipo = strtolower($tipoReferencia->nombre ?? '');
        $esLaboral = str_contains($nombreTipo, 'laboral') || str_contains($nombreTipo, 'profesional');
    @endphp
    <div class="col-12 col-xl-6 d-flex">
        <section class="perfil-panel fac-referencias-panel w-100 mb-0" aria-label="{{ $tipoReferencia->nombre }}">
            <div class="perfil-section-header fac-referencias-header">
                <div>
                    <h4 class="mb-1"><i class="fas {{ $esLaboral ? 'fa-briefcase' : 'fa-user-friends' }} me-2 fac-ref-icon" aria-hidden="true"></i>{{ $tipoReferencia->nombre }}</h4>
                    <p class="text-muted mb-0">{{ $items->count() }} de 3 referencias registradas</p>
                </div>
                @if($items->count() < 3)
                    <button type="button" class="btn btn-fepade" onclick="mostrarFormularioPerfil('crear-referencia-{{ $tipoReferencia->id_tipo_referencia }}')"><i class="fas fa-plus me-1" aria-hidden="true"></i>Añadir referencia</button>
                @else
                    <span class="badge badge-warning-soft">Límite alcanzado</span>
                @endif
            </div>
            <div id="crear-referencia-{{ $tipoReferencia->id_tipo_referencia }}" class="perfil-form-wrapper d-none mb-3">
                @include('fac.consultores.partials._referencia_form', ['consultor' => $consultor, 'catalogos' => $catalogos, 'tipoReferencia' => $tipoReferencia, 'referencia' => null])
            </div>
            @forelse($items as $referencia)
                @php $relacion = $catalogos['tiposRelacion']->firstWhere('id_tipo_relacion', $referencia->id_tipo_relacion); @endphp
                <article class="perfil-card-item fac-ref-card">
                    <div class="perfil-card-icon fac-ref-avatar"><i class="fas fa-user" aria-hidden="true"></i></div>
                    <div class="perfil-card-body min-w-0 w-100">
                        <div class="d-flex justify-content-between align-items-start flex-wrap gap-2">
                            <div class="min-w-0">
                                <div class="perfil-card-title">{{ $referencia->nombre }}</div>
                                @if($relacion)<div class="perfil-card-subtitle">{{ $relacion->nombre }}</div>@endif
                                @if($referencia->cargo || $referencia->empresa)
                                    <div class="perfil-card-subtitle">{{ $referencia->cargo }} @if($referencia->empresa) · {{ $referencia->empresa }} @endif</div>
                                @endif
                            </div>
                            <div class="d-flex gap-2 flex-wrap fac-ref-actions">
                                <button type="button" class="btn btn-sm btn-outline-secondary" onclick="mostrarFormularioPerfil('editar-referencia-{{ $referencia->id_referencia }}')"><i class="fas fa-pen me-1" aria-hidden="true"></i>Editar</button>
                                <form method="POST" action="{{ route('fac.consultores.referencias.destroy', [$consultor, $referencia]) }}" onsubmit="return confirm('¿Deseas eliminar esta referencia?')">
                                    @csrf @method('DELETE')
                                    <button type="submit" class="btn btn-sm btn-outline-danger"><i class="fas fa-trash-alt me-1" aria-hidden="true"></i>Eliminar</button>
                                </form>
                            </div>
                        </div>
                        @if($referencia->telefono || $referencia->correo)
                            <div class="fac-ref-contact mt-3">
                                @if($referencia->telefono)<div><i class="fas fa-phone-alt me-2" aria-hidden="true"></i>{{ $referencia->telefono }}</div>@endif
                                @if($referencia->correo)<div class="text-break"><i class="fas fa-envelope me-2" aria-hidden="true"></i>{{ $referencia->correo }}</div>@endif
                            </div>
                        @endif
                        <div id="editar-referencia-{{ $referencia->id_referencia }}" class="perfil-form-wrapper d-none mt-3">
                            @include('fac.consultores.partials._referencia_form', ['consultor' => $consultor, 'catalogos' => $catalogos, 'tipoReferencia' => $tipoReferencia, 'referencia' => $referencia])
                        </div>
                    </div>
                </article>
            @empty
                <div class="fac-ref-empty text-center p-4">
                    <i class="fas fa-address-book mb-2" aria-hidden="true"></i>
                    <p class="mb-1 fw-semibold">Aún no hay referencias</p>
                    <p class="text-muted mb-0 small">Añade un contacto de referencia para completar esta sección.</p>
                </div>
            @endforelse
        </section>
    </div>
@endforeach
</div>

@include(
    'fac.consultores.partials._wizard_actions',
    [
        'consultor' =>
            $consultor,

        'anterior' =>
            route(
                'fac.consultores.idiomas.edit',
                $consultor
            ),

        'continuarRoute' =>
            route(
                'fac.consultores.referencias.continuar',
                $consultor
            ),

        'continuarLabel' =>
            'Continuar a Disponibilidad',
    ]
)
@endsection
@push('scripts')
<script src="{{ asset('vendor/intl-tel-input/js/intlTelInputWithUtils.min.js') }}"></script>
<script>
function mostrarFormularioPerfil(id) {
    document.querySelectorAll('.perfil-form-wrapper').forEach(el => el.classList.add('d-none'));
    const target = document.getElementById(id);
    if (target) {
        target.classList.remove('d-none');
        target.scrollIntoView({behavior: 'smooth', block: 'nearest'});
    }
}
function cerrarFormulariosPerfil() {
    document.querySelectorAll('.perfil-form-wrapper').forEach(el => el.classList.add('d-none'));
}
document.addEventListener('DOMContentLoaded', function () {
    document.querySelectorAll('[data-fac-ref-combobox]').forEach(combo => {
        const select = combo.querySelector('select.fac-ref-native-select');
        const input = combo.querySelector('.fac-ref-combobox-input');
        const toggle = combo.querySelector('.fac-ref-combobox-toggle');
        const menu = combo.querySelector('.fac-ref-combobox-menu');
        if (!select || !input || !menu) return;
        const entries = Array.from(select.options).filter(o => o.value).map(o => ({value:o.value,label:o.textContent.trim()}));
        const normalize = value => (value || '').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLocaleLowerCase('es').trim();
        let opened=false, active=-1, matches=[];
        const currentLabel=()=>entries.find(o=>o.value===select.value)?.label || '';
        const close=()=>{opened=false;menu.hidden=true;combo.classList.remove('is-open');input.setAttribute('aria-expanded','false');input.value=currentLabel();active=-1;};
        const choose=entry=>{select.value=entry.value;select.dispatchEvent(new Event('change',{bubbles:true}));close();input.focus();};
        const draw=query=>{
            matches=entries.filter(o=>normalize(o.label).includes(normalize(query)));menu.replaceChildren();active=-1;
            if(!matches.length){const el=document.createElement('div');el.className='fac-ref-combobox-empty';el.textContent='No se encontraron relaciones';menu.append(el);return;}
            matches.forEach(entry=>{const btn=document.createElement('button');btn.type='button';btn.className='fac-ref-combobox-option';btn.setAttribute('role','option');btn.setAttribute('aria-selected',String(entry.value===select.value));btn.textContent=entry.label;btn.addEventListener('mousedown',e=>e.preventDefault());btn.addEventListener('click',()=>choose(entry));menu.append(btn);});
        };
        const show=query=>{opened=true;menu.hidden=false;combo.classList.add('is-open');input.setAttribute('aria-expanded','true');draw(query);};
        input.value=currentLabel();
        input.addEventListener('focus',()=>{input.select();show('');});
        input.addEventListener('input',()=>show(input.value));
        toggle.addEventListener('click',()=>{if(opened){close();}else{input.focus();input.select();show('');}});
        input.addEventListener('keydown',e=>{
            if(e.key==='Escape'){close();input.blur();return;}
            if(e.key==='Tab'){close();return;}
            if(e.key==='ArrowDown'||e.key==='ArrowUp'){e.preventDefault();if(!opened)show('');if(!matches.length)return;active=(active+(e.key==='ArrowDown'?1:-1)+matches.length)%matches.length;Array.from(menu.children).forEach((node,i)=>node.classList.toggle('is-active',i===active));menu.children[active]?.scrollIntoView({block:'nearest'});}
            if(e.key==='Enter'&&opened){e.preventDefault();if(matches.length===1)choose(matches[0]);else if(active>=0)choose(matches[active]);}
        });
        document.addEventListener('pointerdown',e=>{if(!combo.contains(e.target)&&opened)close();});
        select.form?.addEventListener('submit',e=>{if(!select.value){e.preventDefault();show('');input.focus();input.setCustomValidity(combo.dataset.requiredMessage||'Selecciona una opción.');input.reportValidity();input.setCustomValidity('');}});
    });

    const telefonos = new Map();
    document.querySelectorAll('.referencia-form [data-referencia-telefono]').forEach(input => {
        if (typeof window.intlTelInput !== 'function') return;
        const iti = window.intlTelInput(input, {
            initialCountry: 'sv',
            countryOrder: ['sv', 'gt', 'hn', 'ni', 'cr', 'pa', 'us', 'mx'],
            nationalMode: true,
            autoPlaceholder: 'polite',
            formatAsYouType: true
        });
        telefonos.set(input, iti);
        if (input.value.trim().startsWith('+')) iti.setNumber(input.value.trim());
    });

    document.querySelectorAll('.referencia-form').forEach(form => {
        form.addEventListener('submit', function (event) {
            const input = form.querySelector('[data-referencia-telefono]');
            const iti = input && telefonos.get(input);
            if (!input || !iti || !input.value.trim()) return;

            if (!iti.isValidNumber()) {
                event.preventDefault();
                input.setCustomValidity('Ingresa un número telefónico válido para el país seleccionado.');
                input.reportValidity();
                input.addEventListener('input', () => input.setCustomValidity(''), {once: true});
                return;
            }
            input.setCustomValidity('');
            input.value = iti.getNumber();
        });
    });

    const formularioConErrores = @json(old('_referencia_form_id', ''));
    const hayErrores = @json($errors->any());
    if (hayErrores && formularioConErrores) {
        mostrarFormularioPerfil(formularioConErrores);
    }
});
</script>
@endpush
