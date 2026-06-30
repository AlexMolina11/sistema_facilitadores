<?php

namespace App\Modules\Fac\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Consultor;
use App\Modules\Fac\Models\ConsultorDisponibilidad;
use App\Modules\Fac\Models\ConsultorExperienciaLaboral;
use App\Modules\Fac\Models\ConsultorIdioma;
use App\Modules\Fac\Models\ConsultorReferencia;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;
use App\Modules\Fac\Models\ConsultorAreaEspecializacion;
use App\Modules\Fac\Models\ConsultorAreaHabilidad;

class ConsultorExperienciaController extends Controller
{
    public function edit(Consultor $consultor)
    {
        return $this->editExperiencia($consultor);
    }

    public function editExperiencia(Consultor $consultor)
    {
        $consultor->load([
            'experienciasLaborales' => fn ($query) => $query->where('activo', true)->orderByDesc('desde'),
        ]);

        return view('fac.consultores.experiencia', compact('consultor'));
    }

    public function editHabilidades(Consultor $consultor)
    {
        $consultor->load([
            'atestados' => fn ($query) => $query
                ->where('activo', true)
                ->orderByDesc('fecha_fin')
                ->orderByDesc('created_at'),

            'capacitacionesFepade' => fn ($query) => $query
                ->where('activo', true)
                ->orderByDesc('fecha_fin')
                ->orderByDesc('created_at'),

            'areasEspecializacion' => fn ($query) => $query
                ->where('activo', true)
                ->with([
                    'areaEspecializacion',
                    'atestado',
                    'capacitacionFepade',
                    'habilidades.habilidadTecnica.areaEspecializacion',
                ])
                ->orderByDesc('created_at'),
        ]);

        $catalogos = $this->catalogos();

        return view('fac.consultores.habilidades', compact('consultor', 'catalogos'));
    }

    public function editIdiomas(Consultor $consultor)
    {
        $consultor->load([
            'idiomas' => fn ($query) => $query->where('activo', true),
        ]);

        $catalogos = $this->catalogos();

        return view('fac.consultores.idiomas', compact('consultor', 'catalogos'));
    }

    public function editReferencias(Consultor $consultor)
    {
        $consultor->load([
            'referencias' => fn ($query) => $query
                ->where('activo', true)
                ->orderBy('id_tipo_referencia')
                ->orderBy('nombre'),
        ]);

        $catalogos = $this->catalogos();
        $referenciasPorTipo = $consultor->referencias->groupBy('id_tipo_referencia');

        return view('fac.consultores.referencias', compact('consultor', 'catalogos', 'referenciasPorTipo'));
    }

    public function editDisponibilidad(Consultor $consultor)
    {
        $consultor->load([
            'disponibilidades' => fn ($query) => $query->where('activo', true),
        ]);

        $catalogos = $this->catalogos();

        return view('fac.consultores.disponibilidad', compact('consultor', 'catalogos'));
    }

    public function storeExperiencia(Request $request, Consultor $consultor)
    {
        $data = $this->validarExperiencia($request);

        $data['trabajo_actual'] = $request->boolean('trabajo_actual');

        $this->validarFechasExperiencia($data);

        $data = $this->normalizarExperiencia($data);

        if ($request->hasFile('evidencia')) {
            $data['url_evidencia'] = $request->file('evidencia')
                ->store("consultores/{$consultor->id_consultor}/experiencia", 'public');
        }

        $data['id_consultor'] = $consultor->id_consultor;
        $data['activo'] = true;
        $data['usuario_crea'] = auth()->id();

        ConsultorExperienciaLaboral::create($data);

        return redirect()
            ->route('fac.consultores.experiencia.edit', $consultor)
            ->with('success', 'Experiencia laboral registrada correctamente.');
    }

    public function updateExperiencia(Request $request, Consultor $consultor, ConsultorExperienciaLaboral $experiencia)
    {
        $this->validarPertenencia($consultor, $experiencia->id_consultor);

        $data = $this->validarExperiencia($request);

        $data['trabajo_actual'] = $request->boolean('trabajo_actual');

        $this->validarFechasExperiencia($data);

        $data = $this->normalizarExperiencia($data);

        if ($request->hasFile('evidencia')) {
            $this->eliminarArchivoPublico($experiencia->url_evidencia);

            $data['url_evidencia'] = $request->file('evidencia')
                ->store("consultores/{$consultor->id_consultor}/experiencia", 'public');
        }

        $data['activo'] = true;
        $data['usuario_mod'] = auth()->id();

        $experiencia->update($data);

        return redirect()
            ->route('fac.consultores.experiencia.edit', $consultor)
            ->with('success', 'Experiencia laboral actualizada correctamente.');
    }

