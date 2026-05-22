<?php

namespace App\Modules\Fac\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Consultor;
use App\Modules\Fac\Models\ConsultorDisponibilidad;
use App\Modules\Fac\Models\ConsultorExperienciaLaboral;
use App\Modules\Fac\Models\ConsultorHabilidad;
use App\Modules\Fac\Models\ConsultorIdioma;
use App\Modules\Fac\Models\ConsultorTipoConsultoria;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use App\Modules\Fac\Models\ConsultorReferencia;

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
            'referencias' => fn ($query) => $query->where('activo', true),
        ]);

        $catalogos = [
            'tiposDisponibilidad' => DB::table('tbl_tipo_disponibilidad')
                ->where('activo', true)
                ->orderBy('nombre')
                ->get(),

            'tiposHabilidad' => DB::table('tbl_tipo_habilidad')
                ->where('activo', true)
                ->orderBy('nombre')
                ->get(),

            'habilidades' => DB::table('tbl_habilidad')
                ->where('activo', true)
                ->orderBy('nombre')
                ->get(),

            'idiomas' => DB::table('tbl_idioma')
                ->where('activo', true)
                ->orderBy('nombre')
                ->get(),

            'nivelesIdioma' => DB::table('tbl_idioma_nivel')
                ->where('activo', true)
                ->orderBy('nombre')
                ->get(),

            'tiposConsultoria' => DB::table('tbl_tipo_consultoria')
                ->where('activo', true)
                ->orderBy('nombre')
                ->get(),

            'tiposReferencia' => DB::table('tbl_tipo_referencia')
                ->where('activo', true)
                ->orderBy('nombre')
                ->get(),
        ];

        return view('fac.consultores.experiencia', compact('consultor', 'catalogos'));
    }

    public function update(Request $request, Consultor $consultor)
    {
        $request->validate([
            'experiencias' => ['nullable', 'array'],
            'experiencias.*.empresa' => ['nullable', 'string', 'max:150'],
            'experiencias.*.cargo' => ['nullable', 'string', 'max:100'],
            'experiencias.*.descripcion' => ['nullable', 'string', 'max:500'],
            'experiencias.*.desde' => ['nullable', 'date'],
            'experiencias.*.hasta' => ['nullable', 'date'],
            'experiencias.*.trabajo_actual' => ['nullable', 'boolean'],
            'experiencias.*.jefe_nombre' => ['nullable', 'string', 'max:150'],
            'experiencias.*.jefe_email' => [
                'nullable',
                'string',
                'max:120',
                'regex:/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/',
            ],
            'experiencias.*.jefe_telefono' => [
                'nullable',
                'string',
                'max:20',
                'regex:/^[0-9+\-\s]{7,20}$/',
            ],
            'experiencias.*.evidencia' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:5120',
            ],

            'disponibilidades' => ['nullable', 'array'],
            'disponibilidades.*' => ['integer', 'exists:tbl_tipo_disponibilidad,id_tipo_disponibilidad'],

            'habilidades' => ['nullable', 'array'],
            'habilidades.*' => ['integer', 'exists:tbl_habilidad,id_habilidad'],

            'idiomas' => ['nullable', 'array'],
            'idiomas.*.id_idioma' => ['nullable', 'integer', 'exists:tbl_idioma,id_idioma'],
            'idiomas.*.id_idioma_nivel' => ['nullable', 'integer', 'exists:tbl_idioma_nivel,id_idioma_nivel'],
            'idiomas.*.certificado' => [
                'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:5120',
            ],

            'tipos_consultoria' => ['nullable', 'array'],
            'tipos_consultoria.*' => ['integer', 'exists:tbl_tipo_consultoria,id_tipo_consultoria'],

            'referencias' => ['nullable', 'array'],
            'referencias.*.id_tipo_referencia' => ['nullable', 'integer', 'exists:tbl_tipo_referencia,id_tipo_referencia'],
            'referencias.*.nombre' => ['nullable', 'string', 'max:150'],
            'referencias.*.telefono' => ['nullable', 'string', 'max:20', 'regex:/^[0-9+\-\s]{7,20}$/'],
            'referencias.*.correo' => [
                'nullable',
                'string',
                'max:120',
                'regex:/^[A-Za-z0-9._%+\-]+@[A-Za-z0-9.\-]+\.[A-Za-z]{2,}$/',
            ],
            'referencias.*.empresa' => ['nullable', 'string', 'max:150'],
            'referencias.*.cargo' => ['nullable', 'string', 'max:100'],

        ], [

            'experiencias.*.jefe_email.regex' => 'El correo del jefe inmediato debe tener un dominio completo. Ejemplo: nombre@dominio.com',
            'experiencias.*.jefe_telefono.regex' => 'El teléfono del jefe inmediato solo puede contener números, espacios, guiones o +.',
            'experiencias.*.evidencia.mimes' => 'La evidencia laboral debe ser PDF, JPG, JPEG, PNG o WEBP.',
            'experiencias.*.evidencia.max' => 'La evidencia laboral no debe superar los 5 MB.',
            'idiomas.*.certificado.mimes' => 'El certificado de idioma debe ser PDF, JPG, JPEG, PNG o WEBP.',
            'idiomas.*.certificado.max' => 'El certificado de idioma no debe superar los 5 MB.',
            'referencias.*.telefono.regex' => 'El teléfono de referencia solo puede contener números, espacios, guiones o +.',
            'referencias.*.correo.regex' => 'El correo de referencia debe tener un dominio completo. Ejemplo: nombre@dominio.com',

        ]);

        foreach ($request->input('experiencias', []) as $index => $experiencia) {
            if (
                empty($experiencia['trabajo_actual']) &&
                !empty($experiencia['desde']) &&
                !empty($experiencia['hasta']) &&
                $experiencia['hasta'] < $experiencia['desde']
            ) {
                return back()
                    ->withErrors([
                        "experiencias.$index.hasta" => 'La fecha hasta no puede ser menor que la fecha desde.',
                    ])
                    ->withInput();
            }
        }

        $habilidades = collect($request->input('habilidades', []))->filter()->values();
        if ($habilidades->duplicates()->isNotEmpty()) {
            return back()->withErrors(['habilidades' => 'No puedes seleccionar habilidades duplicadas.'])->withInput();
        }

        $disponibilidades = collect($request->input('disponibilidades', []))->filter()->values();
        if ($disponibilidades->duplicates()->isNotEmpty()) {
            return back()->withErrors(['disponibilidades' => 'No puedes seleccionar disponibilidades duplicadas.'])->withInput();
        }

        $tiposConsultoria = collect($request->input('tipos_consultoria', []))->filter()->values();
        if ($tiposConsultoria->duplicates()->isNotEmpty()) {
            return back()->withErrors(['tipos_consultoria' => 'No puedes seleccionar tipos de consultoría duplicados.'])->withInput();
        }

        $idiomas = collect($request->input('idiomas', []))
            ->filter(fn ($item) => !empty($item['id_idioma']))
            ->values();

        if ($idiomas->pluck('id_idioma')->duplicates()->isNotEmpty()) {
            return back()->withErrors(['idiomas' => 'No puedes seleccionar idiomas duplicados.'])->withInput();
        }

        $referencias = collect($request->input('referencias', []))
            ->filter(fn ($item) => !empty($item['id_tipo_referencia']) && !empty($item['nombre']))
            ->values();

        $tiposReferencia = DB::table('tbl_tipo_referencia')
            ->where('activo', true)
            ->get()
            ->keyBy('id_tipo_referencia');

        $personales = $referencias->filter(function ($item) use ($tiposReferencia) {
            $tipo = $tiposReferencia->get($item['id_tipo_referencia']);
            return $tipo && strtolower($tipo->nombre) === 'personal';
        });

        $laborales = $referencias->filter(function ($item) use ($tiposReferencia) {
            $tipo = $tiposReferencia->get($item['id_tipo_referencia']);
            return $tipo && strtolower($tipo->nombre) === 'laboral';
        });

        if ($personales->count() > 3) {
            return back()
                ->withErrors(['referencias' => 'Solo puedes registrar un máximo de 3 referencias personales.'])
                ->withInput();
        }

        if ($laborales->count() > 3) {
            return back()
                ->withErrors(['referencias' => 'Solo puedes registrar un máximo de 3 referencias laborales.'])
                ->withInput();
        }

        DB::transaction(function () use ($request, $consultor) {
            $userId = auth()->id();

            $this->eliminarActuales($consultor->experienciasLaborales(), $userId, true, 'url_evidencia');
            $this->eliminarActuales($consultor->disponibilidades(), $userId);
            $this->eliminarActuales($consultor->habilidades(), $userId);
            $this->eliminarActuales($consultor->idiomas(), $userId, true, 'url_certificado');
            $this->eliminarActuales($consultor->tiposConsultoria(), $userId);
            $this->eliminarActuales($consultor->referencias(), $userId);

            foreach ($request->input('experiencias', []) as $index => $experiencia) {
                if (empty($experiencia['empresa']) && empty($experiencia['cargo'])) {
                    continue;
                }

                $rutaEvidencia = null;

                if ($request->hasFile("experiencias.$index.evidencia")) {
                    $rutaEvidencia = $request->file("experiencias.$index.evidencia")
                        ->store("consultores/{$consultor->id_consultor}/experiencia", 'public');
                }

                ConsultorExperienciaLaboral::create([
                    'id_consultor' => $consultor->id_consultor,
                    'empresa' => $experiencia['empresa'] ?? null,
                    'cargo' => $experiencia['cargo'] ?? null,
                    'descripcion' => $experiencia['descripcion'] ?? null,
                    'desde' => $experiencia['desde'] ?? null,
                    'hasta' => !empty($experiencia['trabajo_actual']) ? null : ($experiencia['hasta'] ?? null),
                    'trabajo_actual' => !empty($experiencia['trabajo_actual']),
                    'jefe_nombre' => $experiencia['jefe_nombre'] ?? null,
                    'jefe_email' => !empty($experiencia['jefe_email']) ? strtolower(trim($experiencia['jefe_email'])) : null,
                    'jefe_telefono' => $experiencia['jefe_telefono'] ?? null,
                    'url_evidencia' => $rutaEvidencia,
                    'activo' => true,
                    'usuario_crea' => $userId,
                ]);
            }

            foreach ($request->input('disponibilidades', []) as $idTipoDisponibilidad) {
                if ($idTipoDisponibilidad) {
                    ConsultorDisponibilidad::create([
                        'id_consultor' => $consultor->id_consultor,
                        'id_tipo_disponibilidad' => $idTipoDisponibilidad,
                        'activo' => true,
                        'usuario_crea' => $userId,
                    ]);
                }
            }

            foreach ($request->input('habilidades', []) as $idHabilidad) {
                if ($idHabilidad) {
                    ConsultorHabilidad::create([
                        'id_consultor' => $consultor->id_consultor,
                        'id_habilidad' => $idHabilidad,
                        'activo' => true,
                        'usuario_crea' => $userId,
                    ]);
                }
            }

            foreach ($request->input('idiomas', []) as $index => $idioma) {
                if (empty($idioma['id_idioma']) || empty($idioma['id_idioma_nivel'])) {
                    continue;
                }

                $rutaCertificado = null;

                if ($request->hasFile("idiomas.$index.certificado")) {
                    $rutaCertificado = $request->file("idiomas.$index.certificado")
                        ->store("consultores/{$consultor->id_consultor}/idiomas", 'public');
                }

                ConsultorIdioma::create([
                    'id_consultor' => $consultor->id_consultor,
                    'id_idioma' => $idioma['id_idioma'],
                    'id_idioma_nivel' => $idioma['id_idioma_nivel'],
                    'url_certificado' => $rutaCertificado,
                    'activo' => true,
                    'usuario_crea' => $userId,
                ]);
            }

            foreach ($request->input('tipos_consultoria', []) as $idTipoConsultoria) {
                if ($idTipoConsultoria) {
                    ConsultorTipoConsultoria::create([
                        'id_consultor' => $consultor->id_consultor,
                        'id_tipo_consultoria' => $idTipoConsultoria,
                        'activo' => true,
                        'usuario_crea' => $userId,
                    ]);
                }
            }

            foreach ($request->input('referencias', []) as $referencia) {
                if (empty($referencia['id_tipo_referencia']) || empty($referencia['nombre'])) {
                    continue;
                }

                ConsultorReferencia::create([
                    'id_consultor' => $consultor->id_consultor,
                    'id_tipo_referencia' => $referencia['id_tipo_referencia'],
                    'nombre' => trim($referencia['nombre']),
                    'telefono' => $referencia['telefono'] ?? null,
                    'correo' => !empty($referencia['correo']) ? strtolower(trim($referencia['correo'])) : null,
                    'empresa' => $referencia['empresa'] ?? null,
                    'cargo' => $referencia['cargo'] ?? null,
                    'activo' => true,
                    'usuario_crea' => $userId,
                ]);
            }
        });

        return redirect()
            ->route('fac.consultores.documentos.edit', $consultor)
            ->with('success', 'Experiencia y competencias guardadas correctamente. Continúa con documentos.');
    }

    private function eliminarActuales($relation, ?int $userId, bool $eliminarArchivo = false, ?string $campoArchivo = null): void
    {
        $items = $relation->whereNull('deleted_at')->get();

        foreach ($items as $item) {
            if ($eliminarArchivo && $campoArchivo && $item->{$campoArchivo} && Storage::disk('public')->exists($item->{$campoArchivo})) {
                Storage::disk('public')->delete($item->{$campoArchivo});
            }

            $item->update([
                'activo' => false,
                'usuario_elim' => $userId,
            ]);

            $item->delete();
        }
    }
}