<div class="contacto-card item-referencia">
    <div class="row g-3 align-items-end">
        <div class="col-md-3">
            <label class="form-label">Tipo de referencia</label>
            <select name="referencias[\${referenciaIndex}][id_tipo_referencia]" class="form-select">
                <option value="">Seleccione</option>
                @foreach($catalogos['tiposReferencia'] as $tipo)
                    <option value="{{ $tipo->id_tipo_referencia }}">{{ $tipo->nombre }}</option>
                @endforeach
            </select>
        </div>

        <div class="col-md-3">
            <label class="form-label">Nombre</label>
            <input 
                type="text" 
                name="referencias[\${referenciaIndex}][nombre]" 
                class="form-control"
                maxlength="150"
            >
        </div>

        <div class="col-md-3">
            <label class="form-label">Teléfono</label>
            <input 
                type="text" 
                name="referencias[\${referenciaIndex}][telefono]" 
                class="form-control"
                maxlength="20"
            >
        </div>

        <div class="col-md-3">
            <label class="form-label">Correo</label>
            <input 
                type="email" 
                name="referencias[\${referenciaIndex}][correo]" 
                class="form-control"
                placeholder="nombre@dominio.com"
                maxlength="120"
            >
        </div>

        <div class="col-md-4">
            <label class="form-label">Empresa</label>
            <input 
                type="text" 
                name="referencias[\${referenciaIndex}][empresa]" 
                class="form-control"
                maxlength="150"
            >
        </div>

        <div class="col-md-4">
            <label class="form-label">Cargo</label>
            <input 
                type="text" 
                name="referencias[\${referenciaIndex}][cargo]" 
                class="form-control"
                maxlength="100"
            >
        </div>

        <div class="col-md-4 d-flex align-items-end">
            <button 
                type="button" 
                class="btn btn-outline-danger w-100" 
                onclick="eliminarBloque(this, '.item-referencia')"
            >
                Eliminar referencia
            </button>
        </div>
    </div>
</div>