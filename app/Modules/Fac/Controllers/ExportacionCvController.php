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
use App\Modules\Fac\Models\TipoFormacion;
use Illuminate\Support\Str;
use PhpOffice\PhpWord\PhpWord;
use PhpOffice\PhpWord\IOFactory;

class ExportacionCvController extends Controller
{
    public function configurar(Consultor $consultor): View
    {
        $this->cargarConsultor($consultor);

        return view('fac.cv.configurar', [
            'consultor' => $consultor,
            'plantillas' => $this->plantillas(),
            'cvData' => $this->cvData($consultor),
            'cvCatalogos' => [
                'tipo_formacion' => $this->mapaTipoFormacion(),
            ],
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
            'cvCatalogos' => [
                'tipo_formacion' => $this->mapaTipoFormacion(),
            ],
        ])->setPaper($plantilla->tamanio_papel, $plantilla->orientacion);

        $nombre = 'cv-' . str($consultor->nombre_completo ?: 'consultor')->slug('-') . '.pdf';

        return $pdf->stream($nombre);
    }

    public function word(Request $request, Consultor $consultor): Response
    {
        $this->cargarConsultor($consultor);

        $config = json_decode($request->input('config_json', '{}'), true);

        if (! is_array($config)) {
            $config = [];
        }

        $cvData = $this->cvData($consultor);

        $codigoPlantilla = $config['plantilla'] ?? 'fepade';

        $phpWord = match ($codigoPlantilla) {
            'fepade' => $this->construirWordFepade(
                $consultor,
                $cvData,
                $config
            ),

            'profesional' => $this->construirWordProfesional(
                $consultor,
                $cvData,
                $config
            ),

            'mineducyt_birf' => $this->construirWordMineducytBirf(
                $consultor,
                $cvData,
                $config
            ),

            'resumen_personal' => $this->construirWordResumenPersonal(
                $consultor,
                $cvData,
                $config
            ),

            default => $this->construirWordFepade(
                $consultor,
                $cvData,
                $config
            ),
        };

        $nombre = 'cv-' .
            str($consultor->nombre_completo ?: 'consultor')->slug('-') .
            '.docx';

        $rutaTemporal = tempnam(sys_get_temp_dir(), 'cv_');

        $writer = IOFactory::createWriter($phpWord, 'Word2007');
        $writer->save($rutaTemporal);

        return response()
            ->download(
                $rutaTemporal,
                $nombre,
                [
                    'Content-Type' =>
                        'application/vnd.openxmlformats-officedocument.wordprocessingml.document',
                ]
            )
            ->deleteFileAfterSend(true);
    }

    private function seleccionado(
        array $config,
        string $seccion,
        string|int $item,
        string $campo
    ): bool {
        return data_get(
            $config,
            "selections.{$seccion}.{$item}.{$campo}"
        ) === true;
    }

    private function valorSeleccionado(
        array $config,
        string $seccion,
        array $item,
        string $campo
    ): mixed {
        return $this->seleccionado(
            $config,
            $seccion,
            $item['id'],
            $campo
        )
            ? ($item[$campo] ?? '')
            : '';
    }

    private function itemVisible(
        array $config,
        string $seccion,
        array $item,
        array $campos
    ): bool {
        foreach ($campos as $campo) {
            if (
                $this->seleccionado(
                    $config,
                    $seccion,
                    $item['id'],
                    $campo
                ) &&
                ! blank($item[$campo] ?? null)
            ) {
                return true;
            }
        }

        return false;
    }

    private function nuevaSeccionWord(
        \PhpOffice\PhpWord\PhpWord $phpWord
    ): \PhpOffice\PhpWord\Element\Section {
        return $phpWord->addSection([
            'paperSize' => 'Letter',
            'orientation' => 'portrait',
            'marginTop' => 720,
            'marginRight' => 900,
            'marginBottom' => 720,
            'marginLeft' => 900,
        ]);
    }

    private function tituloWord(
        \PhpOffice\PhpWord\Element\Section $section,
        string $titulo
    ): void {
        $section->addTextBreak();

        $section->addText(
            $titulo,
            [
                'bold' => true,
                'size' => 12,
            ]
        );
    }

    private function textoPersonalSeleccionado(
        array $config,
        array $cvData,
        string $campo
    ): string {
        if (
            ! $this->seleccionado(
                $config,
                'personal',
                'personal',
                $campo
            )
        ) {
            return '';
        }

        return (string) data_get(
            $cvData,
            "personal.{$campo}",
            ''
        );
    }

    private function agregarLineaWord(
        \PhpOffice\PhpWord\Element\Section $section,
        string $etiqueta,
        ?string $valor
    ): void {
        $valor = trim((string) $valor);

        if ($valor === '') {
            return;
        }

        $run = $section->addTextRun();

        $run->addText(
            $etiqueta . ': ',
            ['bold' => true]
        );

        $run->addText($valor);
    }

