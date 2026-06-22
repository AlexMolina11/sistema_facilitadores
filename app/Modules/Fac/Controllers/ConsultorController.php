<?php

namespace App\Modules\Fac\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Consultor;
use App\Modules\Fac\Requests\StoreConsultorRequest;
use App\Modules\Fac\Requests\UpdateConsultorRequest;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Arr;
use Illuminate\Support\Facades\Storage;
use App\Modules\Fac\Models\ConsultorDocumento;

class ConsultorController extends Controller
{
    public function index(Request $request)
    {
        $buscar = $request->get('buscar');
        $estado = $request->get('estado');

        $consultores = Consultor::query()
            ->when($buscar, function ($query) use ($buscar) {
                $query->where(function ($q) use ($buscar) {
                    $q->where('nombres', 'like', "%{$buscar}%")
                        ->orWhere('apellidos', 'like', "%{$buscar}%")
                        ->orWhere('numero_identificacion', 'like', "%{$buscar}%")
                        ->orWhere('nit', 'like', "%{$buscar}%");
                });
            })
            ->when($estado !== null && $estado !== '', function ($query) use ($estado) {
                $query->where('activo', $estado);
            })
            ->orderBy('apellidos')
            ->orderBy('nombres')
            ->paginate(10)
            ->withQueryString();

        return view('fac.consultores.index', compact('consultores', 'buscar', 'estado'));
    }

    public function create()
    {
        $catalogos = $this->catalogosFormulario();

        return view('fac.consultores.create', compact('catalogos'));
    }

    public function store(StoreConsultorRequest $request)
    {
        $data = Arr::except($request->validated(), [
            'id_departamento',
            'id_municipio_mh',
            'foto',
            'documento_identificacion',
            'documento_nit',
            'documento_nrc',
            'actividad_giro',
        ]);

        $consultor = Consultor::create([
            ...$data,
            'vigente' => $request->boolean('vigente'),
            'activo' => $request->boolean('activo'),
            'usuario_crea' => auth()->id(),
        ]);

        if ($request->hasFile('foto')) {
            $rutaFoto = $request->file('foto')
                ->store("consultores/{$consultor->id_consultor}/foto", 'public');

            $consultor->update([
                'ruta_foto' => $rutaFoto,
                'usuario_mod' => auth()->id(),
            ]);
        }

        $this->guardarDocumentosIdentificacion($request, $consultor);

        return redirect()
            ->route('fac.consultores.contacto.edit', $consultor)
            ->with('success', 'Datos personales guardados correctamente. Continúa con la información de contacto.');
    }

