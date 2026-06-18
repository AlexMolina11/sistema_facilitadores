<?php

namespace App\Modules\Fac\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Consultor;
use App\Modules\Fac\Models\ConsultorAtestado;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Storage;
use Illuminate\Validation\ValidationException;

class ConsultorFormacionController extends Controller
{
    public function edit(Consultor $consultor)
    {
        $consultor->load([
            'atestados' => fn ($query) => $query
                ->where('activo', true)
                ->orderByDesc('fecha_fin')
                ->orderByDesc('fecha_emision')
                ->orderByDesc('created_at'),
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

        $formacionesPorTipo = $consultor->atestados->groupBy('id_tipo_formacion');

        return view('fac.consultores.formacion', compact('consultor', 'catalogos', 'formacionesPorTipo'));
    }

    /**
     * Compatibilidad temporal con la ruta anterior fac.consultores.formacion.store.
     * La vista nueva usa ConsultorAtestadoController, pero mantenemos este método
     * para evitar errores si algún enlace anterior todavía apunta aquí.
     */
    public function store(Request $request, Consultor $consultor)
    {
        $data = $this->validarAtestado($request, true);
        $this->validarRelacionTipoFormacionAtestado($data);

        $archivo = $request->file('archivo_atestado');

        if ($archivo) {
            $data['url_archivo'] = $archivo->store("consultores/{$consultor->id_consultor}/atestados", 'public');
            $data['nombre_archivo_original'] = $archivo->getClientOriginalName();
        }

        unset($data['archivo_atestado']);

        ConsultorAtestado::create(array_merge($data, [
            'id_consultor' => $consultor->id_consultor,
            'activo' => true,
            'usuario_crea' => auth()->id(),
        ]));

        return redirect()
            ->route('fac.consultores.formacion.edit', $consultor)
            ->with('success', 'Registro de trayectoria agregado correctamente.');
    }

    /**
     * Compatibilidad temporal con la ruta anterior fac.consultores.formacion.atestados.update.
     */
    public function updateAtestado(Request $request, Consultor $consultor, ConsultorAtestado $formacion)
    {
        $this->validarPertenencia($consultor, $formacion);

        $data = $this->validarAtestado($request, false);
        $this->validarRelacionTipoFormacionAtestado($data);

        $archivo = $request->file('archivo_atestado');

        if ($archivo) {
            if ($formacion->url_archivo && Storage::disk('public')->exists($formacion->url_archivo)) {
                Storage::disk('public')->delete($formacion->url_archivo);
            }

            $data['url_archivo'] = $archivo->store("consultores/{$consultor->id_consultor}/atestados", 'public');
            $data['nombre_archivo_original'] = $archivo->getClientOriginalName();
        }

        unset($data['archivo_atestado']);

        $formacion->update(array_merge($data, [
            'usuario_mod' => auth()->id(),
        ]));

        return redirect()
            ->route('fac.consultores.formacion.edit', $consultor)
            ->with('success', 'Registro de trayectoria actualizado correctamente.');
    }

    /**
     * Compatibilidad temporal con la ruta anterior fac.consultores.formacion.atestados.destroy.
     */
    public function destroyAtestado(Consultor $consultor, ConsultorAtestado $formacion)
    {
        $this->validarPertenencia($consultor, $formacion);

        if ($formacion->url_archivo && Storage::disk('public')->exists($formacion->url_archivo)) {
            Storage::disk('public')->delete($formacion->url_archivo);
        }

        $formacion->update([
            'activo' => false,
            'usuario_elim' => auth()->id(),
        ]);

        $formacion->delete();

        return redirect()
            ->route('fac.consultores.formacion.edit', $consultor)
            ->with('success', 'Registro de trayectoria eliminado correctamente.');
    }

    public function continuar(Consultor $consultor)
    {
        return redirect()
            ->route('fac.consultores.habilidades.edit', $consultor)
            ->with('success', 'Trayectoria académica y profesional guardada correctamente. Continúa con habilidades.');
    }

    private function validarAtestado(Request $request, bool $archivoRequerido): array
    {
        return $request->validate([
            'id_tipo_formacion' => ['required', 'integer', 'exists:tbl_tipo_formacion,id_tipo_formacion'],
            'id_tipo_atestado' => ['required', 'integer', 'exists:tbl_tipo_atestado,id_tipo_atestado'],
            'id_nivel_academico' => ['nullable', 'integer', 'exists:tbl_nivel_academico,id_nivel_academico'],
            'id_pais' => ['nullable', 'integer', 'exists:tbl_pais,id_pais'],
            'titulo' => ['required', 'string', 'max:250'],
            'descripcion' => ['nullable', 'string'],
            'institucion' => ['nullable', 'string', 'max:250'],
            'entidad_acreditadora' => ['nullable', 'string', 'max:250'],
            'cliente_institucion' => ['nullable', 'string', 'max:250'],
            'codigo_acreditacion' => ['nullable', 'string', 'max:100'],
            'fecha_inicio' => ['nullable', 'date'],
            'fecha_fin' => ['nullable', 'date', 'after_or_equal:fecha_inicio'],
            'fecha_emision' => ['nullable', 'date'],
            'fecha_vencimiento' => ['nullable', 'date', 'after_or_equal:fecha_emision'],
            'horas' => ['nullable', 'integer', 'min:0', 'max:9999'],
            'archivo_atestado' => [
                $archivoRequerido ? 'nullable' : 'nullable',
                'file',
                'mimes:pdf,jpg,jpeg,png,webp',
                'max:5120',
            ],
        ], [
            'id_tipo_formacion.required' => 'Debes seleccionar el tipo de formación.',
            'id_tipo_atestado.required' => 'Debes seleccionar el tipo de atestado.',
            'titulo.required' => 'Debes ingresar el título o nombre del registro.',
            'fecha_fin.after_or_equal' => 'La fecha de fin no puede ser menor que la fecha de inicio.',
            'fecha_vencimiento.after_or_equal' => 'La fecha de vencimiento no puede ser menor que la fecha de emisión.',
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

    private function validarPertenencia(Consultor $consultor, ConsultorAtestado $formacion): void
    {
        if ((int) $formacion->id_consultor !== (int) $consultor->id_consultor) {
            abort(403, 'Este registro no pertenece al consultor seleccionado.');
        }
    }
}
