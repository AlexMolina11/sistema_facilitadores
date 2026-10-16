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
        ? 'Mi formación | Facilitadores FEPADE'
        : 'Formación | Facilitadores FEPADE'
)

@section(
    'page-title',
    $esMiPerfil
        ? 'Mi formación'
        : 'Formación del consultor'
)

@section(
    'page-subtitle',
    $esMiPerfil
        ? 'Administra tu formación académica, educación continua y acreditaciones.'
        : 'Administra la trayectoria académica y profesional del consultor.'
)

@section('content')

<!--<x-ui.page-header
    :title="$esMiPerfil
        ? 'Mi trayectoria educativa'
        : 'Trayectoria educativa'"
    :subtitle="$esMiPerfil
        ? 'Registra tus estudios, acreditaciones, educación continua y documentos de respaldo.'
        : 'Registra estudios, acreditaciones, educación continua y documentos de respaldo del consultor.'"
/>-->

@include('fac.consultores.partials._fac_input_ux')

@include('fac.consultores.partials._wizard', ['step' => 4, 'consultor' => $consultor])

@foreach($catalogos['tiposFormacion'] as $tipoFormacion)
    @php
        $formaciones = $formacionesPorTipo->get($tipoFormacion->id_tipo_formacion, collect());
    @endphp
    <div class="perfil-panel mb-4">
        
        <div class="perfil-section-header">
            <div>
                <h4>{{ $tipoFormacion->nombre }}</h4>
                <p class="text-muted mb-0">Registros asociados a {{ strtolower($tipoFormacion->nombre) }}.</p>
            </div>

            <button 
                type="button" 
                class="btn btn-fepade"
                onclick="mostrarFormulario('crear-{{ $tipoFormacion->id_tipo_formacion }}')"
            >
                + Añadir registro
            </button>
        </div>

        <div id="crear-{{ $tipoFormacion->id_tipo_formacion }}" class="formacion-form-wrapper d-none">
            @include('fac.consultores.partials._formacion_form', [
                'consultor' => $consultor,
                'catalogos' => $catalogos,
                'tipoFormacion' => $tipoFormacion,
                'formacion' => null
            ])
        </div>

        @forelse($formaciones as $formacion)
            @php
                $tipoAtestado = $catalogos['tiposAtestado']->firstWhere('id_tipo_atestado', $formacion->id_tipo_atestado);
                $nivel = $catalogos['nivelesAcademicos']->firstWhere('id_nivel_academico', $formacion->id_nivel_academico);
                $pais = $catalogos['paises']->firstWhere('id_pais', $formacion->id_pais);
            @endphp

            <article class="formacion-card">
                <div class="formacion-icon" aria-hidden="true">
                    <i class="fa-regular fa-file-lines"></i>
                </div>

                <div class="formacion-card-body">
                    <div class="d-flex justify-content-between align-items-start gap-3">
                        <div>
                            <h5>{{ $formacion->titulo }}</h5>
                            <p class="text-muted mb-0">
                                {{ $tipoAtestado->nombre ?? 'Atestado' }}
                                @if($formacion->horas)
                                    · {{ $formacion->horas }} horas
                                @endif
                            </p>
                        </div>

                        <div class="d-flex gap-2">
                            <button 
                                type="button" 
                                class="btn btn-sm btn-outline-secondary d-inline-flex align-items-center gap-1"
                                onclick="mostrarFormulario('editar-{{ $formacion->id_atestado }}')"
                                title="Editar registro" aria-label="Editar registro"
                            >
                                <i class="fa-solid fa-pen" aria-hidden="true"></i>
                                <span>Editar</span>
                            </button>

                            <form 
                                method="POST" 
                                action="{{ route('fac.consultores.trayectoria.atestados.destroy', [$consultor, $formacion]) }}"
                                onsubmit="return confirm('¿Deseas eliminar este registro de trayectoria?')"
                            >
                                @csrf
                                @method('DELETE')

                                <button type="submit" class="btn btn-sm btn-outline-danger d-inline-flex align-items-center gap-1" title="Eliminar registro" aria-label="Eliminar registro">
                                    <i class="fa-solid fa-trash" aria-hidden="true"></i>
                                    <span>Eliminar</span>
                                </button>
                            </form>
                        </div>
                    </div>

                    @if($formacion->descripcion)
                        <p class="mt-3 mb-0">{{ $formacion->descripcion }}</p>
                    @endif

                    @php
                        $categoriaNombre = mb_strtolower($tipoFormacion->nombre);
                        $esFormal = str_contains($categoriaNombre, 'formal');
                        $esAcreditacion = str_contains($categoriaNombre, 'acreditaci');
                        $esConsultoria = str_contains($categoriaNombre, 'consultor');
                        $esImpartida = str_contains($categoriaNombre, 'impartida');
                        $esContinuaOCapacitacion = str_contains($categoriaNombre, 'continua') || str_contains($categoriaNombre, 'recibida') || $esImpartida;
                        $detalles = [];
                        if (($esFormal || $esContinuaOCapacitacion) && filled($formacion->institucion)) $detalles['Institución'] = $formacion->institucion;
                        if ($esFormal && $nivel) $detalles['Nivel académico'] = $nivel->nombre;
                        if ($esAcreditacion && filled($formacion->entidad_acreditadora)) $detalles['Entidad acreditadora'] = $formacion->entidad_acreditadora;
                        if (($esImpartida || $esConsultoria) && filled($formacion->cliente_institucion)) $detalles['Cliente / institución'] = $formacion->cliente_institucion;
                        if ($esAcreditacion && filled($formacion->codigo_acreditacion)) $detalles['Código de acreditación'] = $formacion->codigo_acreditacion;
                        if ($pais) $detalles['País'] = $pais->nombre_pais;
                        if ($esAcreditacion) {
                            if ($formacion->fecha_emision) $detalles['Emisión'] = $formacion->fecha_emision->format('d/m/Y');
                            if ($formacion->fecha_vencimiento) $detalles['Vencimiento'] = $formacion->fecha_vencimiento->format('d/m/Y');
                        } else {
                            if ($formacion->fecha_inicio) $detalles['Inicio'] = $formacion->fecha_inicio->format('d/m/Y');
                            if ($formacion->fecha_fin) $detalles['Finalización'] = $formacion->fecha_fin->format('d/m/Y');
                        }
                    @endphp
                    @if(count($detalles))
                        <div class="fac-atestado-details mt-3">
                            @foreach($detalles as $etiqueta => $dato)
                                <div class="fac-atestado-detail">
                                    <small class="text-muted d-block">{{ $etiqueta }}</small>
                                    <span>{{ $dato }}</span>
                                </div>
                            @endforeach
                        </div>
                    @endif
                    <div class="fac-atestado-document mt-3">
                        @if($formacion->url_archivo)
                            <i class="fa-regular fa-file-lines text-success" aria-hidden="true"></i>
                            <a href="{{ \Illuminate\Support\Facades\Storage::url($formacion->url_archivo) }}" target="_blank" rel="noopener noreferrer" class="fw-semibold">Ver documento</a>
                            @if($formacion->nombre_archivo_original)
                                <span class="text-muted small text-break">{{ $formacion->nombre_archivo_original }}</span>
                            @endif
                        @else
                            <i class="fa-regular fa-file text-muted" aria-hidden="true"></i>
                            <span class="text-muted small">Sin documento adjunto</span>
                        @endif
                    </div>

                    <div id="editar-{{ $formacion->id_atestado }}" class="formacion-form-wrapper d-none mt-3">
                        @include('fac.consultores.partials._formacion_form', [
                            'consultor' => $consultor,
                            'catalogos' => $catalogos,
                            'tipoFormacion' => $tipoFormacion,
                            'formacion' => $formacion
                        ])
                    </div>
                </div>
            </article>
        @empty
            <div class="fac-atestado-empty mb-3">
                <i class="fa-regular fa-folder-open" aria-hidden="true"></i>
                <span>Aún no hay registros de {{ strtolower($tipoFormacion->nombre) }}. Usa «Añadir registro» para comenzar.</span>
            </div>
        @endforelse
    </div>
