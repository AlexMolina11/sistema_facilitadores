<?php

namespace App\Modules\Fac\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Consultor;
use App\Modules\Fac\Models\ConsultorFormacionAcademica;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ConsultorFormacionController extends Controller
{
    public function edit(Consultor $consultor)
    {
        $consultor->load([
            'formaciones' => fn ($query) => $query->where('activo', true)->orderByDesc('fecha_fin'),
        ]);

        $catalogos = [
            'tiposFormacion' => DB::table('tbl_tipo_formacion')
                ->where('activo', true)
                ->orderBy('id_tipo_formacion')
                ->get(),

            'tiposAtestado' => DB::table('tbl_tipo_atestado')
                ->where('activo', true)
                ->orderBy('nombre')
                ->get(),

            'nivelesAcademicos' => DB::table('tbl_nivel_academico')
                ->where('activo', true)
                ->orderBy('id_nivel_academico')
                ->get(),

            'paises' => DB::table('tbl_pais')
                ->where('activo', true)
                ->orderBy('nombre_pais')
                ->get(),
        ];

        $formacionesPorTipo = $consultor->formaciones->groupBy(function ($formacion) use ($catalogos) {
            $tipoAtestado = $catalogos['tiposAtestado']->firstWhere('id_tipo_atestado', $formacion->id_tipo_atestado);

            return $tipoAtestado->id_tipo_formacion ?? 'sin_tipo';
        });

        return view('fac.consultores.formacion', compact('consultor', 'catalogos', 'formacionesPorTipo'));
    }

    public function store(Request $request, Consultor $consultor)
    {
        $data = $this->validarAtestado($request, true);

        $this->validarRelacionTipoFormacionAtestado($data);

        $rutaArchivo = $request->file('archivo_atestado')
            ->store("consultores/{$consultor->id_consultor}/formacion", 'public');

        ConsultorFormacionAcademica::create([
            'id_consultor' => $consultor->id_consultor,
            'id_tipo_atestado' => $data['id_tipo_atestado'],
            'id_nivel_academico' => $data['id_nivel_academico'],
            'id_pais' => $data['id_pais'] ?? null,
            'descripcion' => $data['descripcion'],
            'institucion' => $data['institucion'],
            'fecha_inicio' => $data['fecha_inicio'] ?? null,
            'fecha_fin' => $data['fecha_fin'] ?? null,
            'url' => $rutaArchivo,
            'activo' => true,
            'usuario_crea' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.consultores.formacion.edit', $consultor)
            ->with('success', 'Atestado agregado correctamente.');
    }

    public function updateAtestado(Request $request, Consultor $consultor, ConsultorFormacionAcademica $formacion)
    {
        $this->validarPertenencia($consultor, $formacion);

        $data = $this->validarAtestado($request, false);

        $this->validarRelacionTipoFormacionAtestado($data);

        $rutaArchivo = $formacion->url;

        if ($request->hasFile('archivo_atestado')) {
            if ($formacion->url && Storage::disk('public')->exists($formacion->url)) {
                Storage::disk('public')->delete($formacion->url);
            }

            $rutaArchivo = $request->file('archivo_atestado')
                ->store("consultores/{$consultor->id_consultor}/formacion", 'public');
        }

        $formacion->update([
            'id_tipo_atestado' => $data['id_tipo_atestado'],
            'id_nivel_academico' => $data['id_nivel_academico'],
            'id_pais' => $data['id_pais'] ?? null,
            'descripcion' => $data['descripcion'],
            'institucion' => $data['institucion'],
            'fecha_inicio' => $data['fecha_inicio'] ?? null,
            'fecha_fin' => $data['fecha_fin'] ?? null,
            'url' => $rutaArchivo,
            'usuario_mod' => auth()->id(),
        ]);

        return redirect()
            ->route('fac.consultores.formacion.edit', $consultor)
            ->with('success', 'Atestado actualizado correctamente.');
    }

    public function destroyAtestado(Consultor $consultor, ConsultorFormacionAcademica $formacion)
    {
        $this->validarPertenencia($consultor, $formacion);

        if ($formacion->url && Storage::disk('public')->exists($formacion->url)) {
            Storage::disk('public')->delete($formacion->url);
        }

        $formacion->update([
            'activo' => false,
            'usuario_elim' => auth()->id(),
        ]);

        $formacion->delete();

        return redirect()
            ->route('fac.consultores.formacion.edit', $consultor)
            ->with('success', 'Atestado eliminado correctamente.');
    }

    public function continuar(Consultor $consultor)
    {
        return redirect()
            ->route('fac.consultores.habilidades.edit', $consultor)
            ->with('success', 'Títulos académicos guardados correctamente. Continúa con habilidades.');
    }

    private function validarAtestado(Request $request, bool $archivoRequerido): array
    {
        return $request->validate([
            'id_tipo_formacion' => ['required', 'integer', 'exists:tbl_tipo_formacion,id_tipo_formacion'],
            'id_tipo_atestado' => ['required', 'integer', 'exists:tbl_tipo_atestado,id_tipo_atestado'],
            'id_nivel_academico' => ['required', 'integer', 'exists:tbl_nivel_academico,id_nivel_academico'],
            'id_pais' => ['nullable', 'integer', 'exists:tbl_pais,id_pais'],
            'institucion' => ['required', 'string', 'max:250'],
            'descripcion' => ['required', 'string', 'max:250'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'archivo_atestado' => [
                $archivoRequerido ? 'required' : 'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:5120',
            ],
        ], [
            'id_tipo_formacion.required' => 'Debes seleccionar el tipo de formación.',
            'id_tipo_atestado.required' => 'Debes seleccionar el tipo de atestado.',
            'id_nivel_academico.required' => 'Debes seleccionar el nivel académico.',
            'institucion.required' => 'Debes ingresar la institución.',
            'descripcion.required' => 'Debes ingresar la descripción o título obtenido.',
            'fecha_fin.after_or_equal' => 'La fecha de fin no puede ser menor que la fecha de inicio.',
            'archivo_atestado.required' => 'Debes adjuntar el comprobante del atestado.',
            'archivo_atestado.mimes' => 'El archivo debe ser PDF, JPG, JPEG, PNG o WEBP.',
            'archivo_atestado.max' => 'El archivo no debe superar los 5 MB.',
        ]);
    }

    private function validarRelacionTipoFormacionAtestado(array $data): void
    {
        $existe = DB::table('tbl_tipo_atestado')
            ->where('id_tipo_atestado', $data['id_tipo_atestado'])
            ->where('id_tipo_formacion', $data['id_tipo_formacion'])
            ->where('activo', true)
            ->exists();

        if (!$existe) {
            throw ValidationException::withMessages([
                'id_tipo_atestado' => 'El tipo de atestado no pertenece al tipo de formación seleccionado.',
            ]);
        }
    }

    private function validarPertenencia(Consultor $consultor, ConsultorFormacionAcademica $formacion): void
    {
        if ((int) $formacion->id_consultor !== (int) $consultor->id_consultor) {
            abort(403, 'Este atestado no pertenece al consultor seleccionado.');
        }
    }
}