    public function destroyExperiencia(Consultor $consultor, ConsultorExperienciaLaboral $experiencia)
    {
        $this->validarPertenencia($consultor, $experiencia->id_consultor);

        $this->eliminarArchivoPublico($experiencia->url_evidencia);

        $experiencia->update([
            'activo' => false,
            'usuario_elim' => auth()->id(),
        ]);

        $experiencia->delete();

        return redirect()
            ->route('fac.consultores.experiencia.edit', $consultor)
            ->with('success', 'Experiencia laboral eliminada correctamente.');
    }

    public function storeAreaEspecializacion(Request $request, Consultor $consultor)
    {
        $data = $request->validate([
            'id_area_especializacion' => [
                'required',
                'integer',
                'exists:tbl_area_especializacion,id_area_especializacion',
            ],
            'id_atestado' => [
                'nullable',
                'integer',
                'exists:tbl_consultor_atestado,id_atestado',
            ],
            'id_capacitacion_fepade' => [
                'nullable',
                'integer',
                'exists:tbl_consultor_capacitacion_fepade,id_capacitacion_fepade',
            ],
            'habilidades_tecnicas' => [
                'required',
                'array',
                'min:1',
            ],
            'habilidades_tecnicas.*' => [
                'integer',
                'exists:tbl_habilidad_tecnica,id_habilidad_tecnica',
            ],
        ], [
            'id_area_especializacion.required' => 'Debes seleccionar un área de especialización.',
            'habilidades_tecnicas.required' => 'Debes seleccionar al menos una habilidad técnica aprendida.',
            'habilidades_tecnicas.min' => 'Debes seleccionar al menos una habilidad técnica aprendida.',
        ]);

        if (empty($data['id_atestado']) && empty($data['id_capacitacion_fepade'])) {
            return back()
                ->withErrors(['id_atestado' => 'Debes seleccionar un atestado o una capacitación FEPADE como evidencia.'])
                ->withInput();
        }

        if (!empty($data['id_atestado']) && !empty($data['id_capacitacion_fepade'])) {
            return back()
                ->withErrors(['id_capacitacion_fepade' => 'Selecciona solo una evidencia: atestado o capacitación FEPADE.'])
                ->withInput();
        }

        if (!empty($data['id_atestado'])) {
            $atestadoPertenece = $consultor->atestados()
                ->where('id_atestado', $data['id_atestado'])
                ->where('activo', true)
                ->exists();

            if (!$atestadoPertenece) {
                abort(403, 'El atestado seleccionado no pertenece al consultor.');
            }
        }

        if (!empty($data['id_capacitacion_fepade'])) {
            $capacitacionPertenece = $consultor->capacitacionesFepade()
                ->where('id_capacitacion_fepade', $data['id_capacitacion_fepade'])
                ->where('activo', true)
                ->exists();

            if (!$capacitacionPertenece) {
                abort(403, 'La capacitación FEPADE seleccionada no pertenece al consultor.');
            }
        }

        $habilidadesValidas = DB::table('tbl_habilidad_tecnica')
            ->where('id_area_especializacion', $data['id_area_especializacion'])
            ->where('activo', true)
            ->whereNull('deleted_at')
            ->whereIn('id_habilidad_tecnica', $data['habilidades_tecnicas'])
            ->pluck('id_habilidad_tecnica')
            ->map(fn ($id) => (int) $id)
            ->values();

        if ($habilidadesValidas->count() !== collect($data['habilidades_tecnicas'])->unique()->count()) {
            return back()
                ->withErrors(['habilidades_tecnicas' => 'Todas las habilidades técnicas deben pertenecer al área de especialización seleccionada.'])
                ->withInput();
        }

        DB::transaction(function () use ($consultor, $data, $habilidadesValidas) {
            $registro = ConsultorAreaEspecializacion::withTrashed()->updateOrCreate(
                [
                    'id_consultor' => $consultor->id_consultor,
                    'id_area_especializacion' => $data['id_area_especializacion'],
                    'id_atestado' => $data['id_atestado'] ?? null,
                    'id_capacitacion_fepade' => $data['id_capacitacion_fepade'] ?? null,
                ],
                [
                    'activo' => true,
                    'deleted_at' => null,
                    'usuario_crea' => auth()->id(),
                    'usuario_mod' => auth()->id(),
                    'usuario_elim' => null,
                ]
            );

            $registro->habilidades()
                ->whereNotIn('id_habilidad_tecnica', $habilidadesValidas)
                ->update([
                    'activo' => false,
                    'usuario_elim' => auth()->id(),
                    'deleted_at' => now(),
                ]);

            foreach ($habilidadesValidas as $idHabilidadTecnica) {
                ConsultorAreaHabilidad::withTrashed()->updateOrCreate(
                    [
                        'id_consultor_area' => $registro->id_consultor_area,
                        'id_habilidad_tecnica' => $idHabilidadTecnica,
                    ],
                    [
                        'activo' => true,
                        'deleted_at' => null,
                        'usuario_crea' => auth()->id(),
                        'usuario_mod' => auth()->id(),
                        'usuario_elim' => null,
                    ]
                );
            }
        });

        return redirect()
            ->route('fac.consultores.habilidades.edit', $consultor)
            ->with('success', 'Área de especialización registrada correctamente.');
    }