@endforeach

@include(
    'fac.consultores.partials._wizard_actions',
    [
        'consultor' =>
            $consultor,

        'anterior' =>
            route(
                'fac.consultores.experiencia.edit',
                $consultor
            ),

        'continuarRoute' =>
            route(
                'fac.consultores.formacion.continuar',
                $consultor
            ),

        'continuarLabel' =>
            'Continuar a Especialización',
    ]
)

<style>
.fac-guide-progress{height:7px;background:#e9edf2;border-radius:99px;overflow:hidden}.fac-guide-progress-fill{display:block;width:33.333%;height:100%;background:#00bf92;transition:width .2s}.fac-guide-step[hidden],.fac-guide-back[hidden],.fac-guide-next[hidden],.fac-guide-save[hidden]{display:none!important}.fac-guide-step .form-label{font-weight:600}.fac-guided-form .fac-guide-step .row{align-items:start}.fac-guided-form .fac-guide-step [data-fac-field-hidden="1"]{display:none!important}.fac-guide-step .form-text{font-size:.82rem}

/* Formación: controles y tarjetas consistentes con Contacto */
.fac-guided-form .fac-guide-document-help:empty{display:none!important}
.fac-guided-form .fac-guide-review[hidden]{display:none!important}
.fac-guided-form .fac-guide-review{margin-bottom:0}
.fac-guided-form .fac-upload-box{display:flex;align-items:center;gap:12px;flex-wrap:wrap;border:1px dashed #cbd5e1;border-radius:10px;padding:14px;background:#f8fafc}
.fac-guided-form .fac-upload-button{white-space:nowrap;cursor:pointer}
.fac-guided-form .fac-upload-filename{overflow-wrap:anywhere}
.fac-atestado-details{display:grid;grid-template-columns:repeat(3,minmax(0,1fr));gap:16px 24px}
.fac-atestado-detail span{font-weight:500;overflow-wrap:anywhere}
.fac-atestado-document{border-top:1px solid #e5e7eb;padding-top:14px;display:flex;align-items:center;gap:10px;flex-wrap:wrap}
.fac-atestado-empty{border:1px dashed #d6dee6;border-radius:12px;padding:20px;color:#64748b;display:flex;align-items:center;gap:12px;background:#fafcfd}
.fac-atestado-empty i{font-size:1.35rem;color:#94a3b8}
@media(max-width:768px){.fac-atestado-details{grid-template-columns:repeat(2,minmax(0,1fr))}}
@media(max-width:480px){.fac-atestado-details{grid-template-columns:1fr}}
</style>


<style>
/* Selector integrado: mismo lenguaje visual que Idiomas y Referencias. */
.fac-formacion-combo{position:relative;min-width:0}
.fac-formacion-combo-control{display:flex;align-items:center;gap:9px;min-height:40px;padding:0 11px;background:#f8fafc;border:1px solid #dce3eb;border-radius:9px;transition:border-color .15s,box-shadow .15s}
.fac-formacion-combo:focus-within .fac-formacion-combo-control,.fac-formacion-combo.is-open .fac-formacion-combo-control{border-color:#00bf92;box-shadow:0 0 0 .2rem rgba(0,191,146,.12);background:#fff}
.fac-formacion-combo-control.is-invalid{border-color:#dc3545}
.fac-formacion-combo-control i{color:#64748b}
.fac-formacion-combo-input{width:100%;min-width:0;flex:1;border:0!important;background:transparent!important;outline:0!important;box-shadow:none!important;padding:9px 0;font-size:14px;color:#102237}
.fac-formacion-combo-toggle{border:0;background:transparent;padding:6px;cursor:pointer;color:#64748b}
.fac-formacion-combo-menu{position:absolute;top:calc(100% + 5px);left:0;right:0;z-index:1060;max-height:230px;overflow:auto;padding:5px;background:#fff;border:1px solid #dce3eb;border-radius:9px;box-shadow:0 12px 28px rgba(16,34,55,.14)}
.fac-formacion-combo-menu[hidden]{display:none!important}
.fac-formacion-combo-option{display:block;width:100%;border:0;border-radius:6px;background:transparent;text-align:left;padding:9px 11px;color:#102237;font-size:14px;cursor:pointer}
.fac-formacion-combo-option:hover,.fac-formacion-combo-option.is-active{background:#e8f8f4}
.fac-formacion-combo-option[aria-selected="true"]{font-weight:700;color:#008b72}
.fac-formacion-combo-empty{padding:12px;color:#64748b;font-size:13px}
/* Mantener el select real para Laravel y para el JavaScript existente. */
.fac-formacion-combo-native{position:absolute!important;width:1px!important;height:1px!important;opacity:0!important;pointer-events:none!important;overflow:hidden!important;left:0;bottom:0}
</style>
<script>
(function(){
 const names=['id_tipo_atestado','id_nivel_academico','id_pais'];
 const normalize=s=>(s||'').normalize('NFD').replace(/[\u0300-\u036f]/g,'').toLocaleLowerCase('es').trim();
 function enhance(select){
  if(select.dataset.facComboReady==='1')return;
  select.dataset.facComboReady='1';
  const container=document.createElement('div');container.className='fac-formacion-combo';
  const control=document.createElement('div');control.className='fac-formacion-combo-control';
  if(select.classList.contains('is-invalid'))control.classList.add('is-invalid');
  const icon=document.createElement('i');icon.className='fa-solid fa-magnifying-glass';icon.setAttribute('aria-hidden','true');
  const input=document.createElement('input');input.type='text';input.className='fac-formacion-combo-input';input.autocomplete='off';input.setAttribute('role','combobox');input.setAttribute('aria-autocomplete','list');input.setAttribute('aria-expanded','false');input.setAttribute('aria-label',select.closest('[class*="col-"]')?.querySelector('.form-label')?.textContent.trim()||'Buscar opción');
  const toggle=document.createElement('button');toggle.type='button';toggle.className='fac-formacion-combo-toggle';toggle.setAttribute('aria-label','Mostrar opciones');toggle.innerHTML='<i class="fa-solid fa-chevron-down" aria-hidden="true"></i>';
  const menu=document.createElement('div');menu.className='fac-formacion-combo-menu';menu.setAttribute('role','listbox');menu.hidden=true;
  select.parentNode.insertBefore(container,select);container.append(control,menu,select);control.append(icon,input,toggle);
  select.classList.add('fac-formacion-combo-native');select.setAttribute('tabindex','-1');
  let matches=[],active=-1,open=false;
  const options=()=>Array.from(select.options).map(o=>({value:o.value,label:o.textContent.trim(),disabled:o.disabled}));
  const currentLabel=()=>select.selectedOptions[0]?.textContent.trim()||'';
  function close(){open=false;menu.hidden=true;container.classList.remove('is-open');input.setAttribute('aria-expanded','false');input.value=currentLabel();active=-1;}
  function choose(entry){select.value=entry.value;select.dispatchEvent(new Event('change',{bubbles:true}));close();input.focus();}
  function draw(query){matches=options().filter(o=>!o.disabled&&(!query||normalize(o.label).includes(normalize(query))));menu.replaceChildren();active=-1;
   if(!matches.length){const empty=document.createElement('div');empty.className='fac-formacion-combo-empty';empty.textContent='No se encontraron opciones';menu.append(empty);return;}
   matches.forEach((entry,i)=>{const btn=document.createElement('button');btn.type='button';btn.className='fac-formacion-combo-option';btn.textContent=entry.label;btn.setAttribute('role','option');btn.setAttribute('aria-selected',String(select.value===entry.value));btn.addEventListener('mousedown',e=>e.preventDefault());btn.addEventListener('click',()=>choose(entry));menu.append(btn);});
  }
  function show(query=''){open=true;menu.hidden=false;container.classList.add('is-open');input.setAttribute('aria-expanded','true');draw(query);}
  input.value=currentLabel();
  input.addEventListener('focus',()=>{input.select();show('');});
  input.addEventListener('input',()=>show(input.value));
  toggle.addEventListener('click',()=>{if(open)close();else{input.focus();input.select();show('');}});
  input.addEventListener('keydown',e=>{
   if(e.key==='Escape'){close();input.blur();return;}
   if(e.key==='Tab'){close();return;}
   if(e.key==='ArrowDown'||e.key==='ArrowUp'){e.preventDefault();if(!open)show('');if(!matches.length)return;active=(active+(e.key==='ArrowDown'?1:-1)+matches.length)%matches.length;Array.from(menu.children).forEach((node,i)=>node.classList.toggle('is-active',i===active));menu.children[active]?.scrollIntoView({block:'nearest'});}
   if(e.key==='Enter'&&open){e.preventDefault();if(matches.length===1)choose(matches[0]);else if(active>=0)choose(matches[active]);}
  });
  select.addEventListener('change',()=>{input.value=currentLabel();control.classList.remove('is-invalid');});
  document.addEventListener('pointerdown',e=>{if(open&&!container.contains(e.target))close();});
  // Si se escribe sin elegir, restaurar la opción original, sin alterar el ID guardado.
 }
 document.querySelectorAll('.fac-guided-form select').forEach(select=>{if(names.includes(select.name))enhance(select);});
})();
</script>

<script>
(function(){
 const categoryMap={
  'educación formal': 'formal','educacion formal':'formal',
  'educación continua':'continua','educacion continua':'continua',
  'acreditación':'acreditacion','acreditacion':'acreditacion',
  'capacitación recibida':'recibida','capacitacion recibida':'recibida',
  'capacitación impartida':'impartida','capacitacion impartida':'impartida',
  'consultoría realizada':'consultoria','consultoria realizada':'consultoria'
 };
 const config={
  formal:{show:['titulo','institucion','id_nivel_academico','id_pais','fecha_inicio','fecha_fin','fecha_emision','descripcion'],labels:{titulo:'Nombre del título o programa',institucion:'Institución educativa'}},
  continua:{show:['titulo','institucion','id_pais','horas','fecha_inicio','fecha_fin','descripcion'],labels:{titulo:'Nombre del curso, diplomado o programa',institucion:'Institución que impartió la formación',horas:'Horas de formación'}},
  acreditacion:{show:['titulo','entidad_acreditadora','codigo_acreditacion','id_pais','fecha_emision','fecha_vencimiento','descripcion'],labels:{titulo:'Nombre de la acreditación',entidad_acreditadora:'Entidad emisora o acreditadora'}},
  recibida:{show:['titulo','institucion','id_pais','horas','fecha_inicio','fecha_fin','descripcion'],labels:{titulo:'Nombre de la capacitación recibida',institucion:'Institución que impartió la capacitación',horas:'Horas recibidas'}},
  impartida:{show:['titulo','cliente_institucion','id_pais','horas','fecha_inicio','fecha_fin','descripcion'],labels:{titulo:'Nombre de la capacitación impartida',cliente_institucion:'Institución o cliente',horas:'Horas impartidas'}},
  consultoria:{show:['titulo','cliente_institucion','id_pais','fecha_inicio','fecha_fin','descripcion'],labels:{titulo:'Nombre de la consultoría o proyecto',cliente_institucion:'Cliente o institución contratante'}}
 };
 const allNames=['titulo','institucion','id_nivel_academico','id_pais','entidad_acreditadora','cliente_institucion','codigo_acreditacion','fecha_inicio','fecha_fin','fecha_emision','fecha_vencimiento','horas','descripcion'];
 function init(form){
  const category=categoryMap[form.dataset.facCategory]||'formal', conf=config[category];let step=1;
  const type=form.querySelector('[name="id_tipo_atestado"]');
  const next=form.querySelector('.fac-guide-next'),back=form.querySelector('.fac-guide-back'),save=form.querySelector('.fac-guide-save');
  const labels={};
  allNames.forEach(name=>{
   const field=form.querySelector('[name="'+name+'"]');if(!field)return;
   const group=field.closest('[class*="col-md-"]');if(!group)return;
   const label=group.querySelector('.form-label');if(label)labels[name]=label.innerHTML;
   // Preserve existing values and submitted field names. Do not disable or delete hidden fields.
   group.dataset.facFieldHidden=conf.show.includes(name)?'0':'1';
   if(label&&conf.labels[name])label.textContent=conf.labels[name]+(name==='titulo'?' *':'');
  });
  const help=form.querySelector('.fac-guide-document-help');
  function updateHelp(){const selected=type?.selectedOptions[0]?.textContent?.trim();help.textContent=selected&&type.value?'Documento seleccionado: '+selected+'. Adjunta el respaldo correspondiente en el último paso.':'Puedes seleccionar el tipo de atestado correspondiente. Si no lo conoces, Laravel permite dejarlo sin seleccionar.';}
  type?.addEventListener('change',updateHelp);updateHelp();
  const fileInput=form.querySelector('[name="archivo_atestado"]');
  const fileLabel=form.querySelector('.fac-upload-filename');
  fileInput?.addEventListener('change',()=>{if(fileLabel)fileLabel.textContent=fileInput.files?.[0]?.name||'Ningún archivo nuevo seleccionado';});
  function render(){const review=form.querySelector('.fac-guide-review');if(review)review.hidden=step!==3;form.querySelectorAll('[data-fac-step]').forEach(el=>el.hidden=Number(el.dataset.facStep)!==step);back.hidden=step===1;next.hidden=step===3;save.hidden=step!==3;form.querySelector('.fac-guide-count').textContent='Paso '+step+' de 3';form.querySelector('.fac-guide-progress-fill').style.width=(step*100/3)+'%';if(step===3){const title=form.querySelector('[name="titulo"]')?.value||'Sin título';const selected=type?.selectedOptions[0]?.textContent?.trim()||'Sin tipo';form.querySelector('.fac-guide-review').hidden=false;form.querySelector('.fac-guide-review').textContent='Resumen: '+title+' · '+selected+'. Comprueba la información antes de guardar.';}}
  function validateStep(){const current=form.querySelector('[data-fac-step="'+step+'"]');for(const input of current.querySelectorAll('input,select,textarea')){if(input.closest('[data-fac-field-hidden="1"]'))continue;if(!input.checkValidity()){input.reportValidity();return false;}}return true;}
  next.addEventListener('click',()=>{if(!validateStep())return;step=Math.min(3,step+1);render();});back.addEventListener('click',()=>{step=Math.max(1,step-1);render();});
  form.addEventListener('submit',()=>{ // Hidden optional fields are not cleared: preserve data when editing.
   form.querySelectorAll('[data-fac-field-hidden="1"] input,[data-fac-field-hidden="1"] select,[data-fac-field-hidden="1"] textarea').forEach(el=>{el.required=false;});
  });
  if(form.dataset.facError==='1'){
   const error=form.querySelector('.is-invalid');const errorSection=error?.closest('[data-fac-step]');step=errorSection?Number(errorSection.dataset.facStep):2;
   const wrapper=form.closest('.formacion-form-wrapper');if(wrapper){wrapper.classList.remove('d-none');}
  }
  render();
 }
 document.querySelectorAll('[data-fac-guided-form]').forEach(init);
})();
</script>

<script>
    function cerrarFormularios() {
        document.querySelectorAll('.formacion-form-wrapper').forEach(element => {
            element.classList.add('d-none');
        });
    }

    function mostrarFormulario(id) {
        const element = document.getElementById(id);

        if (!element) {
            return;
        }

        const estabaOculto = element.classList.contains('d-none');

        cerrarFormularios();

        if (estabaOculto) {
            element.classList.remove('d-none');
            element.scrollIntoView({ behavior: 'smooth', block: 'center' });
        }
    }
</script>

@endsection
