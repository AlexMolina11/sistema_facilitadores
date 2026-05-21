<div class="contacto-card item-idioma">
    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Idioma</label>
            <select name="idiomas[{{ $index }}][id_idioma]" class="form-select">
                <option value="">Seleccione</option>
                @foreach($catalogos['idiomas'] as $idioma)
                    <option 
                        value="{{ $idioma->id_idioma }}"
                        {{ old("idiomas.$index.id_idioma", $idiomaConsultor->id_idioma ?? '') == $idioma->id_idioma ? 'selected' : '' }}
                    >
                        {{ $idioma->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Nivel</label>
            <select name="idiomas[{{ $index }}][id_idioma_nivel]" class="form-select">
                <option value="">Seleccione</option>
                @foreach($catalogos['nivelesIdioma'] as $nivel)
                    <option 
                        value="{{ $nivel->id_idioma_nivel }}"
                        {{ old("idiomas.$index.id_idioma_nivel", $idiomaConsultor->id_idioma_nivel ?? '') == $nivel->id_idioma_nivel ? 'selected' : '' }}
                    >
                        {{ $nivel->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Certificado</label>
            <input 
                type="file" 
                name="idiomas[{{ $index }}][certificado]" 
                class="form-control"
                accept=".pdf,.jpg,.jpeg,.png,.webp"
            >
            <div class="form-text">Opcional. Máximo 5 MB.</div>

            @if(isset($idiomaConsultor) && $idiomaConsultor?->url_certificado)
                <div class="mt-2">
                    <a 
                        href="{{ \Illuminate\Support\Facades\Storage::url($idiomaConsultor->url_certificado) }}" 
                        target="_blank"
                        class="formacion-document-link"
                    >
                        Ver certificado actual
                    </a>
                </div>
            @endif
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