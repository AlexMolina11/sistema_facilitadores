<div class="contacto-card item-experiencia">
    <div class="row g-3">
        <div class="col-md-4">
            <label class="form-label">Empresa</label>
            <input 
                type="text" 
                name="experiencias[\${experienciaIndex}][empresa]" 
                class="form-control"
                maxlength="150"
            >
        </div>

        <div class="col-md-4">
            <label class="form-label">Cargo</label>
            <input 
                type="text" 
                name="experiencias[\${experienciaIndex}][cargo]" 
                class="form-control"
                maxlength="100"
            >
        </div>

        <div class="col-md-4 d-flex align-items-end">
            <div class="form-check form-switch mb-2">
                <input 
                    class="form-check-input" 
                    type="checkbox" 
                    name="experiencias[\${experienciaIndex}][trabajo_actual]" 
                    value="1"
                    id="trabajo_actual_\${experienciaIndex}"
                >
                <label class="form-check-label" for="trabajo_actual_\${experienciaIndex}">
                    Trabajo actual
                </label>
            </div>
        </div>

        <div class="col-md-3">
            <label class="form-label">Fecha desde</label>
            <input 
                type="date" 
                name="experiencias[\${experienciaIndex}][desde]" 
                class="form-control"
            >
        </div>

        <div class="col-md-3">
            <label class="form-label">Fecha hasta</label>
            <input 
                type="date" 
                name="experiencias[\${experienciaIndex}][hasta]" 
                class="form-control"
            >
        </div>

        <div class="col-md-6">
            <label class="form-label">Evidencia laboral</label>
            <input 
                type="file" 
                name="experiencias[\${experienciaIndex}][evidencia]" 
                class="form-control"
                accept=".pdf,.jpg,.jpeg,.png,.webp"
            >
            <div class="form-text">PDF, JPG, JPEG, PNG o WEBP. Máximo 5 MB.</div>
        </div>

        <div class="col-12">
            <label class="form-label">Descripción de funciones o experiencia</label>
            <textarea 
                name="experiencias[\${experienciaIndex}][descripcion]" 
                rows="3" 
                class="form-control"
                maxlength="500"
            ></textarea>
        </div>

        <div class="col-md-4">
            <label class="form-label">Jefe inmediato</label>
            <input 
                type="text" 
                name="experiencias[\${experienciaIndex}][jefe_nombre]" 
                class="form-control"
                maxlength="150"
            >
        </div>

        <div class="col-md-4">
            <label class="form-label">Correo del jefe inmediato</label>
            <input 
                type="email" 
                name="experiencias[\${experienciaIndex}][jefe_email]" 
                class="form-control"
                placeholder="nombre@dominio.com"
                maxlength="120"
            >
        </div>

        <div class="col-md-3">
            <label class="form-label">Teléfono del jefe inmediato</label>
            <input 
                type="text" 
                name="experiencias[\${experienciaIndex}][jefe_telefono]" 
                class="form-control"
                maxlength="20"
            >
        </div>

        <div class="col-md-1 d-flex align-items-end">
            <button 
                type="button" 
                class="btn btn-outline-danger w-100" 
                onclick="eliminarBloque(this, '.item-experiencia')"
            >
                X
            </button>
        </div>
    </div>
</div>