    private function construirWordFepade(
        Consultor $consultor,
        array $cvData,
        array $config
    ): PhpWord {
        $phpWord = new PhpWord();

        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(10);

        $section = $phpWord->addSection([
            'paperSize' => 'Letter',
            'orientation' => 'portrait',
            'marginTop' => 720,
            'marginRight' => 900,
            'marginBottom' => 720,
            'marginLeft' => 900,
        ]);

        /*
        |--------------------------------------------------------------------------
        | Encabezado
        |--------------------------------------------------------------------------
        */

        $section->addText(
            'CONSULTORES FEPADE 2026',
            [
                'bold' => true,
                'size' => 10,
            ],
            [
                'alignment' => 'center',
            ]
        );

        $section->addText(
            'Hoja de Vida',
            [
                'bold' => true,
                'size' => 18,
            ],
            [
                'alignment' => 'center',
                'spaceAfter' => 0,
            ]
        );

        $section->addText(
            'Formato CV FEPADE',
            [
                'italic' => true,
                'size' => 10,
            ],
            [
                'alignment' => 'center',
                'spaceAfter' => 240,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Datos principales
        |--------------------------------------------------------------------------
        */

        $personal = $cvData['personal'] ?? [];

        $nombre = $this->seleccionado(
            $config,
            'personal',
            'personal',
            'nombre'
        )
            ? ($personal['nombre'] ?? '')
            : '';

        $fechaNacimiento = $this->seleccionado(
            $config,
            'personal',
            'personal',
            'fecha_nacimiento'
        )
            ? ($personal['fecha_nacimiento'] ?? '')
            : '';

        $residencia = $this->seleccionado(
            $config,
            'personal',
            'personal',
            'residencia'
        )
            ? ($personal['residencia'] ?? '')
            : '';

        /*
        * El PDF toma como cargo la experiencia actual.
        * Si no existe, utiliza la experiencia finalizada más reciente.
        */
        $experiencias = collect($cvData['experiencias'] ?? []);

        $cargoActual = $experiencias->first(
            fn ($item) =>
                ($item['trabajo_actual_bool'] ?? false) === true
        );

        $cargoReciente = $cargoActual ?: $experiencias
            ->filter(
                fn ($item) =>
                    ! blank($item['hasta_iso'] ?? null)
            )
            ->sortByDesc('hasta_iso')
            ->first();

        $cargo = '';

        if (
            $cargoReciente &&
            $this->seleccionado(
                $config,
                'experiencias',
                $cargoReciente['id'],
                'cargo'
            )
        ) {
            $cargo = $cargoReciente['cargo'] ?? '';
        }

        $tablaDatos = $section->addTable([
            'borderSize' => 6,
            'cellMargin' => 80,
            'width' => 100,
            'unit' => 'pct',
        ]);

        $datosPrincipales = [
            'Cargo:' => $cargo,
            'Nombre del Profesional:' => $nombre,
            'Fecha de nacimiento:' => $fechaNacimiento,
            'País de ciudadanía/residencia:' => $residencia,
        ];

        foreach ($datosPrincipales as $etiqueta => $valor) {
            $tablaDatos->addRow();

            $tablaDatos->addCell(3500)->addText(
                $etiqueta,
                ['bold' => true]
            );

            $tablaDatos->addCell(6500)->addText(
                (string) $valor
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Educación
        |--------------------------------------------------------------------------
        */

        $section->addTextBreak();

        $section->addText(
            'Educación:',
            [
                'bold' => true,
                'size' => 12,
            ]
        );

        $tipoFormacion = $this->mapaTipoFormacion();

        $idsEducacionFormal =
            $tipoFormacion['educacion_formal'] ?? [];

        $educacionFormal = collect($cvData['atestados'] ?? [])
            ->filter(
                fn ($item) =>
                    in_array(
                        (int) ($item['id_tipo_formacion'] ?? 0),
                        $idsEducacionFormal,
                        true
                    )
            )
            ->filter(
                fn ($item) =>
                    $this->itemVisible(
                        $config,
                        'atestados',
                        $item,
                        [
                            'titulo',
                            'institucion',
                            'fecha_inicio',
                            'fecha_fin',
                            'archivo_url',
                        ]
                    )
            );

        $tablaEducacion = $section->addTable([
            'borderSize' => 6,
            'cellMargin' => 80,
            'width' => 100,
            'unit' => 'pct',
        ]);

        $tablaEducacion->addRow();

        foreach (
            [
                'Título obtenido',
                'Institución',
                'Fecha de estudios',
            ] as $encabezado
        ) {
            $tablaEducacion
                ->addCell()
                ->addText(
                    $encabezado,
                    ['bold' => true]
                );
        }

        if ($educacionFormal->isEmpty()) {
            $tablaEducacion->addRow();

            $tablaEducacion->addCell()->addText('');
            $tablaEducacion->addCell()->addText('');
            $tablaEducacion->addCell()->addText('');
        } else {
            foreach ($educacionFormal as $item) {
                $tablaEducacion->addRow();

                $tablaEducacion->addCell()->addText(
                    (string) $this->valorSeleccionado(
                        $config,
                        'atestados',
                        $item,
                        'titulo'
                    )
                );

                $tablaEducacion->addCell()->addText(
                    (string) $this->valorSeleccionado(
                        $config,
                        'atestados',
                        $item,
                        'institucion'
                    )
                );

                $tablaEducacion->addCell()->addText(
                    (string) $this->valorSeleccionado(
                        $config,
                        'atestados',
                        $item,
                        'fecha_fin'
                    )
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Asociaciones / áreas / habilidades
        |--------------------------------------------------------------------------
        */

        $section->addTextBreak();

        $section->addText(
            'Asociaciones profesionales a las que pertenece:',
            [
                'bold' => true,
                'size' => 12,
            ]
        );

        $asociaciones = collect($cvData['areas'] ?? [])
            ->filter(
                fn ($area) =>
                    $this->seleccionado(
                        $config,
                        'areas',
                        $area['id'],
                        'nombre'
                    )
            )
            ->map(function ($area) use ($cvData, $config) {

                $atestado = collect($cvData['atestados'] ?? [])
                    ->first(
                        fn ($item) =>
                            (int) $item['id'] ===
                            (int) ($area['id_atestado'] ?? 0)
                    );

                $habilidades = collect(
                    $area['habilidades'] ?? []
                )
                    ->filter(
                        fn ($habilidad) =>
                            $this->seleccionado(
                                $config,
                                'habilidades_area_' . $area['id'],
                                $habilidad['id'],
                                'nombre'
                            )
                    )
                    ->pluck('nombre')
                    ->filter()
                    ->unique()
                    ->values();

                return [
                    'institucion' =>
                        $atestado['institucion'] ?? '',

                    'area' =>
                        $area['nombre'] ?? '',

                    'habilidades' =>
                        $habilidades,
                ];
            })
            ->filter(
                fn ($item) =>
                    $item['institucion'] ||
                    $item['area'] ||
                    $item['habilidades']->count()
            );

        $tablaAsociaciones = $section->addTable([
            'borderSize' => 6,
            'cellMargin' => 80,
            'width' => 100,
            'unit' => 'pct',
        ]);

        $tablaAsociaciones->addRow();

        foreach (
            [
                'Institución',
                'Área de especialización',
                'Habilidad técnica',
            ] as $encabezado
        ) {
            $tablaAsociaciones
                ->addCell()
                ->addText(
                    $encabezado,
                    ['bold' => true]
                );
        }

        if ($asociaciones->isEmpty()) {
            $tablaAsociaciones->addRow();

            $tablaAsociaciones->addCell()->addText('');
            $tablaAsociaciones->addCell()->addText('');
            $tablaAsociaciones->addCell()->addText('');
        } else {
            foreach ($asociaciones as $item) {
                $tablaAsociaciones->addRow();

                $tablaAsociaciones
                    ->addCell(2200)
                    ->addText(
                        (string) $item['institucion']
                    );

                $tablaAsociaciones
                    ->addCell(3600)
                    ->addText(
                        (string) $item['area']
                    );

                $tablaAsociaciones
                    ->addCell(4200)
                    ->addText(
                        $item['habilidades']->implode("\n")
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Otros estudios
        |--------------------------------------------------------------------------
        */

        $section->addTextBreak();

        $section->addText(
            'Otros estudios:',
            [
                'bold' => true,
                'size' => 12,
            ]
        );

        $idsOtrosEstudios = array_merge(
            $tipoFormacion['acreditacion'] ?? [],
            $tipoFormacion['educacion_continua'] ?? []
        );

        $otrosEstudios = collect($cvData['atestados'] ?? [])
            ->filter(
                fn ($item) =>
                    in_array(
                        (int) ($item['id_tipo_formacion'] ?? 0),
                        $idsOtrosEstudios,
                        true
                    )
            )
            ->filter(
                fn ($item) =>
                    $this->itemVisible(
                        $config,
                        'atestados',
                        $item,
                        [
                            'titulo',
                            'institucion',
                            'fecha_inicio',
                            'fecha_fin',
                            'archivo_url',
                        ]
                    )
            );

        $tablaOtrosEstudios = $section->addTable([
            'borderSize' => 6,
            'cellMargin' => 80,
            'width' => 100,
            'unit' => 'pct',
        ]);

        $tablaOtrosEstudios->addRow();

        $tablaOtrosEstudios
            ->addCell(4200)
            ->addText(
                'Nombre del curso/seminario',
                ['bold' => true]
            );

        $tablaOtrosEstudios
            ->addCell(3500)
            ->addText(
                'Institución que lo impartió',
                ['bold' => true]
            );

        $tablaOtrosEstudios
            ->addCell(2300)
            ->addText(
                'Fecha de estudios',
                ['bold' => true]
            );

        if ($otrosEstudios->isEmpty()) {

            $tablaOtrosEstudios->addRow();

            $tablaOtrosEstudios->addCell(4200)->addText('');
            $tablaOtrosEstudios->addCell(3500)->addText('');
            $tablaOtrosEstudios->addCell(2300)->addText('');

        } else {

            foreach ($otrosEstudios as $item) {

                $tablaOtrosEstudios->addRow();

                $tablaOtrosEstudios
                    ->addCell(4200)
                    ->addText(
                        (string) $this->valorSeleccionado(
                            $config,
                            'atestados',
                            $item,
                            'titulo'
                        )
                    );

                $tablaOtrosEstudios
                    ->addCell(3500)
                    ->addText(
                        (string) $this->valorSeleccionado(
                            $config,
                            'atestados',
                            $item,
                            'institucion'
                        )
                    );

                $tablaOtrosEstudios
                    ->addCell(2300)
                    ->addText(
                        (string) $this->valorSeleccionado(
                            $config,
                            'atestados',
                            $item,
                            'fecha_fin'
                        )
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Historia laboral
        |--------------------------------------------------------------------------
        */

        $section->addTextBreak();

        $section->addText(
            'Historia laboral:',
            [
                'bold' => true,
                'size' => 12,
            ]
        );

        $experiencias = collect($cvData['experiencias'] ?? [])
            ->filter(
                fn ($item) =>
                    $this->itemVisible(
                        $config,
                        'experiencias',
                        $item,
                        [
                            'desde',
                            'hasta',
                            'empresa',
                            'cargo',
                        ]
                    )
            );

        $tablaExperiencias = $section->addTable([
            'borderSize' => 6,
            'cellMargin' => 80,
            'width' => 100,
            'unit' => 'pct',
        ]);

        $tablaExperiencias->addRow();

        $tablaExperiencias
            ->addCell(1800)
            ->addText('Desde', ['bold' => true]);

        $tablaExperiencias
            ->addCell(1800)
            ->addText('Hasta', ['bold' => true]);

        $tablaExperiencias
            ->addCell(3200)
            ->addText('Empresa', ['bold' => true]);

        $tablaExperiencias
            ->addCell(3200)
            ->addText('Cargos desempeñados', ['bold' => true]);

        if ($experiencias->isEmpty()) {
            $tablaExperiencias->addRow();

            $tablaExperiencias->addCell(1800)->addText('');
            $tablaExperiencias->addCell(1800)->addText('');
            $tablaExperiencias->addCell(3200)->addText('');
            $tablaExperiencias->addCell(3200)->addText('');
        } else {
            foreach ($experiencias as $item) {
                $tablaExperiencias->addRow();

                $tablaExperiencias
                    ->addCell(1800)
                    ->addText(
                        (string) $this->valorSeleccionado(
                            $config,
                            'experiencias',
                            $item,
                            'desde'
                        )
                    );

                $tablaExperiencias
                    ->addCell(1800)
                    ->addText(
                        (string) $this->valorSeleccionado(
                            $config,
                            'experiencias',
                            $item,
                            'hasta'
                        )
                    );

                $tablaExperiencias
                    ->addCell(3200)
                    ->addText(
                        (string) $this->valorSeleccionado(
                            $config,
                            'experiencias',
                            $item,
                            'empresa'
                        )
                    );

                $tablaExperiencias
                    ->addCell(3200)
                    ->addText(
                        (string) $this->valorSeleccionado(
                            $config,
                            'experiencias',
                            $item,
                            'cargo'
                        )
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Experiencia en consultorías y gestión de proyectos
        |--------------------------------------------------------------------------
        */

        $section->addTextBreak();

        $section->addText(
            'Experiencia en consultorías y gestión de proyectos:',
            [
                'bold' => true,
                'size' => 12,
            ]
        );

        $idsConsultorias = array_merge(
            $tipoFormacion['capacitacion_impartida'] ?? [],
            $tipoFormacion['capacitacion_recibida'] ?? [],
            $tipoFormacion['consultoria_realizada'] ?? []
        );

        $consultorias = collect($cvData['atestados'] ?? [])
            ->filter(
                fn ($item) =>
                    in_array(
                        (int) ($item['id_tipo_formacion'] ?? 0),
                        $idsConsultorias,
                        true
                    )
            )
            ->filter(
                fn ($item) =>
                    $this->itemVisible(
                        $config,
                        'atestados',
                        $item,
                        [
                            'titulo',
                            'institucion',
                            'fecha_inicio',
                            'fecha_fin',
                            'archivo_url',
                        ]
                    )
            );

        $tablaConsultorias = $section->addTable([
            'borderSize' => 6,
            'cellMargin' => 80,
            'width' => 100,
            'unit' => 'pct',
        ]);

        $tablaConsultorias->addRow();

        $tablaConsultorias
            ->addCell(4000)
            ->addText(
                'Consultorías / capacitaciones',
                ['bold' => true]
            );

        $tablaConsultorias
            ->addCell(3700)
            ->addText(
                'Empresa / organización',
                ['bold' => true]
            );

        $tablaConsultorias
            ->addCell(2300)
            ->addText(
                'Fecha',
                ['bold' => true]
            );

        if ($consultorias->isEmpty()) {
            $tablaConsultorias->addRow();

            $tablaConsultorias->addCell(4000)->addText('');
            $tablaConsultorias->addCell(3700)->addText('');
            $tablaConsultorias->addCell(2300)->addText('');
        } else {
            foreach ($consultorias as $item) {
                $tablaConsultorias->addRow();

                $tablaConsultorias
                    ->addCell(4000)
                    ->addText(
                        (string) $this->valorSeleccionado(
                            $config,
                            'atestados',
                            $item,
                            'titulo'
                        )
                    );

                $tablaConsultorias
                    ->addCell(3700)
                    ->addText(
                        (string) $this->valorSeleccionado(
                            $config,
                            'atestados',
                            $item,
                            'institucion'
                        )
                    );

                $tablaConsultorias
                    ->addCell(2300)
                    ->addText(
                        (string) $this->valorSeleccionado(
                            $config,
                            'atestados',
                            $item,
                            'fecha_fin'
                        )
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Experiencia como facilitador/a
        |--------------------------------------------------------------------------
        */

        $section->addTextBreak();

        $section->addText(
            'Experiencia como facilitador/a:',
            [
                'bold' => true,
                'size' => 12,
            ]
        );

        $capacitacionesFepade = collect(
            $cvData['capacitaciones_fepade'] ?? []
        )->filter(
            fn ($item) =>
                $this->itemVisible(
                    $config,
                    'capacitaciones_fepade',
                    $item,
                    [
                        'curso_nombre',
                        'fecha_inicio',
                        'fecha_fin',
                        'cliente',
                    ]
                )
        );

        $tablaCapacitaciones = $section->addTable([
            'borderSize' => 6,
            'cellMargin' => 80,
            'width' => 100,
            'unit' => 'pct',
        ]);

        $tablaCapacitaciones->addRow();

        $tablaCapacitaciones
            ->addCell(3400)
            ->addText(
                'Nombre de la capacitación',
                ['bold' => true]
            );

        $tablaCapacitaciones
            ->addCell(1800)
            ->addText(
                'Fecha inicio',
                ['bold' => true]
            );

        $tablaCapacitaciones
            ->addCell(1800)
            ->addText(
                'Fecha fin',
                ['bold' => true]
            );

        $tablaCapacitaciones
            ->addCell(3000)
            ->addText(
                'Empresa a quien se impartió',
                ['bold' => true]
            );

        if ($capacitacionesFepade->isEmpty()) {
            $tablaCapacitaciones->addRow();

            $tablaCapacitaciones->addCell(3400)->addText('');
            $tablaCapacitaciones->addCell(1800)->addText('');
            $tablaCapacitaciones->addCell(1800)->addText('');
            $tablaCapacitaciones->addCell(3000)->addText('');
        } else {
            foreach ($capacitacionesFepade as $item) {
                $tablaCapacitaciones->addRow();

                $tablaCapacitaciones
                    ->addCell(3400)
                    ->addText(
                        (string) $this->valorSeleccionado(
                            $config,
                            'capacitaciones_fepade',
                            $item,
                            'curso_nombre'
                        )
                    );

                $tablaCapacitaciones
                    ->addCell(1800)
                    ->addText(
                        (string) $this->valorSeleccionado(
                            $config,
                            'capacitaciones_fepade',
                            $item,
                            'fecha_inicio'
                        )
                    );

                $tablaCapacitaciones
                    ->addCell(1800)
                    ->addText(
                        (string) $this->valorSeleccionado(
                            $config,
                            'capacitaciones_fepade',
                            $item,
                            'fecha_fin'
                        )
                    );

                $tablaCapacitaciones
                    ->addCell(3000)
                    ->addText(
                        (string) $this->valorSeleccionado(
                            $config,
                            'capacitaciones_fepade',
                            $item,
                            'cliente'
                        )
                    );
            }
        }


        /*
        |--------------------------------------------------------------------------
        | Información de contacto
        |--------------------------------------------------------------------------
        */

        $emails = collect($cvData['emails'] ?? [])
            ->filter(
                fn ($item) =>
                    $this->itemVisible(
                        $config,
                        'emails',
                        $item,
                        ['email']
                    )
            );

        $telefonos = collect($cvData['telefonos'] ?? [])
            ->filter(
                fn ($item) =>
                    $this->itemVisible(
                        $config,
                        'telefonos',
                        $item,
                        [
                            'tipo',
                            'extension',
                            'numero',
                        ]
                    )
            );

        if ($emails->isNotEmpty() || $telefonos->isNotEmpty()) {

            $section->addTextBreak();

            $section->addText(
                'Información de contacto:',
                [
                    'bold' => true,
                    'size' => 12,
                ]
            );

            if ($emails->isNotEmpty()) {

                $section->addText(
                    'Correo:',
                    [
                        'bold' => true,
                    ]
                );

                foreach ($emails as $item) {
                    $email = (string) $this->valorSeleccionado(
                        $config,
                        'emails',
                        $item,
                        'email'
                    );

                    if ($email !== '') {
                        $section->addText($email);
                    }
                }
            }

            if ($telefonos->isNotEmpty()) {

                $section->addText(
                    'Teléfono:',
                    [
                        'bold' => true,
                    ]
                );

                foreach ($telefonos as $item) {

                    $tipo = (string) $this->valorSeleccionado(
                        $config,
                        'telefonos',
                        $item,
                        'tipo'
                    );

                    $extension = (string) $this->valorSeleccionado(
                        $config,
                        'telefonos',
                        $item,
                        'extension'
                    );

                    $numero = (string) $this->valorSeleccionado(
                        $config,
                        'telefonos',
                        $item,
                        'numero'
                    );

                    $telefono = '';

                    if ($tipo !== '') {
                        $telefono .= $tipo . ': ';
                    }

                    if ($extension !== '') {
                        $telefono .= '(' . $extension . ') ';
                    }

                    $telefono .= $numero;

                    if (trim($telefono) !== '') {
                        $section->addText(
                            trim($telefono)
                        );
                    }
                }
            }
        }

        return $phpWord;
    }

    private function construirWordResumenPersonal(
        Consultor $consultor,
        array $cvData,
        array $config
    ): \PhpOffice\PhpWord\PhpWord {

        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(9);

        $section = $this->nuevaSeccionWord($phpWord);

        $tipoFormacion = $this->mapaTipoFormacion();

        $nombreConsultoria = trim(
            (string) data_get(
                $config,
                'campos_manual.nombre_consultoria',
                ''
            )
        );

        $cargoPropuesto = trim(
            (string) data_get(
                $config,
                'campos_manual.cargo_propuesto',
                ''
            )
        );

        $actividades = trim(
            (string) data_get(
                $config,
                'campos_manual.actividades_consultoria',
                ''
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Datos
        |--------------------------------------------------------------------------
        */

        $idsEducacion = $tipoFormacion['educacion_formal'] ?? [];

        $educacion = collect($cvData['atestados'] ?? [])
            ->filter(
                fn ($item) =>
                    in_array(
                        (int) ($item['id_tipo_formacion'] ?? 0),
                        $idsEducacion,
                        true
                    )
            )
            ->filter(
                fn ($item) =>
                    $this->itemVisible(
                        $config,
                        'atestados',
                        $item,
                        ['titulo', 'institucion', 'fecha_fin']
                    )
            );

        $idsEspecialidades = array_merge(
            $tipoFormacion['acreditacion'] ?? [],
            $tipoFormacion['educacion_continua'] ?? [],
            $tipoFormacion['capacitacion_recibida'] ?? []
        );

        $especialidades = collect($cvData['atestados'] ?? [])
            ->filter(
                fn ($item) =>
                    in_array(
                        (int) ($item['id_tipo_formacion'] ?? 0),
                        $idsEspecialidades,
                        true
                    )
            )
            ->filter(
                fn ($item) =>
                    $this->itemVisible(
                        $config,
                        'atestados',
                        $item,
                        ['titulo', 'institucion']
                    )
            );

        $experiencias = collect($cvData['experiencias'] ?? [])
            ->filter(
                fn ($item) =>
                    $this->itemVisible(
                        $config,
                        'experiencias',
                        $item,
                        ['empresa', 'cargo', 'desde', 'hasta']
                    )
            );

        $areas = collect($cvData['areas'] ?? [])
            ->filter(
                fn ($area) =>
                    $this->seleccionado(
                        $config,
                        'areas',
                        $area['id'],
                        'nombre'
                    )
                    && ! blank($area['nombre'] ?? null)
            );

        /*
        |--------------------------------------------------------------------------
        | Encabezado
        |--------------------------------------------------------------------------
        */

        $section->addText(
            'FEPADE',
            [
                'bold' => true,
                'size' => 10,
            ],
            [
                'alignment' => 'center',
                'spaceAfter' => 80,
            ]
        );

        $section->addText(
            'RESUMEN DEL CV DEL PERSONAL PROPUESTO',
            [
                'bold' => true,
                'size' => 16,
            ],
            [
                'alignment' => 'center',
                'spaceAfter' => 70,
            ]
        );

        $section->addText(
            'Documento de síntesis para oferta o consultoría',
            [
                'italic' => true,
                'size' => 9,
            ],
            [
                'alignment' => 'center',
                'spaceAfter' => 250,
            ]
        );

        /*
        |--------------------------------------------------------------------------
        | Tabla principal
        |--------------------------------------------------------------------------
        */

        $table = $section->addTable([
            'borderSize' => 6,
            'borderColor' => 'AAB2BD',
            'cellMargin' => 90,
            'width' => 100,
            'unit' => 'pct',
        ]);

        /*
        |--------------------------------------------------------------------------
        | Consultoría / Cargo
        |--------------------------------------------------------------------------
        */

        $table->addRow();

        $cell = $table->addCell(5000);
        $cell->addText(
            'CONSULTORÍA O PROCESO',
            ['bold' => true, 'size' => 8]
        );
        $cell->addText(
            $nombreConsultoria !== ''
                ? $nombreConsultoria
                : 'Pendiente de completar por FEPADE'
        );

        $cell = $table->addCell(5000);
        $cell->addText(
            'CARGO PROPUESTO',
            ['bold' => true, 'size' => 8]
        );
        $cell->addText(
            $cargoPropuesto !== ''
                ? $cargoPropuesto
                : 'Pendiente de completar por FEPADE'
        );

        /*
        |--------------------------------------------------------------------------
        | Identificación
        |--------------------------------------------------------------------------
        */

        $table->addRow();

        $table->addCell(
            10000,
            [
                'gridSpan' => 2,
                'bgColor' => '162536',
            ]
        )->addText(
            'IDENTIFICACIÓN',
            [
                'bold' => true,
                'color' => 'FFFFFF',
                'size' => 9,
            ]
        );

        $filasIdentificacion = [
            [
                'Nombre del Oferente',
                'Fundación Empresarial para el Desarrollo Educativo -FEPADE-',
            ],
            [
                'Nombre del profesional propuesto',
                $this->textoPersonalSeleccionado(
                    $config,
                    $cvData,
                    'nombre'
                ),
            ],
            [
                'Nacionalidad',
                $this->textoPersonalSeleccionado(
                    $config,
                    $cvData,
                    'nacionalidad'
                ) ?: 'No registrado',
            ],
        ];

        foreach ($filasIdentificacion as [$label, $value]) {
            $table->addRow();

            $table->addCell(3500)->addText(
                $label,
                ['bold' => true]
            );

            $table->addCell(6500)->addText(
                (string) $value
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Formación y experiencia
        |--------------------------------------------------------------------------
        */

        $table->addRow();

        $table->addCell(
            10000,
            [
                'gridSpan' => 2,
                'bgColor' => '162536',
            ]
        )->addText(
            'FORMACIÓN Y EXPERIENCIA',
            [
                'bold' => true,
                'color' => 'FFFFFF',
                'size' => 9,
            ]
        );

        /*
        | Educación
        */

        $table->addRow();

        $table->addCell(3500)->addText(
            'Educación',
            ['bold' => true]
        );

        $cell = $table->addCell(6500);

        if ($educacion->isEmpty()) {
            $cell->addText(
                'No registrado',
                ['italic' => true]
            );
        } else {
            foreach ($educacion as $item) {
                $titulo = (string) $this->valorSeleccionado(
                    $config,
                    'atestados',
                    $item,
                    'titulo'
                );

                $institucion = (string) $this->valorSeleccionado(
                    $config,
                    'atestados',
                    $item,
                    'institucion'
                );

                $fecha = (string) $this->valorSeleccionado(
                    $config,
                    'atestados',
                    $item,
                    'fecha_fin'
                );

                $texto = $titulo;

                if ($institucion !== '') {
                    $texto .= ' — ' . $institucion;
                }

                if ($fecha !== '') {
                    $texto .= ' (' . $fecha . ')';
                }

                if ($texto !== '') {
                    $cell->addText(
                        '• ' . $texto,
                        [],
                        ['spaceAfter' => 40]
                    );
                }
            }
        }

        /*
        | Asociaciones
        */

        $table->addRow();

        $table->addCell(3500)->addText(
            'Asociaciones profesionales a las que pertenece',
            ['bold' => true]
        );

        $table->addCell(6500)->addText(
            'No registrado',
            ['italic' => true]
        );

        /*
        | Otras especialidades
        */

        $table->addRow();

        $table->addCell(3500)->addText(
            'Otras especialidades',
            ['bold' => true]
        );

        $cell = $table->addCell(6500);

        $hayEspecialidades = false;

        foreach ($especialidades as $item) {
            $titulo = (string) $this->valorSeleccionado(
                $config,
                'atestados',
                $item,
                'titulo'
            );

            $institucion = (string) $this->valorSeleccionado(
                $config,
                'atestados',
                $item,
                'institucion'
            );

            $texto = $titulo;

            if ($institucion !== '') {
                $texto .= ' — ' . $institucion;
            }

            if ($texto !== '') {
                $cell->addText(
                    '• ' . $texto,
                    [],
                    ['spaceAfter' => 40]
                );

                $hayEspecialidades = true;
            }
        }

        foreach ($areas as $area) {
            $habilidades = collect(
                $area['habilidades'] ?? []
            )
                ->filter(
                    fn ($hab) =>
                        $this->seleccionado(
                            $config,
                            'habilidades_area_' . $area['id'],
                            $hab['id'],
                            'nombre'
                        )
                        && ! blank($hab['nombre'] ?? null)
                )
                ->pluck('nombre');

            $texto = (string) $area['nombre'];

            if ($habilidades->isNotEmpty()) {
                $texto .= ': ' . $habilidades->implode(', ');
            }

            $cell->addText(
                '• ' . $texto,
                [],
                ['spaceAfter' => 40]
            );

            $hayEspecialidades = true;
        }

        if (! $hayEspecialidades) {
            $cell->addText(
                'No registrado',
                ['italic' => true]
            );
        }

        /*
        | Países
        */

        $table->addRow();

        $table->addCell(3500)->addText(
            'Países donde tiene experiencia de trabajo',
            ['bold' => true]
        );

        $table->addCell(6500)->addText(
            'No registrado',
            ['italic' => true]
        );

        /*
        | Trabajos realizados
        */

        $table->addRow();

        $table->addCell(3500)->addText(
            'Trabajos que ha realizado',
            ['bold' => true]
        );

        $cell = $table->addCell(6500);

        if ($experiencias->isEmpty()) {
            $cell->addText(
                'No registrado',
                ['italic' => true]
            );
        } else {
            foreach ($experiencias as $item) {
                $cargo = (string) $this->valorSeleccionado(
                    $config,
                    'experiencias',
                    $item,
                    'cargo'
                );

                $empresa = (string) $this->valorSeleccionado(
                    $config,
                    'experiencias',
                    $item,
                    'empresa'
                );

                $desde = (string) $this->valorSeleccionado(
                    $config,
                    'experiencias',
                    $item,
                    'desde'
                );

                $hasta = (string) $this->valorSeleccionado(
                    $config,
                    'experiencias',
                    $item,
                    'hasta'
                );

                $texto = $cargo;

                if ($empresa !== '') {
                    $texto .= ' — ' . $empresa;
                }

                if ($desde !== '' || $hasta !== '') {
                    $texto .= ' (' . $desde;

                    if ($hasta !== '') {
                        $texto .= ' – ' . $hasta;
                    }

                    $texto .= ')';
                }

                if ($texto !== '') {
                    $cell->addText(
                        '• ' . $texto,
                        [],
                        ['spaceAfter' => 40]
                    );
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Asignación de consultoría
        |--------------------------------------------------------------------------
        */

        $table->addRow();

        $table->addCell(
            10000,
            [
                'gridSpan' => 2,
                'bgColor' => '162536',
            ]
        )->addText(
            'ASIGNACIÓN DE LA CONSULTORÍA',
            [
                'bold' => true,
                'color' => 'FFFFFF',
                'size' => 9,
            ]
        );

        $table->addRow();

        $table->addCell(3500)->addText(
            'Detalle de las actividades asignadas en esta consultoría',
            ['bold' => true]
        );

        $table->addCell(6500)->addText(
            $actividades !== ''
                ? $actividades
                : 'Pendiente de completar por FEPADE'
        );

        return $phpWord;
    }

    private function construirWordMineducytBirf(
        Consultor $consultor,
        array $cvData,
        array $config
    ): \PhpOffice\PhpWord\PhpWord {

        $phpWord = new \PhpOffice\PhpWord\PhpWord();
        $section = $this->nuevaSeccionWord($phpWord);

        $tipoFormacion = $this->mapaTipoFormacion();

        $cargoNumero = trim(
            (string) data_get(
                $config,
                'campos_manual.cargo_numero',
                ''
            )
        );

        $nombreConsultoria = trim(
            (string) data_get(
                $config,
                'campos_manual.nombre_consultoria',
                ''
            )
        );

        $tareas = trim(
            (string) data_get(
                $config,
                'campos_manual.tareas_asignadas',
                ''
            )
        );

        /*
        |--------------------------------------------------------------------------
        | Encabezado
        |--------------------------------------------------------------------------
        */

        $section->addText(
            'FEPADE',
            ['bold' => true, 'size' => 10],
            ['alignment' => 'center']
        );

        $section->addText(
            'CURRÍCULUM DEL PERSONAL PROPUESTO',
            ['bold' => true, 'size' => 16],
            ['alignment' => 'center']
        );

        $section->addText(
            'Formato CV MINEDUCYT / BIRF',
            ['italic' => true, 'size' => 9],
            ['alignment' => 'center']
        );

        $section->addTextBreak();

        $this->agregarLineaWord(
            $section,
            'Consultoría o proceso',
            $nombreConsultoria !== ''
                ? $nombreConsultoria
                : 'Pendiente de completar por FEPADE'
        );

        /*
        |--------------------------------------------------------------------------
        | 1. Información general
        |--------------------------------------------------------------------------
        */

        $this->tituloWord(
            $section,
            '1. Información general'
        );

        $tabla = $section->addTable([
            'borderSize' => 6,
            'cellMargin' => 80,
            'width' => 100,
            'unit' => 'pct',
        ]);

        $datos = [
            [
                'Nombre del cargo y número',
                $cargoNumero !== ''
                    ? $cargoNumero
                    : 'Pendiente de completar por FEPADE',
            ],
            [
                'Nombre del Experto',
                $this->textoPersonalSeleccionado(
                    $config,
                    $cvData,
                    'nombre'
                ),
            ],
            [
                'Fecha de nacimiento',
                $this->textoPersonalSeleccionado(
                    $config,
                    $cvData,
                    'fecha_nacimiento'
                ),
            ],
            [
                'País de ciudadanía/residencia',
                $this->textoPersonalSeleccionado(
                    $config,
                    $cvData,
                    'nacionalidad'
                ) ?:
                $this->textoPersonalSeleccionado(
                    $config,
                    $cvData,
                    'residencia'
                ),
            ],
        ];

        foreach ($datos as [$label, $value]) {
            $tabla->addRow();
            $tabla->addCell(3500)
                ->addText($label, ['bold' => true]);
            $tabla->addCell(6500)
                ->addText((string) $value);
        }

        /*
        |--------------------------------------------------------------------------
        | Educación
        |--------------------------------------------------------------------------
        */

        $educacion = collect($cvData['atestados'] ?? [])
            ->filter(
                fn ($item) =>
                    in_array(
                        (int) ($item['id_tipo_formacion'] ?? 0),
                        $tipoFormacion['educacion_formal'] ?? [],
                        true
                    )
            )
            ->filter(
                fn ($item) =>
                    $this->itemVisible(
                        $config,
                        'atestados',
                        $item,
                        [
                            'titulo',
                            'institucion',
                            'fecha_fin',
                        ]
                    )
            );

        $this->tituloWord($section, '2. Educación');

        $tablaEducacion = $section->addTable([
            'borderSize' => 6,
            'cellMargin' => 80,
            'width' => 100,
            'unit' => 'pct',
        ]);

        $tablaEducacion->addRow();
        $tablaEducacion->addCell(3800)
            ->addText('Título obtenido', ['bold' => true]);
        $tablaEducacion->addCell(3800)
            ->addText('Institución', ['bold' => true]);
        $tablaEducacion->addCell(2400)
            ->addText('Fecha de estudios', ['bold' => true]);

        if ($educacion->isEmpty()) {
            $tablaEducacion->addRow();
            $tablaEducacion->addCell(10000, [
                'gridSpan' => 3,
            ])->addText('No registrado', ['italic' => true]);
        } else {
            foreach ($educacion as $item) {
                $tablaEducacion->addRow();

                $tablaEducacion->addCell(3800)->addText(
                    (string) $this->valorSeleccionado(
                        $config,
                        'atestados',
                        $item,
                        'titulo'
                    )
                );

                $tablaEducacion->addCell(3800)->addText(
                    (string) $this->valorSeleccionado(
                        $config,
                        'atestados',
                        $item,
                        'institucion'
                    )
                );

                $tablaEducacion->addCell(2400)->addText(
                    (string) $this->valorSeleccionado(
                        $config,
                        'atestados',
                        $item,
                        'fecha_fin'
                    )
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Formación complementaria
        |--------------------------------------------------------------------------
        */

        $idsComplementaria = array_merge(
            $tipoFormacion['acreditacion'] ?? [],
            $tipoFormacion['educacion_continua'] ?? [],
            $tipoFormacion['capacitacion_recibida'] ?? []
        );

        $complementaria = collect($cvData['atestados'] ?? [])
            ->filter(
                fn ($item) =>
                    in_array(
                        (int) ($item['id_tipo_formacion'] ?? 0),
                        $idsComplementaria,
                        true
                    )
            )
            ->filter(
                fn ($item) =>
                    $this->itemVisible(
                        $config,
                        'atestados',
                        $item,
                        [
                            'titulo',
                            'institucion',
                            'fecha_fin',
                            'fecha_emision',
                        ]
                    )
            );

        $this->tituloWord(
            $section,
            '3. Formación complementaria'
        );

        $tablaComplementaria = $section->addTable([
            'borderSize' => 6,
            'cellMargin' => 80,
            'width' => 100,
            'unit' => 'pct',
        ]);

        $tablaComplementaria->addRow();
        $tablaComplementaria->addCell(4200)
            ->addText('Curso, seminario o certificación', ['bold' => true]);
        $tablaComplementaria->addCell(3500)
            ->addText('Institución', ['bold' => true]);
        $tablaComplementaria->addCell(2300)
            ->addText('Fecha', ['bold' => true]);

        if ($complementaria->isEmpty()) {
            $tablaComplementaria->addRow();
            $tablaComplementaria->addCell(
                10000,
                ['gridSpan' => 3]
            )->addText('No registrado', ['italic' => true]);
        } else {
            foreach ($complementaria as $item) {
                $tablaComplementaria->addRow();

                $tablaComplementaria->addCell(4200)->addText(
                    (string) $this->valorSeleccionado(
                        $config,
                        'atestados',
                        $item,
                        'titulo'
                    )
                );

                $tablaComplementaria->addCell(3500)->addText(
                    (string) $this->valorSeleccionado(
                        $config,
                        'atestados',
                        $item,
                        'institucion'
                    )
                );

                $fecha = (string) $this->valorSeleccionado(
                    $config,
                    'atestados',
                    $item,
                    'fecha_fin'
                );

                if ($fecha === '') {
                    $fecha = (string) $this->valorSeleccionado(
                        $config,
                        'atestados',
                        $item,
                        'fecha_emision'
                    );
                }

                $tablaComplementaria
                    ->addCell(2300)
                    ->addText($fecha);
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Experiencia laboral
        |--------------------------------------------------------------------------
        */

        $experiencias = collect($cvData['experiencias'] ?? [])
            ->filter(
                fn ($item) =>
                    $this->itemVisible(
                        $config,
                        'experiencias',
                        $item,
                        [
                            'empresa',
                            'cargo',
                            'descripcion',
                            'desde',
                            'hasta',
                            'jefe_nombre',
                            'jefe_email',
                            'jefe_telefono',
                        ]
                    )
            );

        $this->tituloWord(
            $section,
            '4. Experiencia laboral pertinente para el trabajo'
        );

        $tablaExperiencia = $section->addTable([
            'borderSize' => 6,
            'cellMargin' => 70,
            'width' => 100,
            'unit' => 'pct',
        ]);

        $tablaExperiencia->addRow();
        $tablaExperiencia->addCell(1900)
            ->addText('Período', ['bold' => true]);
        $tablaExperiencia->addCell(3500)
            ->addText('Entidad empleadora y referencias', ['bold' => true]);
        $tablaExperiencia->addCell(1800)
            ->addText('País', ['bold' => true]);
        $tablaExperiencia->addCell(2800)
            ->addText('Resumen', ['bold' => true]);

        if ($experiencias->isEmpty()) {
            $tablaExperiencia->addRow();
            $tablaExperiencia->addCell(
                10000,
                ['gridSpan' => 4]
            )->addText('No registrado', ['italic' => true]);
        } else {
            foreach ($experiencias as $item) {
                $desde = (string) $this->valorSeleccionado(
                    $config,
                    'experiencias',
                    $item,
                    'desde'
                );

                $hasta = (string) $this->valorSeleccionado(
                    $config,
                    'experiencias',
                    $item,
                    'hasta'
                );

                $periodo = $desde;

                if ($hasta !== '') {
                    $periodo .= ' – ' . $hasta;
                }

                $empresa = (string) $this->valorSeleccionado(
                    $config,
                    'experiencias',
                    $item,
                    'empresa'
                );

                $cargo = (string) $this->valorSeleccionado(
                    $config,
                    'experiencias',
                    $item,
                    'cargo'
                );

                $jefe = (string) $this->valorSeleccionado(
                    $config,
                    'experiencias',
                    $item,
                    'jefe_nombre'
                );

                $telefono = (string) $this->valorSeleccionado(
                    $config,
                    'experiencias',
                    $item,
                    'jefe_telefono'
                );

                $correo = (string) $this->valorSeleccionado(
                    $config,
                    'experiencias',
                    $item,
                    'jefe_email'
                );

                $entidad = trim($empresa . "\n" . $cargo);

                if ($jefe !== '') {
                    $entidad .= "\nReferencia: " . $jefe;
                }

                if ($telefono !== '') {
                    $entidad .= "\nTeléfono: " . $telefono;
                }

                if ($correo !== '') {
                    $entidad .= "\nCorreo: " . $correo;
                }

                $tablaExperiencia->addRow();

                $tablaExperiencia
                    ->addCell(1900)
                    ->addText($periodo);

                $tablaExperiencia
                    ->addCell(3500)
                    ->addText($entidad);

                // Actualmente la tabla de experiencia laboral
                // no almacena país.
                $tablaExperiencia
                    ->addCell(1800)
                    ->addText('No registrado');

                $tablaExperiencia
                    ->addCell(2800)
                    ->addText(
                        (string) $this->valorSeleccionado(
                            $config,
                            'experiencias',
                            $item,
                            'descripcion'
                        )
                    );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Asociaciones y publicaciones
        |--------------------------------------------------------------------------
        */

        $this->tituloWord(
            $section,
            '5. Asociaciones profesionales y publicaciones'
        );

        $tablaAsociaciones = $section->addTable([
            'borderSize' => 6,
            'cellMargin' => 80,
            'width' => 100,
            'unit' => 'pct',
        ]);

        foreach ([
            'Asociaciones profesionales',
            'Publicaciones',
        ] as $label) {
            $tablaAsociaciones->addRow();
            $tablaAsociaciones
                ->addCell(3500)
                ->addText($label, ['bold' => true]);
            $tablaAsociaciones
                ->addCell(6500)
                ->addText('No registrado', ['italic' => true]);
        }

        /*
        |--------------------------------------------------------------------------
        | Idiomas
        |--------------------------------------------------------------------------
        */

        $idiomas = collect($cvData['idiomas'] ?? [])
            ->filter(
                fn ($item) =>
                    $this->itemVisible(
                        $config,
                        'idiomas',
                        $item,
                        [
                            'idioma',
                            'nivel',
                            'certificado_url',
                        ]
                    )
            );

        $this->tituloWord($section, '6. Idiomas');

        $tablaIdiomas = $section->addTable([
            'borderSize' => 6,
            'cellMargin' => 80,
            'width' => 100,
            'unit' => 'pct',
        ]);

        $tablaIdiomas->addRow();
        $tablaIdiomas->addCell(3500)
            ->addText('Idioma', ['bold' => true]);
        $tablaIdiomas->addCell(3000)
            ->addText('Nivel', ['bold' => true]);
        $tablaIdiomas->addCell(3500)
            ->addText('Certificado', ['bold' => true]);

        if ($idiomas->isEmpty()) {
            $tablaIdiomas->addRow();
            $tablaIdiomas->addCell(
                10000,
                ['gridSpan' => 3]
            )->addText('No registrado', ['italic' => true]);
        } else {
            foreach ($idiomas as $item) {
                $tablaIdiomas->addRow();

                $tablaIdiomas->addCell(3500)->addText(
                    (string) $this->valorSeleccionado(
                        $config,
                        'idiomas',
                        $item,
                        'idioma'
                    )
                );

                $tablaIdiomas->addCell(3000)->addText(
                    (string) $this->valorSeleccionado(
                        $config,
                        'idiomas',
                        $item,
                        'nivel'
                    )
                );

                $certificado = (string) $this->valorSeleccionado(
                    $config,
                    'idiomas',
                    $item,
                    'certificado_url'
                );

                $tablaIdiomas->addCell(3500)->addText(
                    $certificado !== ''
                        ? $certificado
                        : 'No registrado'
                );
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Idoneidad
        |--------------------------------------------------------------------------
        */

        $this->tituloWord(
            $section,
            '7. Idoneidad para el trabajo'
        );

        $tablaIdoneidad = $section->addTable([
            'borderSize' => 6,
            'cellMargin' => 80,
            'width' => 100,
            'unit' => 'pct',
        ]);

        $tablaIdoneidad->addRow();
        $tablaIdoneidad->addCell(3500)
            ->addText('Tareas detalladas asignadas', ['bold' => true]);
        $tablaIdoneidad->addCell(6500)->addText(
            $tareas !== ''
                ? $tareas
                : 'Pendiente de completar por FEPADE'
        );

        $areas = collect($cvData['areas'] ?? [])
            ->filter(
                fn ($area) =>
                    $this->seleccionado(
                        $config,
                        'areas',
                        $area['id'],
                        'nombre'
                    ) &&
                    ! blank($area['nombre'] ?? null)
            );

        $tablaIdoneidad->addRow();
        $tablaIdoneidad->addCell(3500)
            ->addText(
                'Áreas de especialización relevantes',
                ['bold' => true]
            );

        $tablaIdoneidad->addCell(6500)->addText(
            $areas->isNotEmpty()
                ? $areas->pluck('nombre')->implode(', ')
                : 'No registrado'
        );

        $habilidades = collect();

        foreach ($areas as $area) {
            foreach ($area['habilidades'] ?? [] as $hab) {
                if (
                    $this->seleccionado(
                        $config,
                        'habilidades_area_' . $area['id'],
                        $hab['id'],
                        'nombre'
                    ) &&
                    ! blank($hab['nombre'] ?? null)
                ) {
                    $habilidades->push($hab['nombre']);
                }
            }
        }

        $tablaIdoneidad->addRow();
        $tablaIdoneidad->addCell(3500)
            ->addText(
                'Habilidades técnicas relacionadas',
                ['bold' => true]
            );

        $tablaIdoneidad->addCell(6500)->addText(
            $habilidades->isNotEmpty()
                ? $habilidades->unique()->implode(', ')
                : 'No registrado'
        );

        /*
        |--------------------------------------------------------------------------
        | Contacto
        |--------------------------------------------------------------------------
        */

        $this->tituloWord(
            $section,
            '8. Información de contacto'
        );

        $emails = collect($cvData['emails'] ?? [])
            ->filter(
                fn ($item) =>
                    $this->itemVisible(
                        $config,
                        'emails',
                        $item,
                        ['email']
                    )
            );

        $telefonos = collect($cvData['telefonos'] ?? [])
            ->filter(
                fn ($item) =>
                    $this->itemVisible(
                        $config,
                        'telefonos',
                        $item,
                        ['numero']
                    )
            );

        if ($emails->isEmpty() && $telefonos->isEmpty()) {
            $section->addText(
                'No registrado',
                ['italic' => true]
            );
        }

        foreach ($emails as $item) {
            $this->agregarLineaWord(
                $section,
                'Correo',
                (string) $this->valorSeleccionado(
                    $config,
                    'emails',
                    $item,
                    'email'
                )
            );
        }

        foreach ($telefonos as $item) {
            $this->agregarLineaWord(
                $section,
                'Teléfono',
                (string) $this->valorSeleccionado(
                    $config,
                    'telefonos',
                    $item,
                    'numero'
                )
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Certificación y firmas
        |--------------------------------------------------------------------------
        */

        $this->tituloWord(
            $section,
            '9. Certificación'
        );

        $section->addText(
            'Yo, la/el abajo firmante, certifico que este currículum describe correctamente mi persona, mis calificaciones y mi experiencia.',
            [],
            [
                'alignment' => 'both',
                'spaceAfter' => 350,
            ]
        );

        $section->addTextBreak();

        /*
        | Firma del consultor
        */

        $tablaFirmaConsultor = $section->addTable([
            'width' => 100,
            'unit' => 'pct',
            'cellMargin' => 80,
        ]);

        $tablaFirmaConsultor->addRow(700);

        $cell = $tablaFirmaConsultor->addCell(
            6500,
            [
                'borderBottomSize' => 8,
                'borderBottomColor' => '444444',
            ]
        );

        $cell->addText('');

        $cell = $tablaFirmaConsultor->addCell(500);
        $cell->addText('');

        $cell = $tablaFirmaConsultor->addCell(
            3000,
            [
                'borderBottomSize' => 8,
                'borderBottomColor' => '444444',
            ]
        );

        $cell->addText('');

        $tablaFirmaConsultor->addRow();

        $cell = $tablaFirmaConsultor->addCell(6500);

        $cell->addText(
            'Firma del Consultor/a',
            [
                'bold' => true,
                'size' => 9,
            ],
            ['alignment' => 'center']
        );

        $cell = $tablaFirmaConsultor->addCell(500);
        $cell->addText('');

        $cell = $tablaFirmaConsultor->addCell(3000);

        $cell->addText(
            'Fecha',
            [
                'bold' => true,
                'size' => 9,
            ],
            ['alignment' => 'center']
        );

        $nombreConsultor = $this->textoPersonalSeleccionado(
            $config,
            $cvData,
            'nombre'
        );

        if ($nombreConsultor !== '') {
            $section->addText(
                $nombreConsultor,
                [
                    'bold' => true,
                    'size' => 9,
                ],
                [
                    'alignment' => 'center',
                    'spaceBefore' => 80,
                    'spaceAfter' => 400,
                ]
            );
        }

        $section->addTextBreak();

        /*
        | Firma representante FEPADE
        */

        $tablaFirmaFepade = $section->addTable([
            'width' => 100,
            'unit' => 'pct',
            'cellMargin' => 80,
        ]);

        $tablaFirmaFepade->addRow(700);

        $cell = $tablaFirmaFepade->addCell(
            6500,
            [
                'borderBottomSize' => 8,
                'borderBottomColor' => '444444',
            ]
        );

        $cell->addText('');

        $cell = $tablaFirmaFepade->addCell(500);
        $cell->addText('');

        $cell = $tablaFirmaFepade->addCell(
            3000,
            [
                'borderBottomSize' => 8,
                'borderBottomColor' => '444444',
            ]
        );

        $cell->addText('');

        $tablaFirmaFepade->addRow();

        $cell = $tablaFirmaFepade->addCell(6500);

        $cell->addText(
            'Ana María Porras de Bardi',
            [
                'bold' => true,
                'size' => 9,
            ],
            ['alignment' => 'center']
        );

        $cell->addText(
            'Directora Ejecutiva y Representante Legal',
            ['size' => 8],
            ['alignment' => 'center']
        );

        $cell->addText(
            'Fundación Empresarial para el Desarrollo Educativo - FEPADE',
            ['size' => 8],
            ['alignment' => 'center']
        );

        $cell = $tablaFirmaFepade->addCell(500);
        $cell->addText('');

        $cell = $tablaFirmaFepade->addCell(3000);

        $cell->addText(
            'Fecha',
            [
                'bold' => true,
                'size' => 9,
            ],
            ['alignment' => 'center']
        );

        return $phpWord;
    }

    private function construirWordProfesional(
        Consultor $consultor,
        array $cvData,
        array $config
    ): \PhpOffice\PhpWord\PhpWord {

        $phpWord = new \PhpOffice\PhpWord\PhpWord();

        $phpWord->setDefaultFontName('Arial');
        $phpWord->setDefaultFontSize(9);

        $section = $this->nuevaSeccionWord($phpWord);

        $nombre = $this->textoPersonalSeleccionado(
            $config,
            $cvData,
            'nombre'
        );

        $nacionalidad = $this->textoPersonalSeleccionado(
            $config,
            $cvData,
            'nacionalidad'
        );

        $residencia = $this->textoPersonalSeleccionado(
            $config,
            $cvData,
            'residencia_completa'
        );

        $fechaNacimiento = $this->textoPersonalSeleccionado(
            $config,
            $cvData,
            'fecha_nacimiento'
        );

        /*
        |--------------------------------------------------------------------------
        | Encabezado profesional + fotografía
        |--------------------------------------------------------------------------
        */

        $header = $section->addTable([
            'width' => 100,
            'unit' => 'pct',
            'cellMargin' => 120,
        ]);

        $header->addRow();

        /*
        | Columna información
        */

        $cellInfo = $header->addCell(
            7500,
            [
                'bgColor' => '162536',
                'valign' => 'center',
            ]
        );

        $cellInfo->addText(
            'CONSULTORES FEPADE 2026',
            [
                'bold' => true,
                'size' => 8,
                'color' => '6ED5C3',
            ],
            [
                'spaceAfter' => 100,
            ]
        );

        $cellInfo->addText(
            $nombre !== ''
                ? mb_strtoupper($nombre)
                : 'CONSULTOR FEPADE',
            [
                'bold' => true,
                'size' => 18,
                'color' => 'FFFFFF',
            ],
            [
                'spaceAfter' => 80,
            ]
        );

        $cellInfo->addText(
            'Consultor/a profesional FEPADE',
            [
                'italic' => true,
                'size' => 10,
                'color' => 'DDE5EC',
            ],
            [
                'spaceAfter' => 180,
            ]
        );

        if ($nacionalidad !== '') {
            $run = $cellInfo->addTextRun([
                'spaceAfter' => 50,
            ]);

            $run->addText(
                'Nacionalidad: ',
                [
                    'bold' => true,
                    'color' => 'FFFFFF',
                ]
            );

            $run->addText(
                $nacionalidad,
                ['color' => 'FFFFFF']
            );
        }

        if ($residencia !== '') {
            $run = $cellInfo->addTextRun([
                'spaceAfter' => 50,
            ]);

            $run->addText(
                'Residencia: ',
                [
                    'bold' => true,
                    'color' => 'FFFFFF',
                ]
            );

            $run->addText(
                $residencia,
                ['color' => 'FFFFFF']
            );
        }

        if ($fechaNacimiento !== '') {
            $run = $cellInfo->addTextRun();

            $run->addText(
                'Nacimiento: ',
                [
                    'bold' => true,
                    'color' => 'FFFFFF',
                ]
            );

            $run->addText(
                $fechaNacimiento,
                ['color' => 'FFFFFF']
            );
        }

        /*
        |--------------------------------------------------------------------------
        | Columna fotografía
        |--------------------------------------------------------------------------
        */

        $cellFoto = $header->addCell(
            2500,
            [
                'bgColor' => 'EDF1F4',
                'valign' => 'center',
            ]
        );

        $fotoSeleccionada = $this->seleccionado(
            $config,
            'personal',
            'personal',
            'foto'
        );

        $fotoPath = data_get(
            $cvData,
            'personal.foto_pdf'
        );

        if ($fotoSeleccionada) {

            if (
                $fotoPath &&
                is_string($fotoPath) &&
                file_exists($fotoPath)
            ) {
                try {
                    $cellFoto->addImage(
                        $fotoPath,
                        [
                            'width' => 105,
                            'height' => 125,
                            'alignment' => 'center',
                            'wrappingStyle' => 'inline',
                        ]
                    );
                } catch (\Throwable $e) {
                    $cellFoto->addText(
                        'Fotografía no disponible',
                        [
                            'italic' => true,
                            'size' => 8,
                            'color' => '7F8C8D',
                        ],
                        [
                            'alignment' => 'center',
                        ]
                    );
                }
            } else {
                $cellFoto->addText(
                    'Fotografía no disponible',
                    [
                        'italic' => true,
                        'size' => 8,
                        'color' => '7F8C8D',
                    ],
                    [
                        'alignment' => 'center',
                    ]
                );
            }

        }

        /*
        |--------------------------------------------------------------------------
        | Contacto
        |--------------------------------------------------------------------------
        */

        $this->tituloWord(
            $section,
            'INFORMACIÓN DE CONTACTO'
        );

        $contacto = $section->addTable([
            'borderSize' => 4,
            'borderColor' => 'D5DBDB',
            'cellMargin' => 90,
            'width' => 100,
            'unit' => 'pct',
        ]);

        $emails = collect($cvData['emails'] ?? [])
            ->filter(
                fn ($item) =>
                    $this->itemVisible(
                        $config,
                        'emails',
                        $item,
                        ['email']
                    )
            );

        $telefonos = collect($cvData['telefonos'] ?? [])
            ->filter(
                fn ($item) =>
                    $this->itemVisible(
                        $config,
                        'telefonos',
                        $item,
                        ['tipo', 'extension', 'numero']
                    )
            );

        $emailText = $emails
            ->map(
                fn ($item) =>
                    (string) $this->valorSeleccionado(
                        $config,
                        'emails',
                        $item,
                        'email'
                    )
            )
            ->filter()
            ->implode("\n");

        $telefonoText = $telefonos
            ->map(function ($item) use ($config) {
                $tipo = (string) $this->valorSeleccionado(
                    $config,
                    'telefonos',
                    $item,
                    'tipo'
                );

                $extension = (string) $this->valorSeleccionado(
                    $config,
                    'telefonos',
                    $item,
                    'extension'
                );

                $numero = (string) $this->valorSeleccionado(
                    $config,
                    'telefonos',
                    $item,
                    'numero'
                );

                $texto = '';

                if ($tipo !== '') {
                    $texto .= $tipo . ': ';
                }

                if ($extension !== '') {
                    $texto .= '(' . $extension . ') ';
                }

                $texto .= $numero;

                return trim($texto);
            })
            ->filter()
            ->implode("\n");

        $direccion = $this->textoPersonalSeleccionado(
            $config,
            $cvData,
            'direccion'
        );

        $contacto->addRow();

        $cell = $contacto->addCell(5000);
        $cell->addText(
            'Correo electrónico',
            ['bold' => true, 'size' => 8]
        );
        $cell->addText(
            $emailText !== ''
                ? $emailText
                : 'No registrado'
        );

        $cell = $contacto->addCell(5000);
        $cell->addText(
            'Teléfono',
            ['bold' => true, 'size' => 8]
        );
        $cell->addText(
            $telefonoText !== ''
                ? $telefonoText
                : 'No registrado'
        );

        if ($direccion !== '') {
            $contacto->addRow();

            $cell = $contacto->addCell(
                10000,
                ['gridSpan' => 2]
            );

            $cell->addText(
                'Dirección',
                ['bold' => true, 'size' => 8]
            );

            $cell->addText($direccion);
        }

        /*
        |--------------------------------------------------------------------------
        | Áreas de especialización
        |--------------------------------------------------------------------------
        */

        $areas = collect($cvData['areas'] ?? [])
            ->filter(
                fn ($area) =>
                    $this->seleccionado(
                        $config,
                        'areas',
                        $area['id'],
                        'nombre'
                    )
                    && ! blank($area['nombre'] ?? null)
            );

        if ($areas->isNotEmpty()) {
            $this->tituloWord(
                $section,
                'ÁREAS DE ESPECIALIZACIÓN'
            );

            $tablaAreas = $section->addTable([
                'borderSize' => 4,
                'borderColor' => 'D5DBDB',
                'cellMargin' => 90,
                'width' => 100,
                'unit' => 'pct',
            ]);

            foreach ($areas as $area) {
                $tablaAreas->addRow();

                $cell = $tablaAreas->addCell(
                    10000,
                    ['bgColor' => 'F5F7F8']
                );

                $cell->addText(
                    (string) $area['nombre'],
                    ['bold' => true]
                );

                $habilidades = collect(
                    $area['habilidades'] ?? []
                )
                    ->filter(
                        fn ($hab) =>
                            $this->seleccionado(
                                $config,
                                'habilidades_area_' . $area['id'],
                                $hab['id'],
                                'nombre'
                            )
                            && ! blank($hab['nombre'] ?? null)
                    )
                    ->pluck('nombre')
                    ->unique()
                    ->values();

                if ($habilidades->isNotEmpty()) {
                    $cell->addText(
                        $habilidades->implode('  •  '),
                        [
                            'size' => 8,
                            'color' => '566573',
                        ]
                    );
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Idiomas y disponibilidad
        |--------------------------------------------------------------------------
        */

        $idiomas = collect($cvData['idiomas'] ?? [])
            ->filter(
                fn ($item) =>
                    $this->itemVisible(
                        $config,
                        'idiomas',
                        $item,
                        ['idioma', 'nivel', 'certificado_url']
                    )
            );

        $disponibilidades = collect(
            $cvData['disponibilidades'] ?? []
        )->filter(
            fn ($item) =>
                $this->itemVisible(
                    $config,
                    'disponibilidades',
                    $item,
                    ['nombre']
                )
        );

        if (
            $idiomas->isNotEmpty() ||
            $disponibilidades->isNotEmpty()
        ) {
            $this->tituloWord(
                $section,
                'IDIOMAS Y DISPONIBILIDAD'
            );

            $tablaResumen = $section->addTable([
                'borderSize' => 4,
                'borderColor' => 'D5DBDB',
                'cellMargin' => 90,
                'width' => 100,
                'unit' => 'pct',
            ]);

            $tablaResumen->addRow();

            $cell = $tablaResumen->addCell(5000);

            $cell->addText(
                'Idiomas',
                ['bold' => true]
            );

            if ($idiomas->isEmpty()) {
                $cell->addText(
                    'No registrado',
                    ['italic' => true]
                );
            } else {
                foreach ($idiomas as $item) {
                    $idioma = (string) $this->valorSeleccionado(
                        $config,
                        'idiomas',
                        $item,
                        'idioma'
                    );

                    $nivel = (string) $this->valorSeleccionado(
                        $config,
                        'idiomas',
                        $item,
                        'nivel'
                    );

                    $texto = $idioma;

                    if ($nivel !== '') {
                        $texto .= ' — ' . $nivel;
                    }

                    if ($texto !== '') {
                        $cell->addText('• ' . $texto);
                    }
                }
            }

            $cell = $tablaResumen->addCell(5000);

            $cell->addText(
                'Disponibilidad',
                ['bold' => true]
            );

            if ($disponibilidades->isEmpty()) {
                $cell->addText(
                    'No registrado',
                    ['italic' => true]
                );
            } else {
                foreach ($disponibilidades as $item) {
                    $valor = (string) $this->valorSeleccionado(
                        $config,
                        'disponibilidades',
                        $item,
                        'nombre'
                    );

                    if ($valor !== '') {
                        $cell->addText('• ' . $valor);
                    }
                }
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Experiencia profesional
        |--------------------------------------------------------------------------
        */

        $experiencias = collect($cvData['experiencias'] ?? [])
            ->filter(
                fn ($item) =>
                    $this->itemVisible(
                        $config,
                        'experiencias',
                        $item,
                        [
                            'empresa',
                            'cargo',
                            'descripcion',
                            'desde',
                            'hasta',
                            'trabajo_actual',
                            'jefe_nombre',
                            'jefe_email',
                            'jefe_telefono',
                        ]
                    )
            );

        if ($experiencias->isNotEmpty()) {
            $this->tituloWord(
                $section,
                'EXPERIENCIA PROFESIONAL'
            );

            foreach ($experiencias as $item) {
                $cargo = (string) $this->valorSeleccionado(
                    $config,
                    'experiencias',
                    $item,
                    'cargo'
                );

                $empresa = (string) $this->valorSeleccionado(
                    $config,
                    'experiencias',
                    $item,
                    'empresa'
                );

                $desde = (string) $this->valorSeleccionado(
                    $config,
                    'experiencias',
                    $item,
                    'desde'
                );

                $hasta = (string) $this->valorSeleccionado(
                    $config,
                    'experiencias',
                    $item,
                    'hasta'
                );

                $descripcion = (string) $this->valorSeleccionado(
                    $config,
                    'experiencias',
                    $item,
                    'descripcion'
                );

                $box = $section->addTable([
                    'borderSize' => 4,
                    'borderColor' => 'D5DBDB',
                    'cellMargin' => 100,
                    'width' => 100,
                    'unit' => 'pct',
                ]);

                $box->addRow();

                $cell = $box->addCell(
                    10000,
                    ['bgColor' => 'F7F9FA']
                );

                if ($cargo !== '') {
                    $cell->addText(
                        $cargo,
                        [
                            'bold' => true,
                            'size' => 11,
                            'color' => '162536',
                        ]
                    );
                }

                if ($empresa !== '') {
                    $cell->addText(
                        $empresa,
                        ['bold' => true]
                    );
                }

                if ($desde !== '' || $hasta !== '') {
                    $cell->addText(
                        trim($desde . ' – ' . $hasta),
                        [
                            'italic' => true,
                            'size' => 8,
                            'color' => '6C757D',
                        ]
                    );
                }

                if ($descripcion !== '') {
                    $cell->addText(
                        $descripcion,
                        [],
                        ['spaceBefore' => 100]
                    );
                }

                $jefe = (string) $this->valorSeleccionado(
                    $config,
                    'experiencias',
                    $item,
                    'jefe_nombre'
                );

                $jefeTelefono = (string) $this->valorSeleccionado(
                    $config,
                    'experiencias',
                    $item,
                    'jefe_telefono'
                );

                $jefeEmail = (string) $this->valorSeleccionado(
                    $config,
                    'experiencias',
                    $item,
                    'jefe_email'
                );

                $referencia = array_filter([
                    $jefe !== ''
                        ? 'Referencia: ' . $jefe
                        : null,

                    $jefeTelefono !== ''
                        ? 'Tel. ' . $jefeTelefono
                        : null,

                    $jefeEmail !== ''
                        ? $jefeEmail
                        : null,
                ]);

                if ($referencia) {
                    $cell->addText(
                        implode(' | ', $referencia),
                        [
                            'size' => 8,
                            'color' => '566573',
                        ],
                        ['spaceBefore' => 100]
                    );
                }

                $section->addTextBreak();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Formación y atestados
        |--------------------------------------------------------------------------
        */

        $atestados = collect($cvData['atestados'] ?? [])
            ->filter(
                fn ($item) =>
                    $this->itemVisible(
                        $config,
                        'atestados',
                        $item,
                        [
                            'tipo_formacion',
                            'tipo_atestado',
                            'nivel',
                            'titulo',
                            'institucion',
                            'descripcion',
                            'pais',
                            'fecha_inicio',
                            'fecha_fin',
                            'fecha_emision',
                            'fecha_vencimiento',
                            'horas',
                            'archivo_url',
                        ]
                    )
            );

        if ($atestados->isNotEmpty()) {
            $this->tituloWord(
                $section,
                'FORMACIÓN, ATESTADOS Y TRAYECTORIA PROFESIONAL'
            );

            foreach ($atestados as $item) {
                $titulo = (string) $this->valorSeleccionado(
                    $config,
                    'atestados',
                    $item,
                    'titulo'
                );

                $institucion = (string) $this->valorSeleccionado(
                    $config,
                    'atestados',
                    $item,
                    'institucion'
                );

                $box = $section->addTable([
                    'borderSize' => 4,
                    'borderColor' => 'D5DBDB',
                    'cellMargin' => 100,
                    'width' => 100,
                    'unit' => 'pct',
                ]);

                $box->addRow();

                $cell = $box->addCell(10000);

                if ($titulo !== '') {
                    $cell->addText(
                        $titulo,
                        [
                            'bold' => true,
                            'size' => 11,
                            'color' => '162536',
                        ]
                    );
                }

                if ($institucion !== '') {
                    $cell->addText(
                        $institucion,
                        ['bold' => true]
                    );
                }

                $detalles = [];

                foreach (
                    [
                        'tipo_formacion',
                        'tipo_atestado',
                        'nivel',
                        'pais',
                    ] as $campo
                ) {
                    $valor = (string) $this->valorSeleccionado(
                        $config,
                        'atestados',
                        $item,
                        $campo
                    );

                    if ($valor !== '') {
                        $detalles[] = $valor;
                    }
                }

                $horas = (string) $this->valorSeleccionado(
                    $config,
                    'atestados',
                    $item,
                    'horas'
                );

                if ($horas !== '') {
                    $detalles[] = $horas . ' horas';
                }

                if ($detalles) {
                    $cell->addText(
                        implode(' | ', $detalles),
                        [
                            'size' => 8,
                            'color' => '566573',
                        ]
                    );
                }

                $fechaInicio = (string) $this->valorSeleccionado(
                    $config,
                    'atestados',
                    $item,
                    'fecha_inicio'
                );

                $fechaFin = (string) $this->valorSeleccionado(
                    $config,
                    'atestados',
                    $item,
                    'fecha_fin'
                );

                if ($fechaInicio !== '' || $fechaFin !== '') {
                    $cell->addText(
                        trim($fechaInicio . ' – ' . $fechaFin),
                        [
                            'italic' => true,
                            'size' => 8,
                        ]
                    );
                }

                $descripcion = (string) $this->valorSeleccionado(
                    $config,
                    'atestados',
                    $item,
                    'descripcion'
                );

                if ($descripcion !== '') {
                    $cell->addText(
                        $descripcion,
                        [],
                        ['spaceBefore' => 100]
                    );
                }

                $section->addTextBreak();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Capacitaciones FEPADE
        |--------------------------------------------------------------------------
        */

        $capacitaciones = collect(
            $cvData['capacitaciones_fepade'] ?? []
        )->filter(
            fn ($item) =>
                $this->itemVisible(
                    $config,
                    'capacitaciones_fepade',
                    $item,
                    [
                        'curso_nombre',
                        'cliente',
                        'modalidad',
                        'tipo_evento_nombre',
                        'estado_curso_nombre',
                        'fecha_inicio',
                        'fecha_fin',
                        'no_horas_real',
                        'fuente',
                    ]
                )
        );

        if ($capacitaciones->isNotEmpty()) {
            $this->tituloWord(
                $section,
                'CAPACITACIONES FEPADE'
            );

            foreach ($capacitaciones as $item) {
                $curso = (string) $this->valorSeleccionado(
                    $config,
                    'capacitaciones_fepade',
                    $item,
                    'curso_nombre'
                );

                $cliente = (string) $this->valorSeleccionado(
                    $config,
                    'capacitaciones_fepade',
                    $item,
                    'cliente'
                );

                $box = $section->addTable([
                    'borderSize' => 4,
                    'borderColor' => 'D5DBDB',
                    'cellMargin' => 100,
                    'width' => 100,
                    'unit' => 'pct',
                ]);

                $box->addRow();

                $cell = $box->addCell(10000);

                if ($curso !== '') {
                    $cell->addText(
                        $curso,
                        [
                            'bold' => true,
                            'size' => 11,
                            'color' => '162536',
                        ]
                    );
                }

                if ($cliente !== '') {
                    $cell->addText(
                        $cliente,
                        ['bold' => true]
                    );
                }

                $detalles = [];

                foreach (
                    [
                        'tipo_evento_nombre',
                        'modalidad',
                        'fuente',
                    ] as $campo
                ) {
                    $valor = (string) $this->valorSeleccionado(
                        $config,
                        'capacitaciones_fepade',
                        $item,
                        $campo
                    );

                    if ($valor !== '') {
                        $detalles[] = $valor;
                    }
                }

                $horas = (string) $this->valorSeleccionado(
                    $config,
                    'capacitaciones_fepade',
                    $item,
                    'no_horas_real'
                );

                if ($horas !== '') {
                    $detalles[] = $horas . ' horas';
                }

                if ($detalles) {
                    $cell->addText(
                        implode(' | ', $detalles),
                        [
                            'size' => 8,
                            'color' => '566573',
                        ]
                    );
                }

                $section->addTextBreak();
            }
        }

        /*
        |--------------------------------------------------------------------------
        | Referencias
        |--------------------------------------------------------------------------
        */

        $referencias = collect($cvData['referencias'] ?? [])
            ->filter(
                fn ($item) =>
                    $this->itemVisible(
                        $config,
                        'referencias',
                        $item,
                        [
                            'tipo',
                            'nombre',
                            'telefono',
                            'correo',
                            'empresa',
                            'cargo',
                        ]
                    )
            );

        if ($referencias->isNotEmpty()) {
            $this->tituloWord(
                $section,
                'REFERENCIAS'
            );

            $tablaReferencias = $section->addTable([
                'borderSize' => 4,
                'borderColor' => 'D5DBDB',
                'cellMargin' => 90,
                'width' => 100,
                'unit' => 'pct',
            ]);

            foreach (
                $referencias->groupBy(
                    fn ($item) =>
                        $item['tipo'] ?: 'Sin tipo'
                ) as $tipo => $items
            ) {
                $tablaReferencias->addRow();

                $tablaReferencias->addCell(
                    10000,
                    [
                        'gridSpan' => 2,
                        'bgColor' => '162536',
                    ]
                )->addText(
                    mb_strtoupper((string) $tipo),
                    [
                        'bold' => true,
                        'color' => 'FFFFFF',
                        'size' => 8,
                    ]
                );

                foreach ($items as $item) {
                    $nombreRef = (string) $this->valorSeleccionado(
                        $config,
                        'referencias',
                        $item,
                        'nombre'
                    );

                    $cargoRef = (string) $this->valorSeleccionado(
                        $config,
                        'referencias',
                        $item,
                        'cargo'
                    );

                    $empresaRef = (string) $this->valorSeleccionado(
                        $config,
                        'referencias',
                        $item,
                        'empresa'
                    );

                    $telefonoRef = (string) $this->valorSeleccionado(
                        $config,
                        'referencias',
                        $item,
                        'telefono'
                    );

                    $correoRef = (string) $this->valorSeleccionado(
                        $config,
                        'referencias',
                        $item,
                        'correo'
                    );

                    $tablaReferencias->addRow();

                    $cell = $tablaReferencias->addCell(4000);

                    $cell->addText(
                        $nombreRef !== ''
                            ? $nombreRef
                            : 'Referencia',
                        ['bold' => true]
                    );

                    $cell->addText(
                        implode(
                            ' | ',
                            array_filter([
                                $cargoRef,
                                $empresaRef,
                            ])
                        )
                    );

                    $cell = $tablaReferencias->addCell(6000);

                    if ($telefonoRef !== '') {
                        $cell->addText(
                            'Teléfono: ' . $telefonoRef
                        );
                    }

                    if ($correoRef !== '') {
                        $cell->addText(
                            'Correo: ' . $correoRef
                        );
                    }
                }
            }
        }

        return $phpWord;
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
            'experienciasLaborales' => fn ($q) => $q->where('activo', true)
                ->orderByDesc('trabajo_actual')
                ->orderByDesc('desde'),
            'experienciasLaborales' => fn ($q) => $q->where('activo', true)
                ->orderByDesc('trabajo_actual')
                ->orderByDesc('desde'),
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
                'desde_iso' => optional($item->desde)->format('Y-m-d'),
                'hasta' => $item->trabajo_actual
                    ? 'Actualidad'
                    : optional($item->hasta)->format('d/m/Y'),
                'hasta_iso' => optional($item->hasta)->format('Y-m-d'),
                'trabajo_actual' => $item->trabajo_actual ? 'Sí' : 'No',
                'trabajo_actual_bool' => (bool) $item->trabajo_actual,
                'jefe_nombre' => $item->jefe_nombre,
                'jefe_email' => $item->jefe_email,
                'jefe_telefono' => $item->jefe_telefono,
            ])->values(),

            'atestados' => $consultor->atestados->map(fn ($item) => [
                'id' => $item->id_atestado,
                'id_tipo_formacion' => $item->id_tipo_formacion,
                'tipo_formacion' => optional($item->tipoFormacion)->nombre,
                'tipo_atestado' => optional($item->tipoAtestado)->nombre,
                'nivel' => optional($item->nivelAcademico)->nombre,
                'titulo' => $item->titulo,
                'institucion' => $item->institucion,
                'descripcion' => $item->descripcion,
                'pais' => optional($item->pais)->nombre_pais,
                'fecha_inicio' => optional($item->fecha_inicio)->format('d/m/Y'),
                'fecha_inicio_iso' => optional($item->fecha_inicio)->format('Y-m-d'),
                'fecha_fin' => optional($item->fecha_fin)->format('d/m/Y'),
                'fecha_fin_iso' => optional($item->fecha_fin)->format('Y-m-d'),
                'fecha_emision' => optional($item->fecha_emision)->format('d/m/Y'),
                'fecha_emision_iso' => optional($item->fecha_emision)->format('Y-m-d'),
                'fecha_vencimiento' => optional($item->fecha_vencimiento)->format('d/m/Y'),
                'horas' => $item->horas,
                'archivo_url' => $item->url_archivo ? url(Storage::url($item->url_archivo)) : null,
            ])->values(),

            'capacitaciones_fepade' =>
                $consultor
                    ->capacitacionesFepade
                    ->map(
                        fn ($item) => [
                            'id' =>
                                $item->id_capacitacion_fepade,

                            'programa_curso_id' =>
                                $item->programa_curso_id,

                            'codigo_evento' =>
                                $item->codigo_evento,

                            'curso_nombre' =>
                                $item->curso_nombre,

                            'cliente' =>
                                $item->cliente,

                            'modalidad' =>
                                $item->modalidad,

                            'tipo_evento_nombre' =>
                                $item->tipo_evento_nombre,

                            'estado_curso_nombre' =>
                                $item->estado_curso_nombre,

                            'fecha_inicio' =>
                                optional(
                                    $item->fecha_inicio
                                )->format('d/m/Y'),

                            'fecha_inicio_iso' =>
                                optional(
                                    $item->fecha_inicio
                                )->format('Y-m-d'),

                            'fecha_fin' =>
                                optional(
                                    $item->fecha_fin
                                )->format('d/m/Y'),

                            'fecha_fin_iso' =>
                                optional(
                                    $item->fecha_fin
                                )->format('Y-m-d'),

                            'no_horas_real' =>
                                $item->no_horas_real,

                            'encuesta_id' =>
                                $item->encuesta_id,

                            'encuesta_nombre' =>
                                $item->encuesta_nombre,

                            'promedio_encuesta' =>
                                $item->promedio_encuesta,

                            'fecha_evaluacion' =>
                                optional(
                                    $item->fecha_evaluacion
                                )->format('d/m/Y H:i'),

                            'fuente' =>
                                $item->fuente,
                        ]
                    )
                    ->values(),

            'areas' => $consultor->areasEspecializacion->map(fn ($item) => [
                'id' => $item->id_consultor_area ?? $item->id_consultor_area_especializacion ?? $item->id,
                'id_atestado' => $item->id_atestado,
                'id_capacitacion_fepade' => $item->id_capacitacion_fepade,
                'nombre' => optional($item->areaEspecializacion)->nombre,
                'habilidades' => $item->habilidades->map(fn ($hab) => [
                    'id' => $hab->getKey(),
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

    private function mapaTipoFormacion(): array
    {
        return TipoFormacion::query()
            ->where('activo', true)
            ->get()
            ->mapWithKeys(function ($tipo) {
                return [
                    Str::slug($tipo->nombre, '_') => [$tipo->id_tipo_formacion],
                ];
            })
            ->toArray();
    }
}