    public function destroyAreaEspecializacion(Consultor $consultor, ConsultorAreaEspecializacion $consultorArea)
    {
        $this->validarPertenencia($consultor, $consultorArea->id_consultor);

        DB::transaction(function () use ($consultorArea) {
            $consultorArea->habilidades()->update([
                'activo' => false,
                'usuario_elim' => auth()->id(),
                'deleted_at' => now(),
            ]);

            $consultorArea->update([
                'activo' => false,
                'usuario_elim' => auth()->id(),
            ]);

            $consultorArea->delete();
        });

        return redirect()
            ->route('fac.consultores.habilidades.edit', $consultor)
            ->with('success', 'Área de especialización eliminada correctamente.');
    }

    public function updateDisponibilidad(Request $request, Consultor $consultor)
    {
        $data = $request->validate([
            'disponibilidades' => ['nullable', 'array'],
            'disponibilidades.*' => ['integer', 'exists:tbl_tipo_disponibilidad,id_tipo_disponibilidad'],
        ]);

        $idsSeleccionados = collect($data['disponibilidades'] ?? [])
            ->map(fn ($id) => (int) $id)
            ->unique()
            ->values();

        $consultor->disponibilidades()
            ->whereNotIn('id_tipo_disponibilidad', $idsSeleccionados)
            ->update([
                'activo' => false,
                'usuario_elim' => auth()->id(),
                'deleted_at' => now(),
            ]);

        foreach ($idsSeleccionados as $idTipoDisponibilidad) {
            $consultor->disponibilidades()
                ->withTrashed()
                ->updateOrCreate(
                    [
                        'id_consultor' => $consultor->id_consultor,
                        'id_tipo_disponibilidad' => $idTipoDisponibilidad,
                    ],
                    [
                        'activo' => true,
                        'usuario_mod' => auth()->id(),
                        'usuario_elim' => null,
                        'deleted_at' => null,
                    ]
                );
        }

        return redirect()
            ->route('fac.consultores.disponibilidad.edit', $consultor)
            ->with('success', 'Disponibilidad actualizada correctamente.');
    }

    public function storeIdioma(Request $request, Consultor $consultor)
    {
        $data = $this->validarIdioma($request);

        $existenteActivo = ConsultorIdioma::where('id_consultor', $consultor->id_consultor)
            ->where('id_idioma', $data['id_idioma'])
            ->where('activo', true)
            ->exists();

        if ($existenteActivo) {
            return back()->withErrors(['id_idioma' => 'Este idioma ya está registrado para el consultor.'])->withInput();
        }

        if ($request->hasFile('certificado')) {
            $data['url_certificado'] = $request->file('certificado')
                ->store("consultores/{$consultor->id_consultor}/idiomas", 'public');
        }

        ConsultorIdioma::withTrashed()->updateOrCreate(
            [
                'id_consultor' => $consultor->id_consultor,
                'id_idioma' => $data['id_idioma'],
            ],
            [
                'id_idioma_nivel' => $data['id_idioma_nivel'],
                'url_certificado' => $data['url_certificado'] ?? null,
                'activo' => true,
                'deleted_at' => null,
                'usuario_crea' => auth()->id(),
                'usuario_mod' => auth()->id(),
                'usuario_elim' => null,
            ]
        );

        return redirect()
            ->route('fac.consultores.idiomas.edit', $consultor)
            ->with('success', 'Idioma registrado correctamente.');
    }

