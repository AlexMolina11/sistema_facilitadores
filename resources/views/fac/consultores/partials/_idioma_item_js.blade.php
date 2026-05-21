<div class="contacto-card item-idioma">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Idioma</label>
            <select name="idiomas[\${idiomaIndex}][id_idioma]" class="form-select">
                <option value="">Seleccione</option>
                @foreach($catalogos['idiomas'] as $idioma)
                    <option value="{{ $idioma->id_idioma }}">{{ $idioma->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Nivel</label>
            <select name="idiomas[\${idiomaIndex}][id_idioma_nivel]" class="form-select">
                <option value="">Seleccione</option>
                @foreach($catalogos['nivelesIdioma'] as $nivel)
                    <option value="{{ $nivel->id_idioma_nivel }}">{{ $nivel->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Certificado</label>
            <input 
                type="file" 
                name="idiomas[\${idiomaIndex}][certificado]" 
                class="form-control"
                accept=".pdf,.jpg,.jpeg,.png,.webp"
            >
            <div class="form-text">Opcional. Máximo 5 MB.</div>
        </div>

        <div class="col-md-1">
            <button 
                type="button" 
                class="btn btn-outline-danger w-100" 
                onclick="eliminarBloque(this, '.item-idioma')"
            >
                X
            </button>
        </div>
    </div>
</div>