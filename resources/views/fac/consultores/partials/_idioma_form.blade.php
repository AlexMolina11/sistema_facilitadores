@php
    $esEdicion = isset($idiomaConsultor) && $idiomaConsultor;
    $formId = $esEdicion ? 'editar-idioma-'.$idiomaConsultor->id_consultor_idioma : 'crear';
    $tieneErrores = ($idiomaFormError ?? null) === $formId;
@endphp
<form method="POST" enctype="multipart/form-data"
    action="{{ $esEdicion ? route('fac.consultores.idiomas.update', [$consultor, $idiomaConsultor]) : route('fac.consultores.idiomas.store', $consultor) }}"
    class="idioma-guide">
    @csrf
    @if($esEdicion) @method('PUT') @endif
    <input type="hidden" name="_idioma_form_id" value="{{ $formId }}">
    <div class="mb-3">
        <h5 class="mb-1">{{ $esEdicion ? 'Editar idioma' : 'Nuevo idioma' }}</h5>
        <p class="text-muted small mb-0">Selecciona el idioma, indica tu nivel y adjunta un certificado si dispones de él.</p>
    </div>
    @if($tieneErrores && $errors->any())
        <div class="alert alert-danger py-2 small" role="alert">Revisa los campos señalados antes de guardar.</div>
    @endif
    <div class="row g-3">
        <div class="col-md-6">
            <label class="form-label" for="idioma-{{ $formId }}">Idioma <span class="text-danger">*</span></label>
            <div class="idioma-combobox" data-idioma-combobox>
                <div class="idioma-combobox-control {{ $tieneErrores && $errors->has('id_idioma') ? 'is-invalid' : '' }}">
                    <i class="fa-solid fa-magnifying-glass idioma-combobox-icon" aria-hidden="true"></i>
                    <input type="text" id="buscar-idioma-{{ $formId }}" class="idioma-combobox-input"
                        autocomplete="off" role="combobox" aria-autocomplete="list" aria-expanded="false"
                        aria-controls="opciones-idioma-{{ $formId }}" aria-label="Buscar y seleccionar idioma"
                        placeholder="Buscar o seleccionar idioma…">
                    <button type="button" class="idioma-combobox-toggle" aria-label="Mostrar idiomas" tabindex="-1"><i class="fa-solid fa-chevron-down"></i></button>
                </div>
                <div class="idioma-combobox-menu" id="opciones-idioma-{{ $formId }}" role="listbox" hidden></div>
                <select id="idioma-{{ $formId }}" name="id_idioma" required
                    class="idioma-native-select {{ $tieneErrores && $errors->has('id_idioma') ? 'is-invalid' : '' }}" tabindex="-1" aria-hidden="true">
                    <option value="">Selecciona un idioma</option>
                    @foreach($catalogos['idiomas'] as $idioma)
                        <option value="{{ $idioma->id_idioma }}" @selected(($tieneErrores ? old('id_idioma') : ($idiomaConsultor->id_idioma ?? '')) == $idioma->id_idioma)>{{ $idioma->nombre }}</option>
                    @endforeach
                </select>
            </div>
            <div class="form-text">Escribe para buscar o despliega la lista de idiomas.</div>
            @if($tieneErrores) @error('id_idioma') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror @endif
        </div>
        <div class="col-md-6">
            <label class="form-label" for="nivel-{{ $formId }}">Nivel de dominio <span class="text-danger">*</span></label>
            <div class="idioma-combobox" data-idioma-combobox data-empty-message="No se encontraron niveles" data-required-message="Selecciona un nivel de dominio de la lista.">
                <div class="idioma-combobox-control {{ $tieneErrores && $errors->has('id_idioma_nivel') ? 'is-invalid' : '' }}">
                    <i class="fa-solid fa-magnifying-glass idioma-combobox-icon" aria-hidden="true"></i>
                    <input type="text" id="buscar-nivel-{{ $formId }}" class="idioma-combobox-input"
                        autocomplete="off" role="combobox" aria-autocomplete="list" aria-expanded="false"
                        aria-controls="opciones-nivel-{{ $formId }}" aria-label="Buscar y seleccionar nivel de dominio"
                        placeholder="Buscar o seleccionar nivel…">
                    <button type="button" class="idioma-combobox-toggle" aria-label="Mostrar niveles" tabindex="-1"><i class="fa-solid fa-chevron-down"></i></button>
                </div>
                <div class="idioma-combobox-menu" id="opciones-nivel-{{ $formId }}" role="listbox" hidden></div>
                <select id="nivel-{{ $formId }}" name="id_idioma_nivel" required
                    class="idioma-native-select {{ $tieneErrores && $errors->has('id_idioma_nivel') ? 'is-invalid' : '' }}" tabindex="-1" aria-hidden="true">
                    <option value="">Selecciona un nivel</option>
                    @foreach($catalogos['nivelesIdioma'] as $nivel)
                        <option value="{{ $nivel->id_idioma_nivel }}" @selected(($tieneErrores ? old('id_idioma_nivel') : ($idiomaConsultor->id_idioma_nivel ?? '')) == $nivel->id_idioma_nivel)>{{ $nivel->nombre }}</option>
                    @endforeach
                </select>
            </div>
            @if($tieneErrores) @error('id_idioma_nivel') <div class="invalid-feedback d-block">{{ $message }}</div> @enderror @endif
        </div>
        <div class="col-12">
            <div class="idioma-file-box">
                <label class="form-label d-block" for="certificado-{{ $formId }}">Certificado del idioma <span class="text-muted fw-normal">(opcional)</span></label>
                <div class="d-flex align-items-center gap-3 flex-wrap">
                    <input type="file" id="certificado-{{ $formId }}" name="certificado" class="{{ $tieneErrores && $errors->has('certificado') ? 'is-invalid' : '' }}" accept=".pdf,.jpg,.jpeg,.png,.webp">
                    <label for="certificado-{{ $formId }}" class="btn btn-outline-secondary idioma-file-button mb-0"><i class="fa-solid fa-cloud-arrow-up"></i> Elegir documento</label>
                    <span class="idioma-file-name" data-idioma-file-label aria-live="polite">Ningún archivo nuevo seleccionado</span>
                </div>
                <div class="form-text">PDF, JPG, JPEG, PNG o WEBP. Máximo 5 MB.</div>
                @if($tieneErrores) @error('certificado') <div class="text-danger small mt-1">{{ $message }}</div> @enderror @endif
                @if($esEdicion && $idiomaConsultor->url_certificado)
                    <div class="mt-2">
                        <a href="{{ Storage::url($idiomaConsultor->url_certificado) }}" target="_blank" rel="noopener noreferrer"><i class="fa-regular fa-file-lines me-1"></i> Ver certificado actual</a>
                        <div class="form-text">Si no seleccionas otro archivo, se conservará el certificado actual.</div>
                    </div>
                @endif
            </div>
        </div>
    </div>
    <div class="border-top mt-3 pt-3 d-flex flex-wrap justify-content-end gap-2">
        <button type="button" class="btn btn-outline-secondary" onclick="cerrarFormulariosPerfil()">Cancelar</button>
        <button type="submit" class="btn btn-fepade"><i class="fa-solid fa-floppy-disk me-1"></i> {{ $esEdicion ? 'Actualizar idioma' : 'Guardar idioma' }}</button>
    </div>
</form>