    public function updateIdioma(Request $request, Consultor $consultor, ConsultorIdioma $idioma)
    {
        $this->validarPertenencia($consultor, $idioma->id_consultor);

        $data = $this->validarIdioma($request);

        $duplicado = ConsultorIdioma::where('id_consultor', $consultor->id_consultor)
            ->where('id_idioma', $data['id_idioma'])
            ->where('id_consultor_idioma', '!=', $idioma->id_consultor_idioma)
            ->where('activo', true)
            ->exists();

        if ($duplicado) {
            return back()->withErrors(['id_idioma' => 'Este idioma ya está registrado para el consultor.'])->withInput();
        }

        if ($request->hasFile('certificado')) {
            $this->eliminarArchivoPublico($idioma->url_certificado);

            $data['url_certificado'] = $request->file('certificado')
                ->store("consultores/{$consultor->id_consultor}/idiomas", 'public');
        }

        $data['activo'] = true;
        $data['usuario_mod'] = auth()->id();

        $idioma->update($data);

        return redirect()
            ->route('fac.consultores.idiomas.edit', $consultor)
            ->with('success', 'Idioma actualizado correctamente.');
    }

    public function destroyIdioma(Consultor $consultor, ConsultorIdioma $idioma)
    {
        $this->validarPertenencia($consultor, $idioma->id_consultor);

        $idioma->update([
            'activo' => false,
            'usuario_elim' => auth()->id(),
        ]);

        $idioma->delete();

        return redirect()
            ->route('fac.consultores.idiomas.edit', $consultor)
            ->with('success', 'Idioma eliminado correctamente.');
    }

    public function storeReferencia(Request $request, Consultor $consultor)
    {
        $data = $this->validarReferencia($request);

        $this->validarCamposPorTipoReferencia($data);
        $this->validarMaximoReferenciasPorTipo($consultor, (int) $data['id_tipo_referencia']);

        $data = $this->normalizarReferencia($data);

        $this->validarReferenciaDuplicada($consultor, $data);

        $data['id_consultor'] = $consultor->id_consultor;
        $data['activo'] = true;
        $data['usuario_crea'] = auth()->id();

        ConsultorReferencia::create($data);

        return redirect()
            ->route('fac.consultores.referencias.edit', $consultor)
            ->with('success', 'Referencia registrada correctamente.');
    }

    public function updateReferencia(Request $request, Consultor $consultor, ConsultorReferencia $referencia)
    {
        $this->validarPertenencia($consultor, $referencia->id_consultor);

        $data = $this->validarReferencia($request);

        $this->validarCamposPorTipoReferencia($data);
        $this->validarMaximoReferenciasPorTipo(
            $consultor,
            (int) $data['id_tipo_referencia'],
            $referencia->id_referencia
        );

        $data = $this->normalizarReferencia($data);

        $this->validarReferenciaDuplicada($consultor, $data, $referencia->id_referencia);

        $data['activo'] = true;
        $data['usuario_mod'] = auth()->id();

        $referencia->update($data);

        return redirect()
            ->route('fac.consultores.referencias.edit', $consultor)
            ->with('success', 'Referencia actualizada correctamente.');
    }

    public function destroyReferencia(Consultor $consultor, ConsultorReferencia $referencia)
    {
        $this->validarPertenencia($consultor, $referencia->id_consultor);

        $referencia->update([
            'activo' => false,
            'usuario_elim' => auth()->id(),
        ]);

        $referencia->delete();

        return redirect()
            ->route('fac.consultores.referencias.edit', $consultor)
            ->with('success', 'Referencia eliminada correctamente.');
    }

    public function continuar(Consultor $consultor)
    {
        return redirect()
            ->route('fac.consultores.formacion.edit', $consultor)
            ->with('success', 'Experiencia guardada correctamente. Continúa con títulos académicos.');
    }

    public function continuarHabilidades(Request $request, Consultor $consultor)
    {
        return redirect()
            ->route('fac.consultores.idiomas.edit', $consultor)
            ->with('success', 'Áreas de especialización guardadas correctamente. Continúa con idiomas.');
    }