    public function show(Consultor $consultor)
    {
        $consultor->load([
            'sexoCatalogo',

            'emails' => fn ($query) => $query->where('activo', true)->orderByDesc('principal'),

            'telefonos' => fn ($query) => $query->where('activo', true),

            'redesSociales' => fn ($query) => $query->where('activo', true),

            'emergencias' => fn ($query) => $query->where('activo', true),

            'documentos' => fn ($query) => $query->where('activo', true),

            'formaciones' => fn ($query) => $query->where('activo', true)->orderByDesc('fecha_fin'),

            'experienciasLaborales' => fn ($query) => $query
                ->where('activo', true)
                ->orderByDesc('trabajo_actual')
                ->orderByDesc('desde'),

            'disponibilidades' => fn ($query) => $query->where('activo', true),

            'areasEspecializacion' => fn ($query) => $query
                ->where('activo', true)
                ->with(['areaEspecializacion', 'atestado', 'capacitacionFepade', 'habilidades.habilidadTecnica']),

            'idiomas' => fn ($query) => $query->where('activo', true),

            'referencias' => fn ($query) => $query->where('activo', true),

            'tiposConsultoria' => fn ($query) => $query->where('activo', true),
        ]);

        $catalogos = [
            'tiposTelefono' => DB::table('tbl_tipo_telefono')
                ->get()
                ->keyBy('id_tipo_telefono'),

            'tiposRedSocial' => DB::table('tbl_tipo_red_social')
                ->get()
                ->keyBy('id_tipo_red_social'),

            'tiposAtestado' => DB::table('tbl_tipo_atestado')
                ->get()
                ->keyBy('id_tipo_atestado'),

            'tiposFormacion' => DB::table('tbl_tipo_formacion')
                ->get()
                ->keyBy('id_tipo_formacion'),

            'nivelesAcademicos' => DB::table('tbl_nivel_academico')
                ->get()
                ->keyBy('id_nivel_academico'),

            'paises' => DB::table('tbl_pais')
                ->get()
                ->keyBy('id_pais'),

            'tiposDocumento' => DB::table('tbl_tipo_documento')
                ->get()
                ->keyBy('id_tipo_documento'),
            'tiposDisponibilidad' => DB::table('tbl_tipo_disponibilidad')
                ->get()
                ->keyBy('id_tipo_disponibilidad'),

            'areasEspecializacion' => DB::table('tbl_area_especializacion')
                ->whereNull('deleted_at')
                ->get()
                ->keyBy('id_area_especializacion'),

            'habilidadesTecnicas' => DB::table('tbl_habilidad_tecnica')
                ->whereNull('deleted_at')
                ->get()
                ->keyBy('id_habilidad_tecnica'),

            'idiomas' => DB::table('tbl_idioma')
                ->get()
                ->keyBy('id_idioma'),

            'nivelesIdioma' => DB::table('tbl_idioma_nivel')
                ->get()
                ->keyBy('id_idioma_nivel'),

            'tiposReferencia' => DB::table('tbl_tipo_referencia')
                ->get()
                ->keyBy('id_tipo_referencia'),

            'tiposRelacion' => DB::table('tbl_tipo_relacion')
                ->get()
                ->keyBy('id_tipo_relacion'),

            'tiposConsultoria' => DB::table('tbl_tipo_consultoria')
                ->get()
                ->keyBy('id_tipo_consultoria'),
        ];

        $avancePerfil = $consultor->avancePerfil();

        return view('fac.consultores.show', compact('consultor', 'catalogos', 'avancePerfil'));
    }

    public function edit(Consultor $consultor)
    {
        $consultor->load([
            'documentos' => fn ($query) => $query->where('activo', true),
        ]);

        $catalogos = $this->catalogosFormulario();

        return view('fac.consultores.edit', compact('consultor', 'catalogos'));
    }

    public function update(UpdateConsultorRequest $request, Consultor $consultor)
    {
        $data = Arr::except($request->validated(), [
            'id_departamento',
            'id_municipio_mh',
            'foto',
            'documento_identificacion',
            'documento_nit',
            'documento_nrc',
            'actividad_giro',
        ]);

        if ($request->hasFile('foto')) {
            if ($consultor->ruta_foto && Storage::disk('public')->exists($consultor->ruta_foto)) {
                Storage::disk('public')->delete($consultor->ruta_foto);
            }

            $data['ruta_foto'] = $request->file('foto')
                ->store("consultores/{$consultor->id_consultor}/foto", 'public');
        }

        $consultor->update([
            ...$data,
            'vigente' => $request->boolean('vigente'),
            'activo' => $request->boolean('activo'),
            'usuario_mod' => auth()->id(),
        ]);

        $this->guardarDocumentosIdentificacion($request, $consultor);

        return redirect()
            ->route('fac.consultores.contacto.edit', $consultor)
            ->with('success', 'Datos personales actualizados correctamente. Continúa con la información de contacto.');
    }

    public function destroy(Consultor $consultor)
    {
        $consultor->update([
            'activo' => false,
            'usuario_elim' => auth()->id(),
        ]);

        $consultor->delete();

        return redirect()
            ->route('fac.consultores.index')
            ->with('success', 'Consultor eliminado correctamente.');
    }

    private function catalogosFormulario(): array
    {
        return [
            'paises' => DB::table('tbl_pais')
                ->where('activo', true)
                ->orderBy('nombre_pais')
                ->get(),

            'departamentos' => DB::table('tbl_departamento')
                ->where('activo', true)
                ->orderBy('nombre_departamento')
                ->get(),

            'municipiosMh' => DB::table('tbl_municipio_mh')
                ->where('activo', true)
                ->orderBy('municipio_mh_nombre')
                ->get(),

            'municipios' => DB::table('tbl_municipio')
                ->where('activo', true)
                ->orderBy('nombre_distrito')
                ->get(),

            'tiposDocumento' => DB::table('tbl_tipo_documento')
                ->get()
                ->keyBy('id_tipo_documento'),

            'sexos' => DB::table('tbl_sexo')
                ->where('activo', true)
                ->orderBy('nombre')
                ->get(),
        ];
    }

