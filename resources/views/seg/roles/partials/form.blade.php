<div class="row g-3">
    <div class="col-md-6">
        <label class="form-label">Nombre del rol</label>
        <input
            type="text"
            name="nombre"
            value="{{ old('nombre', $rol->nombre ?? '') }}"
            class="form-control @error('nombre') is-invalid @enderror"
            required
            maxlength="100"
        >
        @error('nombre')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-md-6">
        <label class="form-label">Estado</label>
        <select
            name="activo"
            class="form-select"
            @disabled(($rol->nombre ?? null) === 'Administrador')
        >
            <option value="1" @selected(old('activo', $rol->activo ?? 1) == 1)>Activo</option>
            <option value="0" @selected(old('activo', $rol->activo ?? 1) == 0)>Inactivo</option>
        </select>

        @if(($rol->nombre ?? null) === 'Administrador')
            <div class="form-text">
                El rol Administrador siempre debe permanecer activo.
            </div>
        @endif
    </div>

    <div class="col-12">
        <label class="form-label">Descripción</label>
        <textarea
            name="descripcion"
            rows="2"
            class="form-control @error('descripcion') is-invalid @enderror"
            maxlength="255"
        >{{ old('descripcion', $rol->descripcion ?? '') }}</textarea>

        @error('descripcion')
            <div class="invalid-feedback">{{ $message }}</div>
        @enderror
    </div>

    <div class="col-12">
        <div class="d-flex justify-content-between align-items-center mb-2 flex-wrap gap-2">
            <label class="form-label mb-0">Permisos asignados</label>
            <span class="text-muted small">
                Selecciona solo los permisos necesarios para este rol.
            </span>
        </div>

        @error('permisos')
            <div class="text-danger small mb-2">{{ $message }}</div>
        @enderror

        <div class="accordion" id="permisosAccordion">
            @foreach($permisosPorModulo as $modulo => $permisos)
                <div class="accordion-item">
                    <h2 class="accordion-header" id="heading{{ $loop->index }}">
                        <button
                            class="accordion-button {{ !$loop->first ? 'collapsed' : '' }}"
                            type="button"
                            data-bs-toggle="collapse"
                            data-bs-target="#collapse{{ $loop->index }}"
                            aria-expanded="{{ $loop->first ? 'true' : 'false' }}"
                            aria-controls="collapse{{ $loop->index }}"
                        >
                            <span class="fw-semibold">
                                Módulo {{ $modulo }}
                            </span>

                            <span class="badge badge-muted-soft ms-2">
                                {{ $permisos->count() }}
                            </span>
                        </button>
                    </h2>

                    <div
                        id="collapse{{ $loop->index }}"
                        class="accordion-collapse collapse {{ $loop->first ? 'show' : '' }}"
                        aria-labelledby="heading{{ $loop->index }}"
                        data-bs-parent="#permisosAccordion"
                    >
                        <div class="accordion-body">
                            <div class="habilidad-grid">
                                @foreach($permisos as $permiso)
                                    <x-ui.checkbox-card
                                        name="permisos[]"
                                        :value="$permiso->id_permiso"
                                        :checked="in_array($permiso->id_permiso, old('permisos', $permisosSeleccionados ?? []))"
                                        :title="$permiso->nombre"
                                        :code="$permiso->codigo"
                                        :description="$permiso->descripcion"
                                    />
                                @endforeach
                            </div>
                        </div>
                    </div>
                </div>
            @endforeach
        </div>
    </div>
</div>