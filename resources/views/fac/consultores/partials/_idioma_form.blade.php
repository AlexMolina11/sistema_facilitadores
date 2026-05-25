@php
    $esEdicion = isset($idiomaConsultor) && $idiomaConsultor;
@endphp

<form
    method="POST"
    action="{{ $esEdicion
        ? route('fac.consultores.experiencia.idiomas.update', [$consultor, $idiomaConsultor])
        : route('fac.consultores.experiencia.idiomas.store', $consultor)
    }}"
    enctype="multipart/form-data"
    class="border rounded p-3 mb-3 bg-light"
>
    @csrf

    @if($esEdicion)
        @method('PUT')
    @endif

    <div class="row g-3 align-items-end">
        <div class="col-md-4">
            <label class="form-label">Idioma <span class="text-danger">*</span></label>
            <select name="id_idioma" class="form-select">
                <option value="">Seleccione</option>
                @foreach($catalogos['idiomas'] as $idioma)
                    <option value="{{ $idioma->id_idioma }}" {{ old('id_idioma', $idiomaConsultor->id_idioma ?? '') == $idioma->id_idioma ? 'selected' : '' }}>
                        {{ $idioma->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Nivel <span class="text-danger">*</span></label>
            <select name="id_idioma_nivel" class="form-select">
                <option value="">Seleccione</option>
                @foreach($catalogos['nivelesIdioma'] as $nivel)
                    <option value="{{ $nivel->id_idioma_nivel }}" {{ old('id_idioma_nivel', $idiomaConsultor->id_idioma_nivel ?? '') == $nivel->id_idioma_nivel ? 'selected' : '' }}>
                        {{ $nivel->nombre }}
                    </option>
                @endforeach
            </select>
        </div>

        <div class="col-md-4">
            <label class="form-label">Certificado</label>
            <input type="file" name="certificado" class="form-control" accept=".pdf,.jpg,.jpeg,.png,.webp">
            <div class="form-text">Opcional. Máximo 5 MB.</div>

            @if($esEdicion && $idiomaConsultor->url_certificado)
                <div class="mt-2">
                    <a href="{{ \Illuminate\Support\Facades\Storage::url($idiomaConsultor->url_certificado) }}" target="_blank">Ver certificado actual</a>
                </div>
            @endif
        </div>

        <div class="col-12 d-flex justify-content-end gap-2">
            <button type="button" class="btn btn-outline-secondary" onclick="cerrarFormulariosExperiencia()">Cancelar</button>
            <button type="submit" class="btn btn-fepade">{{ $esEdicion ? 'Actualizar idioma' : 'Guardar idioma' }}</button>
        </div>
    </div>
</form>