    public function continuarIdiomas(Consultor $consultor)
    {
        return redirect()
            ->route('fac.consultores.referencias.edit', $consultor)
            ->with('success', 'Continúa con referencias.');
    }

    public function continuarReferencias(Consultor $consultor)
    {
        return redirect()
            ->route('fac.consultores.disponibilidad.edit', $consultor)
            ->with('success', 'Continúa con disponibilidad.');
    }

    public function continuarDisponibilidad(Request $request, Consultor $consultor)
    {
        $data = $request->validate([
            'id_tipo_disponibilidad' => [
                'required',
                'integer',
                'exists:tbl_tipo_disponibilidad,id_tipo_disponibilidad',
            ],
        ], [
            'id_tipo_disponibilidad.required' => 'Debes seleccionar una situación de disponibilidad.',
        ]);

        DB::transaction(function () use ($consultor, $data) {
            ConsultorDisponibilidad::where('id_consultor', $consultor->id_consultor)
                ->whereNull('deleted_at')
                ->get()
                ->each(function ($disponibilidad) {
                    $disponibilidad->update([
                        'activo' => false,
                        'usuario_elim' => auth()->id(),
                    ]);

                    $disponibilidad->delete();
                });

            ConsultorDisponibilidad::withTrashed()->updateOrCreate(
                [
                    'id_consultor' => $consultor->id_consultor,
                    'id_tipo_disponibilidad' => $data['id_tipo_disponibilidad'],
                ],
                [
                    'activo' => true,
                    'deleted_at' => null,
                    'usuario_crea' => auth()->id(),
                    'usuario_mod' => auth()->id(),
                    'usuario_elim' => null,
                ]
            );
        });

        return redirect()
            ->route('fac.consultores.show', $consultor)
            ->with('success', 'Disponibilidad actualizada correctamente.');
    }

    private function catalogos(): array
    {
        return [
            'tiposDisponibilidad' => DB::table('tbl_tipo_disponibilidad')->where('activo', true)->orderBy('nombre')->get(),
            'areasEspecializacion' => \App\Modules\Fac\Models\AreaEspecializacion::where('activo', true)->orderBy('nombre')->get(),
            'habilidadesTecnicas' => \App\Modules\Fac\Models\HabilidadTecnica::with('areaEspecializacion')->where('activo', true)->orderBy('nombre')->get(),
            'idiomas' => DB::table('tbl_idioma')->where('activo', true)->orderBy('nombre')->get(),
            'nivelesIdioma' => DB::table('tbl_idioma_nivel')->where('activo', true)->orderBy('nombre')->get(),
            'tiposReferencia' => DB::table('tbl_tipo_referencia')->where('activo', true)->orderBy('nombre')->get(),
            'tiposRelacion' => DB::table('tbl_tipo_relacion')->where('activo', true)->orderBy('nombre')->get(),
        ];
    }

