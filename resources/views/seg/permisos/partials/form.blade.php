<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Código</label>
        <input
            type="text"
            name="codigo"
            value="{{ old('codigo', $permiso->codigo ?? '') }}"
            class="form-control @error('codigo') is-invalid @enderror"
            placeholder="seg.roles.gestionar"
            required
            maxlength="100"
            pattern="^[a-z0-9]+(\.[a-z0-9]+)*$"
        >

        <div class="form-text">
            Formato recomendado: modulo.recurso.accion. Ejemplo: seg.roles.gestionar.
        </div>

        @error('codigo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Módulo</label>
        <input
            type="text"
            name="modulo"
            value="{{ old('modulo', $permiso->modulo ?? '') }}"
            class="form-control @error('modulo') is-invalid @enderror"
            placeholder="SEG"
            maxlength="50"
        >

        <div class="form-text">
            Ejemplo: SEG, FAC o CAT.
        </div>

        @error('modulo')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-3">
        <label class="form-label">Estado</label>
        <select name="activo" class="form-select">
            <option value="1" @selected(old('activo', $permiso->activo ?? 1) == 1)>Activo</option>
            <option value="0" @selected(old('activo', $permiso->activo ?? 1) == 0)>Inactivo</option>
        </select>
    </div>

    <div class="col-md-6">
        <label class="form-label">Nombre</label>
        <input
            type="text"
            name="nombre"
            value="{{ old('nombre', $permiso->nombre ?? '') }}"
            class="form-control @error('nombre') is-invalid @enderror"
            required
            maxlength="100"
            placeholder="Gestionar roles"
        >

        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Descripción</label>
        <input
            type="text"
            name="descripcion"
            value="{{ old('descripcion', $permiso->descripcion ?? '') }}"
            class="form-control @error('descripcion') is-invalid @enderror"
            maxlength="255"
            placeholder="Permite crear, editar y eliminar roles."
        >

        @error('descripcion')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>
</div>