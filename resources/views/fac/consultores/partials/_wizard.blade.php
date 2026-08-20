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
    | Secciones del expediente
    |--------------------------------------------------------------------------
    |
    | label:
    | Texto corto utilizado en las pestañas del wizard.
    |
    | title_self / title_admin:
    | Título principal mostrado según el contexto.
    |
    | description_self / description_admin:
    | Explicación específica del paso.
    |
    */

    $secciones = [

        'perfil' => [

            'label' => 'Perfil',
            'icon' => 'fa-user',

            'title_self' =>
                'Mi información personal',

            'title_admin' =>
                'Perfil personal',

            'description_self' =>
                'Mantén actualizados tus datos personales, residencia, documentos de identificación y fotografía.',

            'description_admin' =>
                'Actualiza los datos personales, residencia, documentos de identificación y fotografía del consultor.',

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

            'title_self' =>
                'Mi información de contacto',

            'title_admin' =>
                'Información de contacto',

            'description_self' =>
                'Mantén actualizados tus correos, teléfonos, redes sociales y contactos de emergencia.',

            'description_admin' =>
                'Mantén actualizados los medios de contacto y la información de emergencia del consultor.',

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

            'title_self' =>
                'Mi experiencia laboral',

            'title_admin' =>
                'Experiencia laboral',

            'description_self' =>
                'Registra y mantén actualizados tus cargos, empresas, períodos laborales y evidencias.',

            'description_admin' =>
                'Gestiona los cargos, empresas, períodos laborales y evidencias de la trayectoria profesional del consultor.',

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

            'title_self' =>
                'Mi trayectoria educativa',

            'title_admin' =>
                'Trayectoria educativa',

            'description_self' =>
                'Registra tus estudios, acreditaciones, educación continua y documentos de respaldo.',

            'description_admin' =>
                'Gestiona los estudios, acreditaciones, educación continua y documentos de respaldo del consultor.',

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

            'title_self' =>
                'Mis áreas de especialización',

            'title_admin' =>
                'Áreas de especialización',

            'description_self' =>
                'Relaciona tu experiencia y formación con las áreas y habilidades que respaldan tu perfil profesional.',

            'description_admin' =>
                'Relaciona las evidencias del consultor con sus áreas de especialización y habilidades técnicas.',

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

            'title_self' =>
                'Mis idiomas',

            'title_admin' =>
                'Idiomas del consultor',

            'description_self' =>
                'Mantén actualizados los idiomas que dominas, sus niveles y los certificados disponibles.',

            'description_admin' =>
                'Gestiona los idiomas, niveles de dominio y certificados registrados para el consultor.',

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

            'title_self' =>
                'Mis referencias',

            'title_admin' =>
                'Referencias del consultor',

            'description_self' =>
                'Registra contactos que puedan respaldar tu trayectoria personal o profesional.',

            'description_admin' =>
                'Gestiona los contactos de referencia asociados al expediente del consultor.',

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

            'title_self' =>
                'Mi disponibilidad',

            'title_admin' =>
                'Disponibilidad del consultor',

            'description_self' =>
                'Selecciona la opción que mejor representa tu disponibilidad actual para participar en actividades de FEPADE.',

            'description_admin' =>
                'Selecciona la opción que representa la disponibilidad actual del consultor.',

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
    | Información del paso activo
    |--------------------------------------------------------------------------
    */

    $seccionActual =
        $secciones[$step]
        ?? $secciones['perfil'];

    $tituloActual =
        $esMiPerfil
            ? $seccionActual['title_self']
            : $seccionActual['title_admin'];

    $descripcionActual =
        $esMiPerfil
            ? $seccionActual['description_self']
            : $seccionActual['description_admin'];


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

    :title="$tituloActual"

    :description="$descripcionActual"

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