    private function validarExperiencia(Request $request): array
    {
        return $request->validate([
            'empresa' => ['required', 'string', 'max:150'],
            'cargo' => ['required', 'string', 'max:100'],
            'descripcion' => ['nullable', 'string', 'max:500'],
            'desde' => ['nullable', 'date'],
            'hasta' => ['nullable', 'date'],
            'trabajo_actual' => ['nullable', 'boolean'],
            'jefe_nombre' => ['nullable', 'string', 'max:150'],
            'jefe_email' => ['nullable', 'string', 'max:120', 'regex:/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/'],
            'jefe_telefono' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]{7,20}$/'],
            'evidencia' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'empresa.required' => 'La empresa es obligatoria.',
            'cargo.required' => 'El cargo es obligatorio.',
            'jefe_email.regex' => 'El correo del jefe inmediato debe tener un dominio completo. Ejemplo: nombre@dominio.com',
            'jefe_telefono.regex' => 'El teléfono del jefe inmediato solo puede contener números, espacios, guiones o +.',
            'evidencia.mimes' => 'La evidencia laboral debe ser PDF, JPG, JPEG, PNG o WEBP.',
            'evidencia.max' => 'La evidencia laboral no debe superar los 5 MB.',
        ]);
    }

    private function validarFechasExperiencia(array $data): void
    {
        if (!empty($data['trabajo_actual'])) {
            return;
        }

        if (
            !empty($data['desde']) &&
            !empty($data['hasta']) &&
            $data['hasta'] < $data['desde']
        ) {
            throw ValidationException::withMessages([
                'hasta' => 'La fecha hasta no puede ser menor que la fecha desde.',
            ]);
        }
    }

    private function normalizarExperiencia(array $data): array
    {
        $data['empresa'] = trim($data['empresa']);
        $data['cargo'] = trim($data['cargo']);
        $data['descripcion'] = !empty($data['descripcion']) ? trim($data['descripcion']) : null;

        $data['jefe_nombre'] = !empty($data['jefe_nombre']) ? trim($data['jefe_nombre']) : null;
        $data['jefe_email'] = !empty($data['jefe_email']) ? strtolower(trim($data['jefe_email'])) : null;
        $data['jefe_telefono'] = !empty($data['jefe_telefono']) ? trim($data['jefe_telefono']) : null;

        $data['hasta'] = !empty($data['trabajo_actual'])
            ? null
            : ($data['hasta'] ?? null);

        return $data;
    }

    private function validarIdioma(Request $request): array
    {
        return $request->validate([
            'id_idioma' => ['required', 'integer', 'exists:tbl_idioma,id_idioma'],
            'id_idioma_nivel' => ['required', 'integer', 'exists:tbl_idioma_nivel,id_idioma_nivel'],
            'certificado' => ['nullable', 'file', 'mimes:pdf,jpg,jpeg,png,webp', 'max:5120'],
        ], [
            'id_idioma.required' => 'El idioma es obligatorio.',
            'id_idioma_nivel.required' => 'El nivel del idioma es obligatorio.',
            'certificado.mimes' => 'El certificado de idioma debe ser PDF, JPG, JPEG, PNG o WEBP.',
            'certificado.max' => 'El certificado de idioma no debe superar los 5 MB.',
        ]);
    }

    private function validarReferencia(Request $request): array
    {
        return $request->validate([
            'id_tipo_referencia' => ['required', 'integer', 'exists:tbl_tipo_referencia,id_tipo_referencia'],
            'nombre' => ['required', 'string', 'max:150'],
            'telefono' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]{7,20}$/'],
            'correo' => ['nullable', 'string', 'max:120', 'regex:/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/'],
            'empresa' => ['nullable', 'string', 'max:150'],
            'cargo' => ['nullable', 'string', 'max:100'],
            'id_tipo_relacion' => ['nullable', 'integer', 'exists:tbl_tipo_relacion,id_tipo_relacion'],
        ], [
            'id_tipo_referencia.required' => 'El tipo de referencia es obligatorio.',
            'nombre.required' => 'El nombre de la referencia es obligatorio.',
            'telefono.regex' => 'El teléfono de referencia solo puede contener números, espacios, guiones o +.',
            'correo.regex' => 'El correo de referencia debe tener un dominio completo. Ejemplo: nombre@dominio.com',
        ]);
    }

    private function validarCamposPorTipoReferencia(array $data): void
    {
        $tipo = DB::table('tbl_tipo_referencia')
            ->where('id_tipo_referencia', $data['id_tipo_referencia'])
            ->first();

        $nombreTipo = strtolower($tipo->nombre ?? '');

        if (str_contains($nombreTipo, 'personal')) {
            if (empty($data['id_tipo_relacion'])) {
                throw ValidationException::withMessages([
                    'id_tipo_relacion' => 'Debes seleccionar la relación para una referencia personal.',
                ]);
            }
        }

        if (str_contains($nombreTipo, 'laboral') || str_contains($nombreTipo, 'profesional')) {
            if (empty($data['cargo'])) {
                throw ValidationException::withMessages(['cargo' => 'El cargo es obligatorio para una referencia profesional.']);
            }

            if (empty($data['empresa'])) {
                throw ValidationException::withMessages(['empresa' => 'La empresa u organización es obligatoria para una referencia profesional.']);
            }
        }
    }

    private function validarMaximoReferenciasPorTipo(Consultor $consultor, int $idTipoReferencia, ?int $idReferenciaIgnorar = null): void
    {
        $query = ConsultorReferencia::where('id_consultor', $consultor->id_consultor)
            ->where('id_tipo_referencia', $idTipoReferencia)
            ->where('activo', true);

        if ($idReferenciaIgnorar) {
            $query->where('id_referencia', '!=', $idReferenciaIgnorar);
        }

        if ($query->count() >= 3) {
            $tipo = DB::table('tbl_tipo_referencia')->where('id_tipo_referencia', $idTipoReferencia)->first();
            $nombreTipo = $tipo->nombre ?? 'este tipo';

            throw ValidationException::withMessages([
                'id_tipo_referencia' => "Ya alcanzaste el máximo permitido: 3 referencias para {$nombreTipo}.",
            ]);
        }
    }

    private function validarReferenciaDuplicada(Consultor $consultor, array $data, ?int $idReferenciaIgnorar = null): void
    {
        $query = ConsultorReferencia::where('id_consultor', $consultor->id_consultor)
            ->where('activo', true)
            ->where(function ($q) use ($data) {
                $q->whereRaw('LOWER(TRIM(nombre)) = ?', [strtolower(trim($data['nombre']))]);

                if (!empty($data['telefono'])) {
                    $telefonoNormalizado = preg_replace('/[^0-9]/', '', $data['telefono']);

                    $q->orWhereRaw(
                        "REPLACE(REPLACE(REPLACE(REPLACE(telefono, '-', ''), ' ', ''), '+', ''), '.', '') = ?",
                        [$telefonoNormalizado]
                    );
                }

                if (!empty($data['correo'])) {
                    $q->orWhereRaw('LOWER(TRIM(correo)) = ?', [strtolower(trim($data['correo']))]);
                }
            });

        if ($idReferenciaIgnorar) {
            $query->where('id_referencia', '!=', $idReferenciaIgnorar);
        }

        if ($query->exists()) {
            throw ValidationException::withMessages([
                'nombre' => 'Ya existe una referencia registrada con el mismo nombre, teléfono o correo electrónico.',
            ]);
        }
    }

    private function normalizarReferencia(array $data): array
    {
        $tipo = DB::table('tbl_tipo_referencia')
            ->where('id_tipo_referencia', $data['id_tipo_referencia'])
            ->first();

        $nombreTipo = strtolower($tipo->nombre ?? '');

        $data['nombre'] = trim($data['nombre']);
        $data['telefono'] = !empty($data['telefono']) ? trim($data['telefono']) : null;
        $data['correo'] = !empty($data['correo']) ? strtolower(trim($data['correo'])) : null;
        $data['empresa'] = !empty($data['empresa']) ? trim($data['empresa']) : null;
        $data['cargo'] = !empty($data['cargo']) ? trim($data['cargo']) : null;
        $data['id_tipo_relacion'] = !empty($data['id_tipo_relacion']) ? $data['id_tipo_relacion'] : null;

        if (str_contains($nombreTipo, 'personal')) {
            $data['empresa'] = null;
            $data['cargo'] = null;
        }

        if (str_contains($nombreTipo, 'laboral') || str_contains($nombreTipo, 'profesional')) {
            $data['id_tipo_relacion'] = null;
        }

        return $data;
    }

    private function sincronizarRelacionSimple(string $modelo, string $campo, Consultor $consultor, $idsSeleccionados, ?int $userId): void
    {
        $idsSeleccionados = collect($idsSeleccionados)->filter()->unique()->values();

        $modelo::where('id_consultor', $consultor->id_consultor)
            ->whereNotIn($campo, $idsSeleccionados->all())
            ->whereNull('deleted_at')
            ->get()
            ->each(function ($item) use ($userId) {
                $item->update([
                    'activo' => false,
                    'usuario_elim' => $userId,
                ]);

                $item->delete();
            });

        foreach ($idsSeleccionados as $id) {
            $modelo::withTrashed()->updateOrCreate(
                [
                    'id_consultor' => $consultor->id_consultor,
                    $campo => $id,
                ],
                [
                    'activo' => true,
                    'deleted_at' => null,
                    'usuario_crea' => $userId,
                    'usuario_mod' => $userId,
                    'usuario_elim' => null,
                ]
            );
        }
    }

    private function validarPertenencia(Consultor $consultor, int $idConsultorDelRegistro): void
    {
        abort_if($consultor->id_consultor !== $idConsultorDelRegistro, 404);
    }

    private function eliminarArchivoPublico(?string $ruta): void
    {
        if ($ruta && Storage::disk('public')->exists($ruta)) {
            Storage::disk('public')->delete($ruta);
        }
    }
}
