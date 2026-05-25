<?php

namespace App\Modules\Fac\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Consultor;
use App\Modules\Fac\Models\ConsultorDisponibilidad;
use App\Modules\Fac\Models\ConsultorExperienciaLaboral;
use App\Modules\Fac\Models\ConsultorHabilidad;
use App\Modules\Fac\Models\ConsultorIdioma;
use App\Modules\Fac\Models\ConsultorReferencia;
use App\Modules\Fac\Models\ConsultorTipoConsultoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ConsultorExperienciaController extends Controller
{
    public function edit(Consultor $consultor)
    {
        $consultor->load([
            'experienciasLaborales' => fn ($query) => $query->where('activo', true)->orderByDesc('desde'),
            'disponibilidades' => fn ($query) => $query->where('activo', true),
            'habilidades' => fn ($query) => $query->where('activo', true),
            'idiomas' => fn ($query) => $query->where('activo', true),
            'tiposConsultoria' => fn ($query) => $query->where('activo', true),
            'referencias' => fn ($query) => $query->where('activo', true)->orderBy('id_tipo_referencia')->orderBy('nombre'),
        ]);

        $catalogos = $this->catalogos();

        $referenciasPorTipo = $consultor->referencias->groupBy('id_tipo_referencia');

        return view('fac.consultores.experiencia', compact('consultor', 'catalogos', 'referenciasPorTipo'));
    }

    public function storeExperiencia(Request $request, Consultor $consultor)
    {
        $data = $this->validarExperiencia($request);
        $this->validarFechasExperiencia($data);

        if ($request->hasFile('evidencia')) {
            $data['url_evidencia'] = $request->file('evidencia')
                ->store("consultores/{$consultor->id_consultor}/experiencia", 'public');
        }

        $data['id_consultor'] = $consultor->id_consultor;
        $data['trabajo_actual'] = $request->boolean('trabajo_actual');
        $data['hasta'] = $data['trabajo_actual'] ? null : ($data['hasta'] ?? null);
        $data['jefe_email'] = !empty($data['jefe_email']) ? strtolower(trim($data['jefe_email'])) : null;
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
        $this->validarFechasExperiencia($data);

        if ($request->hasFile('evidencia')) {
            $this->eliminarArchivoPublico($experiencia->url_evidencia);

            $data['url_evidencia'] = $request->file('evidencia')
                ->store("consultores/{$consultor->id_consultor}/experiencia", 'public');
        }

        $data['trabajo_actual'] = $request->boolean('trabajo_actual');
        $data['hasta'] = $data['trabajo_actual'] ? null : ($data['hasta'] ?? null);
        $data['jefe_email'] = !empty($data['jefe_email']) ? strtolower(trim($data['jefe_email'])) : null;
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

        $experiencia->update([
            'activo' => false,
            'usuario_elim' => auth()->id(),
        ]);

        $experiencia->delete();

        return redirect()
            ->route('fac.consultores.experiencia.edit', $consultor)
            ->with('success', 'Experiencia laboral eliminada correctamente.');
    }

    public function updateCompetencias(Request $request, Consultor $consultor)
    {
        $request->validate([
            'disponibilidades' => ['nullable', 'array'],
            'disponibilidades.*' => ['integer', 'exists:tbl_tipo_disponibilidad,id_tipo_disponibilidad'],
            'habilidades' => ['nullable', 'array'],
            'habilidades.*' => ['integer', 'exists:tbl_habilidad,id_habilidad'],
            'tipos_consultoria' => ['nullable', 'array'],
            'tipos_consultoria.*' => ['integer', 'exists:tbl_tipo_consultoria,id_tipo_consultoria'],
        ]);

        $disponibilidades = collect($request->input('disponibilidades', []))->filter()->unique()->values();
        $habilidades = collect($request->input('habilidades', []))->filter()->unique()->values();
        $tiposConsultoria = collect($request->input('tipos_consultoria', []))->filter()->unique()->values();

        DB::transaction(function () use ($consultor, $disponibilidades, $habilidades, $tiposConsultoria) {
            $userId = auth()->id();

            $this->sincronizarRelacionSimple(
                ConsultorDisponibilidad::class,
                'id_tipo_disponibilidad',
                $consultor,
                $disponibilidades,
                $userId
            );

            $this->sincronizarRelacionSimple(
                ConsultorHabilidad::class,
                'id_habilidad',
                $consultor,
                $habilidades,
                $userId
            );

            $this->sincronizarRelacionSimple(
                ConsultorTipoConsultoria::class,
                'id_tipo_consultoria',
                $consultor,
                $tiposConsultoria,
                $userId
            );
        });

        return redirect()
            ->route('fac.consultores.experiencia.edit', $consultor)
            ->with('success', 'Disponibilidad, habilidades y tipos de consultoría actualizados correctamente.');
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
            ->route('fac.consultores.experiencia.edit', $consultor)
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
            ->route('fac.consultores.experiencia.edit', $consultor)
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
            ->route('fac.consultores.experiencia.edit', $consultor)
            ->with('success', 'Idioma eliminado correctamente.');
    }

    public function storeReferencia(Request $request, Consultor $consultor)
    {
        $data = $this->validarReferencia($request);
        $this->validarMaximoReferenciasPorTipo($consultor, (int) $data['id_tipo_referencia']);

        $data['id_consultor'] = $consultor->id_consultor;
        $data['nombre'] = trim($data['nombre']);
        $data['correo'] = !empty($data['correo']) ? strtolower(trim($data['correo'])) : null;
        $data['activo'] = true;
        $data['usuario_crea'] = auth()->id();

        ConsultorReferencia::create($data);

        return redirect()
            ->route('fac.consultores.experiencia.edit', $consultor)
            ->with('success', 'Referencia registrada correctamente.');
    }

    public function updateReferencia(Request $request, Consultor $consultor, ConsultorReferencia $referencia)
    {
        $this->validarPertenencia($consultor, $referencia->id_consultor);

        $data = $this->validarReferencia($request);
        $this->validarMaximoReferenciasPorTipo($consultor, (int) $data['id_tipo_referencia'], $referencia->id_referencia);

        $data['nombre'] = trim($data['nombre']);
        $data['correo'] = !empty($data['correo']) ? strtolower(trim($data['correo'])) : null;
        $data['activo'] = true;
        $data['usuario_mod'] = auth()->id();

        $referencia->update($data);

        return redirect()
            ->route('fac.consultores.experiencia.edit', $consultor)
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
            ->route('fac.consultores.experiencia.edit', $consultor)
            ->with('success', 'Referencia eliminada correctamente.');
    }

    public function continuar(Consultor $consultor)
    {
        return redirect()
            ->route('fac.consultores.documentos.edit', $consultor)
            ->with('success', 'Continúa con la sección de documentos.');
    }

    private function catalogos(): array
    {
        return [
            'tiposDisponibilidad' => DB::table('tbl_tipo_disponibilidad')->where('activo', true)->orderBy('nombre')->get(),
            'tiposHabilidad' => DB::table('tbl_tipo_habilidad')->where('activo', true)->orderBy('nombre')->get(),
            'habilidades' => DB::table('tbl_habilidad')->where('activo', true)->orderBy('nombre')->get(),
            'idiomas' => DB::table('tbl_idioma')->where('activo', true)->orderBy('nombre')->get(),
            'nivelesIdioma' => DB::table('tbl_idioma_nivel')->where('activo', true)->orderBy('nombre')->get(),
            'tiposConsultoria' => DB::table('tbl_tipo_consultoria')->where('activo', true)->orderBy('nombre')->get(),
            'tiposReferencia' => DB::table('tbl_tipo_referencia')->where('activo', true)->orderBy('nombre')->get(),
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
        if (
            empty($data['trabajo_actual']) &&
            !empty($data['desde']) &&
            !empty($data['hasta']) &&
            $data['hasta'] < $data['desde']
        ) {
            throw ValidationException::withMessages(['hasta' => 'La fecha hasta no puede ser menor que la fecha desde.']);
        }
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
        ], [
            'id_tipo_referencia.required' => 'El tipo de referencia es obligatorio.',
            'nombre.required' => 'El nombre de la referencia es obligatorio.',
            'telefono.regex' => 'El teléfono de referencia solo puede contener números, espacios, guiones o +.',
            'correo.regex' => 'El correo de referencia debe tener un dominio completo. Ejemplo: nombre@dominio.com',
        ]);
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
                'id_tipo_referencia' => "Solo puedes registrar un máximo de 3 referencias para {$nombreTipo}.",
            ]);
        }
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
