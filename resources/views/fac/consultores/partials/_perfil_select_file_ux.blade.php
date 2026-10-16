{{-- UX de Perfil: no modifica nombres, valores ni eventos change de los select originales. --}}
<style>
.fac-search-select-native{position:absolute!important;width:1px!important;height:1px!important;opacity:0!important;pointer-events:none!important;}
.fac-search-wrap{position:relative;min-width:0}
.fac-search-control{display:flex;align-items:center;gap:9px;border:1px solid #dce3eb;background:#f8fafc;border-radius:9px;min-height:40px;padding:0 12px;transition:border-color .15s,box-shadow .15s}
.fac-search-wrap:focus-within .fac-search-control,.fac-search-wrap.is-open .fac-search-control{border-color:#00bf92;box-shadow:0 0 0 .2rem rgba(0,191,146,.12);background:#fff}
.fac-search-control.is-invalid{border-color:#dc3545}
.fac-search-control i{color:#64748b}
.fac-search-input{flex:1;min-width:0;border:0;outline:0;background:transparent;padding:9px 0;color:#102237;font-size:14px}
.fac-search-toggle{border:0;background:transparent;color:#64748b;padding:6px;cursor:pointer}
.fac-search-menu{position:absolute;z-index:1100;left:0;right:0;top:calc(100% + 5px);max-height:230px;overflow:auto;background:#fff;border:1px solid #dce3eb;border-radius:9px;box-shadow:0 12px 28px rgba(16,34,55,.14);padding:5px}
.fac-search-option{display:block;width:100%;text-align:left;background:transparent;border:0;border-radius:6px;padding:9px 11px;color:#102237;font-size:14px;cursor:pointer}
.fac-search-option:hover,.fac-search-option.is-active{background:#e8f8f4}
.fac-search-option[aria-selected="true"]{font-weight:700;color:#008b72}
.fac-search-empty{padding:12px;color:#64748b;font-size:13px}
.fac-file-picker{display:flex;align-items:center;gap:12px;flex-wrap:wrap;min-height:40px;border:1px solid #dce3eb;border-radius:9px;background:#f8fafc;padding:5px 9px}
.fac-file-picker-button{position:relative;overflow:hidden;display:inline-flex;align-items:center;gap:7px;border:1px solid #7b899a;border-radius:7px;background:#fff;color:#475569;padding:7px 11px;cursor:pointer;font-size:13px;font-weight:600;white-space:nowrap}
.fac-file-picker-button:hover{border-color:#00bf92;color:#008b72}
.fac-file-native{position:absolute!important;width:100%!important;height:100%!important;inset:0;opacity:0;cursor:pointer;font-size:100px}
.fac-file-picker-name{color:#64748b;font-size:13px;min-width:0;overflow-wrap:anywhere}
.fac-file-picker:has(.fac-file-native.is-invalid){border-color:#dc3545}
</style>
<script>
document.addEventListener('DOMContentLoaded', () => {
    const norm = s => String(s || '').normalize('NFD').replace(/[\u0300-\u036f]/g, '').toLowerCase();
    document.querySelectorAll('select[data-fac-search-select]').forEach((select, index) => {
        const wrap = document.createElement('div'); wrap.className='fac-search-wrap';
        const control = document.createElement('div'); control.className='fac-search-control';
        if(select.classList.contains('is-invalid')) control.classList.add('is-invalid');
        const icon = document.createElement('i'); icon.className='fa-solid fa-magnifying-glass'; icon.setAttribute('aria-hidden','true');
        const input = document.createElement('input'); input.type='text'; input.className='fac-search-input'; input.autocomplete='off';
        input.setAttribute('role','combobox'); input.setAttribute('aria-autocomplete','list'); input.setAttribute('aria-expanded','false');
        input.setAttribute('aria-label', 'Buscar '+(select.name||'opción'));
        const toggle = document.createElement('button'); toggle.type='button'; toggle.className='fac-search-toggle'; toggle.setAttribute('aria-label','Mostrar opciones');
        toggle.innerHTML='<i class="fa-solid fa-chevron-down" aria-hidden="true"></i>';
        const menu=document.createElement('div'); menu.className='fac-search-menu'; menu.hidden=true; menu.setAttribute('role','listbox');
        menu.id='fac-search-options-'+index; input.setAttribute('aria-controls',menu.id);
        control.append(icon,input,toggle); wrap.append(control,menu);
        select.parentNode.insertBefore(wrap,select.nextSibling);
        select.classList.add('fac-search-select-native');
        let active=-1;
        const available=()=>Array.from(select.options).filter(o=>o.value && !o.disabled && !o.hidden && !o.closest('optgroup[hidden]'));
        const sync=()=>{const opt=select.selectedOptions[0];input.value=opt && opt.value && !opt.hidden ? opt.textContent.trim() : '';};
        const close=()=>{menu.hidden=true;wrap.classList.remove('is-open');input.setAttribute('aria-expanded','false');active=-1;};
        const choose=option=>{select.value=option.value;select.dispatchEvent(new Event('change',{bubbles:true}));sync();close();};
        const render=(query='')=>{
            const options=available().filter(o=>norm(o.textContent).includes(norm(query)));
            menu.replaceChildren();active=-1;
            if(!options.length){const empty=document.createElement('div');empty.className='fac-search-empty';empty.textContent='No se encontraron opciones';menu.append(empty);}
            options.forEach(option=>{const btn=document.createElement('button');btn.type='button';btn.className='fac-search-option';btn.textContent=option.textContent.trim();btn.setAttribute('role','option');btn.setAttribute('aria-selected',String(option.selected));btn.addEventListener('mousedown',e=>e.preventDefault());btn.addEventListener('click',()=>choose(option));menu.append(btn);});
            menu.hidden=false;wrap.classList.add('is-open');input.setAttribute('aria-expanded','true');
        };
        input.addEventListener('focus',()=>{render('');input.select();});
        input.addEventListener('input',()=>render(input.value));
        toggle.addEventListener('click',()=>{if(menu.hidden){render('');input.focus();}else close();});
        input.addEventListener('keydown',e=>{
            if(e.key==='Escape'){close();sync();return;}
            if(e.key==='Tab'){close();sync();return;}
            const buttons=Array.from(menu.querySelectorAll('button'));
            if(e.key==='ArrowDown'||e.key==='ArrowUp'){e.preventDefault();if(menu.hidden)render('');const items=Array.from(menu.querySelectorAll('button'));if(!items.length)return;active=(active+(e.key==='ArrowDown'?1:-1)+items.length)%items.length;items.forEach((b,i)=>b.classList.toggle('is-active',i===active));items[active].scrollIntoView({block:'nearest'});}
            if(e.key==='Enter'&&!menu.hidden){e.preventDefault();const items=Array.from(menu.querySelectorAll('button'));if(items.length)items[Math.max(active,0)].click();}
        });
        document.addEventListener('pointerdown',e=>{if(!wrap.contains(e.target)){close();sync();}});
        select.addEventListener('change',()=>{sync();close();});
        // La cascada geográfica original cambia option.hidden; sincronizar tras sus eventos.
        ['id_pais','id_departamento','id_municipio_mh'].forEach(id=>{
            const parent=document.getElementById(id);
            if(parent && parent!==select)parent.addEventListener('change',()=>queueMicrotask(()=>{sync();if(!menu.hidden)render('');}));
        });
        sync();
    });
    document.querySelectorAll('[data-fac-file-picker]').forEach(picker=>{
        const input=picker.querySelector('input[type="file"]'),name=picker.querySelector('.fac-file-picker-name');
        if(input&&name)input.addEventListener('change',()=>{name.textContent=input.files && input.files.length?Array.from(input.files).map(f=>f.name).join(', '):'Ningún archivo nuevo seleccionado';});
    });
});
</script>
