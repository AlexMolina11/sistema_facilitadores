<?php

namespace App\Modules\Fac\Controllers;

use App\Http\Controllers\Controller;
use App\Modules\Fac\Models\Consultor;
use Barryvdh\DomPDF\Facade\Pdf;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use Illuminate\View\View;
use Symfony\Component\HttpFoundation\Response;
use App\Modules\Fac\Models\CvPlantilla;
use Illuminate\Support\Facades\View as ViewFacade;
use Illuminate\Support\Facades\Storage;

class ExportacionCvController extends Controller
{
    public function configurar(Consultor $consultor): View
    {
        $this->cargarConsultor($consultor);

        return view('fac.cv.configurar', [
            'consultor' => $consultor,
            'plantillas' => $this->plantillas(),
            'cvData' => $this->cvData($consultor),
        ]);
    }

    public function pdf(Request $request, Consultor $consultor): Response
    {
        $this->cargarConsultor($consultor);

        $config = json_decode($request->input('config_json', '{}'), true);

        if (! is_array($config)) {
            $config = [];
        }

        $codigoPlantilla = $config['plantilla'] ?? 'fepade';

        $plantilla = CvPlantilla::query()
            ->where('codigo', $codigoPlantilla)
            ->where('activo', true)
            ->where('activa', true)
            ->where('vista_verificada', true)
            ->first();

        if (
            ! $plantilla ||
            ! \Illuminate\Support\Facades\View::exists($plantilla->vista_blade)
        ) {
            $pdf = Pdf::loadView('fac.cv.pdf.plantilla-no-disponible')
                ->setPaper('letter', 'portrait');

            return $pdf->stream('plantilla-no-disponible.pdf');
        }

        $pdf = Pdf::loadView($plantilla->vista_blade, [
            'consultor' => $consultor,
            'cvData' => $this->cvData($consultor),
            'config' => $config,
        ])->setPaper($plantilla->tamanio_papel, $plantilla->orientacion);

        $nombre = 'cv-' . str($consultor->nombre_completo ?: 'consultor')->slug('-') . '.pdf';

        return $pdf->stream($nombre);
    }

   private function plantillas(): array
    {
        return CvPlantilla::query()
            ->where('activo', true)
            ->where('activa', true)
            ->where('vista_verificada', true)
            ->orderBy('orden')
            ->orderBy('nombre')
            ->get()
            ->filter(fn ($plantilla) => \Illuminate\Support\Facades\View::exists($plantilla->vista_blade))
            ->mapWithKeys(fn ($plantilla) => [
                $plantilla->codigo => $plantilla->nombre,
            ])
            ->toArray();
    }

    private function cargarConsultor(Consultor $consultor): void
    {
        $consultor->load([
            'pais',
            'municipio',
            'municipio.departamento',
            'emails' => fn ($q) => $q->where('activo', true)->orderByDesc('principal'),
            'telefonos' => fn ($q) => $q->where('activo', true)->with('tipoTelefono'),
            'disponibilidades' => fn ($q) => $q->where('activo', true)->with('tipoDisponibilidad'),
            'experienciasLaborales' => fn ($q) => $q->where('activo', true)->orderByDesc('trabajo_actual')->orderByDesc('desde'),
            'atestados' => fn ($q) => $q->where('activo', true)
                ->with(['tipoFormacion', 'tipoAtestado', 'nivelAcademico', 'pais'])
                ->orderByDesc('fecha_fin'),
            'capacitacionesFepade' => fn ($q) => $q->where('activo', true)->orderByDesc('fecha_fin'),
            'areasEspecializacion' => fn ($q) => $q->where('activo', true)
                ->with(['areaEspecializacion', 'habilidades.habilidadTecnica']),
            'idiomas' => fn ($q) => $q->where('activo', true)->with(['idioma', 'nivel']),
            'referencias' => fn ($q) => $q->where('activo', true)->with(['tipoReferencia']),
        ]);
    }

