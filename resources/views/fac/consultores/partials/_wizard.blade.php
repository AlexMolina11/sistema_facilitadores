@php
    use App\Modules\Fac\Support\ProfileProgressPresenter;

    /*
    |--------------------------------------------------------------------------
    | Contexto
    |--------------------------------------------------------------------------
    */

    $usuarioActual = auth()->user();

    $esMiPerfil =
        filled($usuarioActual?->id_consultor)
        && (int) $usuarioActual->id_consultor
            === (int) $consultor->id_consultor;

    /*
    |--------------------------------------------------------------------------
    | Paso actual
    |--------------------------------------------------------------------------
    */

    $stepMap = [
        1 => 'perfil',
        2 => 'contacto',
        3 => 'experiencia',
        4 => 'formacion',
        5 => 'habilidades',
        6 => 'idiomas',
        7 => 'referencias',
        8 => 'disponibilidad',
    ];

    $step = $stepMap[$step ?? 1]
        ?? $step
        ?? 'perfil';

    /*
    |--------------------------------------------------------------------------
    | Avance
    |--------------------------------------------------------------------------
    */

    $avancePerfil =
        $avancePerfil
        ?? (
            $consultor
                ? $consultor->avancePerfil()
                : [
                    'porcentaje' => 0,
                    'obtenidos' => 0,
                    'total' => 0,
                    'puntos_fijos' => [],
                    'puntos_dinamicos' => [],
                ]
        );

    $puntosFijos =
        collect(
            $avancePerfil['puntos_fijos'] ?? []
        );

    $puntosDinamicos =
        collect(
            $avancePerfil['puntos_dinamicos'] ?? []
        );

    $porcentaje =
        $avancePerfil['porcentaje'] ?? 0;

    /*
    |--------------------------------------------------------------------------
    | Secciones del wizard
    |--------------------------------------------------------------------------
    */

    $secciones = [

        'perfil' => [
            'label' => 'Perfil',
            'icon' => 'fa-user',

            'url' => route(
                'fac.consultores.edit',
                $consultor
            ),

            'criterios' => [
                'Datos personales obligatorios',
                'Documento de identidad con archivo',
                'Residencia completa',
            ],
        ],

        'contacto' => [
            'label' => 'Contacto',
            'icon' => 'fa-phone',

            'url' => route(
                'fac.consultores.contacto.edit',
                $consultor
            ),

            'criterios' => [
                'Correo principal',
                'Teléfono registrado',
                'Contacto de emergencia',
            ],
        ],

        'experiencia' => [
            'label' => 'Experiencia',
            'icon' => 'fa-briefcase',

            'url' => route(
                'fac.consultores.experiencia.edit',
                $consultor
            ),

            'criterios' => [
                'Experiencia laboral',
            ],
        ],

        'formacion' => [
            'label' => 'Formación',
            'icon' => 'fa-graduation-cap',

            'url' => route(
                'fac.consultores.formacion.edit',
                $consultor
            ),

            'criterios' => [
                'Educación formal',
                'Educación continua',
            ],
        ],

        'habilidades' => [
            'label' => 'Especialización',
            'icon' => 'fa-star',

            'url' => route(
                'fac.consultores.habilidades.edit',
                $consultor
            ),

            'criterios' => [
                'Área de especialización con evidencia',
            ],

            'dynamic_prefixes' => [
                'Área vinculada al atestado:',
                'Área vinculada a capacitación FEPADE:',
            ],
        ],

        'idiomas' => [
            'label' => 'Idiomas',
            'icon' => 'fa-language',

            'url' => route(
                'fac.consultores.idiomas.edit',
                $consultor
            ),

            'criterios' => [
                'Idioma registrado',
            ],
        ],

        'referencias' => [
            'label' => 'Referencias',
            'icon' => 'fa-handshake',

            'url' => route(
                'fac.consultores.referencias.edit',
                $consultor
            ),

            'criterios_prefixes' => [
                'Referencia ',
            ],
        ],

        'disponibilidad' => [
            'label' => 'Disponibilidad',
            'icon' => 'fa-calendar-check',

            'url' => route(
                'fac.consultores.disponibilidad.edit',
                $consultor
            ),

            'criterios' => [
                'Disponibilidad actual',
            ],
        ],
    ];

    /*
    |--------------------------------------------------------------------------
    | Estado de completitud
    |--------------------------------------------------------------------------
    */

    $todosLosCriterios =
        $puntosFijos
            ->merge(
                $puntosDinamicos
            );

    $criteriosPresentados =
        ProfileProgressPresenter::presentCollection(
            $todosLosCriterios
        );

    $pendientesPresentados =
        ProfileProgressPresenter::pendingMessages(
            $todosLosCriterios
        );

    $steps = $consultor
        ? collect($secciones)
            ->map(
                function ($seccion) use (
                    $todosLosCriterios
                ) {

                    $criteriosDirectos =
                        collect(
                            $seccion['criterios'] ?? []
                        );

                    $criteriosPorPrefijo =
                        collect();

                    foreach (
                        (
                            $seccion['criterios_prefixes']
                            ?? []
                        ) as $prefix
                    ) {
                        $criteriosPorPrefijo =
                            $criteriosPorPrefijo
                                ->merge(
                                    $todosLosCriterios
                                        ->keys()
                                        ->filter(
                                            fn ($key) =>
                                                str_starts_with(
                                                    $key,
                                                    $prefix
                                                )
                                        )
                                );
                    }

                    foreach (
                        (
                            $seccion['dynamic_prefixes']
                            ?? []
                        ) as $prefix
                    ) {
                        $criteriosPorPrefijo =
                            $criteriosPorPrefijo
                                ->merge(
                                    $todosLosCriterios
                                        ->keys()
                                        ->filter(
                                            fn ($key) =>
                                                str_starts_with(
                                                    $key,
                                                    $prefix
                                                )
                                        )
                                );
                    }

                    $criteriosSeccion =
                        $criteriosDirectos
                            ->merge(
                                $criteriosPorPrefijo
                            )
                            ->unique()
                            ->values();

                    $completed =
                        $criteriosSeccion->isNotEmpty()
                        && $criteriosSeccion
                            ->every(
                                fn ($criterio) =>
                                    (bool) (
                                        $todosLosCriterios[
                                            $criterio
                                        ]
                                        ?? false
                                    )
                            );

                    return [
                        'label' =>
                            $seccion['label'],

                        'icon' =>
                            $seccion['icon'],

                        'url' =>
                            $seccion['url'],

                        'completed' =>
                            $completed,
                    ];
                }
            )
            ->toArray()
        : [];

    $missing =
        collect($steps)
            ->filter(
                fn ($item) =>
                    empty(
                        $item['completed']
                    )
            )
            ->pluck('label')
            ->values()
            ->toArray();
@endphp


<x-ui.wizard-progress

    :title="$esMiPerfil
        ? 'Mi perfil profesional'
        : 'Perfil del consultor'"

    :description="$esMiPerfil
        ? 'Completa y mantén actualizada la información de tu expediente profesional.'
        : 'Completa los criterios requeridos para alcanzar el 100% del expediente profesional.'"

    :steps="$steps"

    :current="$step"

    :completion="$porcentaje"

    :missing="$missing"

    :criteria-fixed="$puntosFijos"

    :criteria-dynamic="$puntosDinamicos"

    :presented-criteria="$criteriosPresentados"

    :pending-messages="$pendientesPresentados"

    :obtained="$avancePerfil['obtenidos'] ?? null"

    :total="$avancePerfil['total'] ?? null"

    class="mb-4"
/>