    private function guardarDocumentosIdentificacion($request, Consultor $consultor): void
    {
        $this->guardarDocumentoPorTipo(
            request: $request,
            consultor: $consultor,
            nombresTipo: $this->mapearTipoIdentificacion($request->input('tipo_identificacion')),
            campoArchivo: 'documento_identificacion',
            numero: $request->input('numero_identificacion'),
            actividadGiro: null
        );

        $this->guardarDocumentoPorTipo(
            request: $request,
            consultor: $consultor,
            nombresTipo: ['NIT', 'N.I.T.'],
            campoArchivo: 'documento_nit',
            numero: $request->input('nit'),
            actividadGiro: null
        );

        $this->guardarDocumentoPorTipo(
            request: $request,
            consultor: $consultor,
            nombresTipo: ['NRC'],
            campoArchivo: 'documento_nrc',
            numero: $request->input('nrc'),
            actividadGiro: $request->input('actividad_giro')
        );
    }

    private function mapearTipoIdentificacion(?string $tipoIdentificacion): array
    {
        return match ($tipoIdentificacion) {
            'DUI' => ['DUI', 'D.U.I.'],
            'Pasaporte' => ['PASAPORTE', 'Pasaporte'],
            'Carné de residencia' => ['CARNET DE RESIDENTE', 'Carné de residencia', 'Carne de residencia'],
            default => array_filter([$tipoIdentificacion]),
        };
    }

    private function guardarDocumentoPorTipo($request, Consultor $consultor, array $nombresTipo, string $campoArchivo, ?string $numero, ?string $actividadGiro): void
    {
        if (!$numero && !$request->hasFile($campoArchivo)) {
            return;
        }

        $nombresNormalizados = collect($nombresTipo)
            ->filter()
            ->map(fn ($nombre) => $this->normalizarTextoDocumento($nombre))
            ->values();

        $tiposDocumento = DB::table('tbl_tipo_documento')
            ->where('activo', true)
            ->get();

        $tipoDocumento = $tiposDocumento->first(function ($tipo) use ($nombresNormalizados) {
            return $nombresNormalizados->contains(
                $this->normalizarTextoDocumento($tipo->nombre)
            );
        });

        if (!$tipoDocumento) {
            return;
        }

        $documento = ConsultorDocumento::where('id_consultor', $consultor->id_consultor)
            ->where('id_tipo_documento', $tipoDocumento->id_tipo_documento)
            ->whereNull('deleted_at')
            ->first();

        $rutaArchivo = $documento?->url_archivo;

        if ($request->hasFile($campoArchivo)) {
            if ($rutaArchivo && Storage::disk('public')->exists($rutaArchivo)) {
                Storage::disk('public')->delete($rutaArchivo);
            }

            $rutaArchivo = $request->file($campoArchivo)
                ->store("consultores/{$consultor->id_consultor}/documentos", 'public');
        }

        if ($documento) {
            $documento->update([
                'numero' => $numero,
                'actividad_giro' => $actividadGiro,
                'url_archivo' => $rutaArchivo,
                'activo' => true,
                'usuario_mod' => auth()->id(),
            ]);

            return;
        }

        ConsultorDocumento::create([
            'id_consultor' => $consultor->id_consultor,
            'id_tipo_documento' => $tipoDocumento->id_tipo_documento,
            'numero' => $numero,
            'actividad_giro' => $actividadGiro,
            'url_archivo' => $rutaArchivo,
            'activo' => true,
            'usuario_crea' => auth()->id(),
        ]);
    }

    private function normalizarTextoDocumento(?string $texto): string
    {
        $texto = strtolower(trim($texto ?? ''));
        $texto = str_replace(['.', '-', '_', ' '], '', $texto);

        return $texto;
    }

}