    private function cvData(Consultor $consultor): array
    {
        $fotoUrl = null;
        $fotoPdf = null;

        if ($consultor->ruta_foto) {
            $fotoUrl = Storage::url($consultor->ruta_foto);

            try {
                $fotoPdf = Storage::disk('public')->path($consultor->ruta_foto);
            } catch (\Throwable $e) {
                $fotoPdf = public_path('storage/' . $consultor->ruta_foto);
            }
        }

        $pais = optional($consultor->pais)->nombre_pais;
        $departamento = optional(optional($consultor->municipio)->departamento)->nombre_departamento;
        $distrito = optional($consultor->municipio)->nombre_distrito;

        $residenciaCompleta = collect([
            $pais,
            $departamento,
            $distrito,
        ])->filter()->implode(', ');

        return [
            'personal' => [
                'foto' => $fotoUrl,
                'foto_pdf' => $fotoPdf,
                'nombre' => $consultor->nombre_completo,
                'fecha_nacimiento' => optional($consultor->fecha_nacimiento)->format('d/m/Y'),
                'nacionalidad' => $consultor->nacionalidad,
                'residencia' => $pais,
                'residencia_completa' => $residenciaCompleta,
                'direccion' => $consultor->direccion_residencia,
            ],

            'emails' => $consultor->emails->map(fn ($item) => [
                'id' => $item->id_email,
                'email' => $item->email,
            ])->values(),

            'telefonos' => $consultor->telefonos->map(fn ($item) => [
                'id' => $item->id_consultor_telefono,
                'tipo' => optional($item->tipoTelefono)->nombre,
                'numero' => $item->numero_telefono,
                'extension' => $item->extension,
                'telefono_completo' => trim(
                    (optional($item->tipoTelefono)->nombre ? optional($item->tipoTelefono)->nombre . ': ' : '') .
                    ($item->extension ? '(' . $item->extension . ') ' : '') .
                    $item->numero_telefono
                ),
            ])->values(),

            'experiencias' => $consultor->experienciasLaborales->map(fn ($item) => [
                'id' => $item->id_experiencia,
                'empresa' => $item->empresa,
                'cargo' => $item->cargo,
                'descripcion' => $item->descripcion,
                'desde' => optional($item->desde)->format('d/m/Y'),
                'hasta' => $item->trabajo_actual ? 'Actualidad' : optional($item->hasta)->format('d/m/Y'),
                'trabajo_actual' => $item->trabajo_actual ? 'Sí' : 'No',
                'jefe_nombre' => $item->jefe_nombre,
                'jefe_email' => $item->jefe_email,
                'jefe_telefono' => $item->jefe_telefono,
            ])->values(),

            'atestados' => $consultor->atestados->map(fn ($item) => [
                'id' => $item->id_atestado,
                'tipo_formacion' => optional($item->tipoFormacion)->nombre,
                'tipo_atestado' => optional($item->tipoAtestado)->nombre,
                'nivel' => optional($item->nivelAcademico)->nombre,
                'titulo' => $item->titulo,
                'institucion' => $item->institucion,
                'descripcion' => $item->descripcion,
                'pais' => optional($item->pais)->nombre_pais,
                'fecha_inicio' => optional($item->fecha_inicio)->format('d/m/Y'),
                'fecha_fin' => optional($item->fecha_fin)->format('d/m/Y'),
                'fecha_emision' => optional($item->fecha_emision)->format('d/m/Y'),
                'fecha_vencimiento' => optional($item->fecha_vencimiento)->format('d/m/Y'),
                'horas' => $item->horas,
                'archivo_url' => $item->url_archivo ? url(Storage::url($item->url_archivo)) : null,
            ])->values(),

            'capacitaciones_fepade' => $consultor->capacitacionesFepade->map(fn ($item) => [
                'id' => $item->id_capacitacion_fepade,
                'nombre_evento' => $item->nombre_evento,
                'tema' => $item->tema,
                'institucion' => $item->institucion,
                'modalidad' => $item->modalidad,
                'fecha_inicio' => optional($item->fecha_inicio)->format('d/m/Y'),
                'fecha_fin' => optional($item->fecha_fin)->format('d/m/Y'),
                'horas' => $item->horas,
                'fuente' => $item->fuente,
            ])->values(),

            'areas' => $consultor->areasEspecializacion->map(fn ($item) => [
                'id' => $item->id_consultor_area ?? $item->id_consultor_area_especializacion ?? $item->id,
                'nombre' => optional($item->areaEspecializacion)->nombre,
                'habilidades' => $item->habilidades->map(fn ($hab) => [
                    'id' => $hab->id_consultor_hab_tec ?? $hab->id,
                    'nombre' => optional($hab->habilidadTecnica)->nombre,
                ])->values(),
            ])->values(),

            'idiomas' => $consultor->idiomas->map(fn ($item) => [
                'id' => $item->id_consultor_idioma,
                'idioma' => optional($item->idioma)->nombre,
                'nivel' => optional($item->nivel)->nombre,
                'certificado' => $item->url_certificado ? 'Sí' : 'No',
                'certificado_url' => $item->url_certificado ? url(Storage::url($item->url_certificado)) : null,
            ])->values(),

            'referencias' => $consultor->referencias->map(fn ($item) => [
                'id' => $item->id_referencia,
                'tipo' => optional($item->tipoReferencia)->nombre,
                'nombre' => $item->nombre,
                'telefono' => $item->telefono,
                'correo' => $item->correo,
                'empresa' => $item->empresa,
                'cargo' => $item->cargo,
            ])->values(),

            'disponibilidades' => $consultor->disponibilidades->map(fn ($item) => [
                'id' => $item->id_consultor_disponibilidad,
                'nombre' => optional($item->tipoDisponibilidad)->nombre,
            ])->values(),
        ];
    }
}