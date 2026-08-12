<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Aplica el nuevo contrato de integración acordado con SAF.
     */
    public function up(): void
    {
        /*
        |--------------------------------------------------------------------------
        | 1. Instructor SAF
        |--------------------------------------------------------------------------
        |
        | Cambios solicitados:
        |
        | - dui -> numero_identificacion
        | - agregar tipo_identificacion
        | - agregar correo_saf
        |
        | Los campos técnicos de procesamiento NO se modifican.
        |
        */
        Schema::table(
            'tbl_saf_instructor_importacion',
            function (Blueprint $table): void {

                $table->renameColumn(
                    'dui',
                    'numero_identificacion'
                );
            }
        );

        Schema::table(
            'tbl_saf_instructor_importacion',
            function (Blueprint $table): void {

                $table
                    ->unsignedInteger('tipo_identificacion')
                    ->nullable()
                    ->after('apellidos');

                $table
                    ->string('correo_saf', 50)
                    ->nullable()
                    ->after('numero_identificacion');
            }
        );

        /*
        |--------------------------------------------------------------------------
        | 2. Capacitación SAF - índices
        |--------------------------------------------------------------------------
        |
        | Antes de renombrar codigo_evento_externo debemos retirar el índice
        | único que depende de ese campo.
        |
        */
        Schema::table(
            'tbl_saf_capacitacion_importacion',
            function (Blueprint $table): void {

                $table->dropUnique(
                    'uq_saf_capacitacion_importacion'
                );
            }
        );

        /*
        |--------------------------------------------------------------------------
        | 3. Capacitación SAF - renombrar campos existentes
        |--------------------------------------------------------------------------
        */
        Schema::table(
            'tbl_saf_capacitacion_importacion',
            function (Blueprint $table): void {

                $table->renameColumn(
                    'codigo_evento_externo',
                    'codigo_evento'
                );

                $table->renameColumn(
                    'nombre_evento',
                    'curso_nombre'
                );

                $table->renameColumn(
                    'institucion',
                    'cliente'
                );

                $table->renameColumn(
                    'horas',
                    'no_horas_real'
                );
            }
        );

        /*
        |--------------------------------------------------------------------------
        | 4. Capacitación SAF - nuevos campos
        |--------------------------------------------------------------------------
        |
        | IMPORTANTE:
        |
        | El campo "estado" existente NO se modifica.
        |
        | estado = estado técnico del procesamiento:
        | PENDIENTE / EN_PROCESO / PROCESADO / ERROR
        |
        | estado_curso_nombre = dato proveniente de SAF.
        |
        */
        Schema::table(
            'tbl_saf_capacitacion_importacion',
            function (Blueprint $table): void {

                $table
                    ->unsignedBigInteger('programa_curso_id')
                    ->nullable()
                    ->after('id_instructor');

                $table
                    ->string('estado_curso_nombre', 30)
                    ->nullable()
                    ->after('fecha_fin');

                /*
                 * modalidad ya existe.
                 * Se conserva el campo pero se ajustará a varchar(50)
                 * posteriormente.
                 */

                $table
                    ->string('tipo_evento_nombre', 30)
                    ->nullable()
                    ->after('modalidad');

                $table
                    ->unsignedBigInteger('encuesta_id')
                    ->nullable()
                    ->after('cliente');

                $table
                    ->string('encuesta_nombre', 100)
                    ->nullable()
                    ->after('encuesta_id');

                $table
                    ->decimal(
                        'promedio_encuesta',
                        10,
                        2
                    )
                    ->nullable()
                    ->after('encuesta_nombre');

                $table
                    ->dateTime('fecha_evaluacion')
                    ->nullable()
                    ->after('promedio_encuesta');
            }
        );

        /*
        |--------------------------------------------------------------------------
        | 5. Capacitación SAF - eliminar campos que SAF ya no enviará
        |--------------------------------------------------------------------------
        */
        Schema::table(
            'tbl_saf_capacitacion_importacion',
            function (Blueprint $table): void {

                $table->dropColumn([
                    'tema',
                    'activo',
                ]);
            }
        );

        /*
        |--------------------------------------------------------------------------
        | 6. Nuevo índice único de capacitación SAF
        |--------------------------------------------------------------------------
        */
        Schema::table(
            'tbl_saf_capacitacion_importacion',
            function (Blueprint $table): void {

                $table->unique(
                    [
                        'id_instructor',
                        'codigo_evento',
                    ],
                    'uq_saf_capacitacion_importacion'
                );
            }
        );

        /*
        |--------------------------------------------------------------------------
        | 7. Tabla funcional de capacitaciones
        |--------------------------------------------------------------------------
        |
        | Conservamos:
        |
        | - fuente
        | - activo
        | - fecha_ultima_sincronizacion_saf
        | - hash_datos_saf
        | - auditoría
        | - soft delete
        |
        | "activo" se mantiene porque actualmente es utilizado por el sistema
        | de Facilitadores aunque SAF deje de enviar ese dato.
        |
        */
        Schema::table(
            'tbl_consultor_capacitacion_fepade',
            function (Blueprint $table): void {

                $table->dropUnique(
                    'uq_consultor_evento_fepade'
                );
            }
        );

        Schema::table(
            'tbl_consultor_capacitacion_fepade',
            function (Blueprint $table): void {

                $table->renameColumn(
                    'codigo_evento_externo',
                    'codigo_evento'
                );

                $table->renameColumn(
                    'nombre_evento',
                    'curso_nombre'
                );

                $table->renameColumn(
                    'institucion',
                    'cliente'
                );

                $table->renameColumn(
                    'horas',
                    'no_horas_real'
                );
            }
        );

        Schema::table(
            'tbl_consultor_capacitacion_fepade',
            function (Blueprint $table): void {

                $table
                    ->unsignedBigInteger('programa_curso_id')
                    ->nullable()
                    ->after('id_consultor');

                $table
                    ->string(
                        'estado_curso_nombre',
                        30
                    )
                    ->nullable()
                    ->after('fecha_fin');

                $table
                    ->string(
                        'tipo_evento_nombre',
                        30
                    )
                    ->nullable()
                    ->after('modalidad');

                $table
                    ->unsignedBigInteger('encuesta_id')
                    ->nullable()
                    ->after('cliente');

                $table
                    ->string(
                        'encuesta_nombre',
                        100
                    )
                    ->nullable()
                    ->after('encuesta_id');

                $table
                    ->decimal(
                        'promedio_encuesta',
                        10,
                        2
                    )
                    ->nullable()
                    ->after('encuesta_nombre');

                $table
                    ->dateTime('fecha_evaluacion')
                    ->nullable()
                    ->after('promedio_encuesta');
            }
        );

        /*
        |--------------------------------------------------------------------------
        | tema deja de formar parte del contrato de capacitación.
        |--------------------------------------------------------------------------
        */
        Schema::table(
            'tbl_consultor_capacitacion_fepade',
            function (Blueprint $table): void {

                $table->dropColumn('tema');
            }
        );

        Schema::table(
            'tbl_consultor_capacitacion_fepade',
            function (Blueprint $table): void {

                $table->unique(
                    [
                        'id_consultor',
                        'codigo_evento',
                    ],
                    'uq_consultor_evento_fepade'
                );
            }
        );
    }

    /**
     * Revierte el nuevo contrato SAF.
     */
    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Capacitación funcional
        |--------------------------------------------------------------------------
        */
        Schema::table(
            'tbl_consultor_capacitacion_fepade',
            function (Blueprint $table): void {

                $table->dropUnique(
                    'uq_consultor_evento_fepade'
                );
            }
        );

        Schema::table(
            'tbl_consultor_capacitacion_fepade',
            function (Blueprint $table): void {

                $table
                    ->string('tema', 250)
                    ->nullable()
                    ->after('curso_nombre');

                $table->dropColumn([
                    'programa_curso_id',
                    'estado_curso_nombre',
                    'tipo_evento_nombre',
                    'encuesta_id',
                    'encuesta_nombre',
                    'promedio_encuesta',
                    'fecha_evaluacion',
                ]);
            }
        );

        Schema::table(
            'tbl_consultor_capacitacion_fepade',
            function (Blueprint $table): void {

                $table->renameColumn(
                    'codigo_evento',
                    'codigo_evento_externo'
                );

                $table->renameColumn(
                    'curso_nombre',
                    'nombre_evento'
                );

                $table->renameColumn(
                    'cliente',
                    'institucion'
                );

                $table->renameColumn(
                    'no_horas_real',
                    'horas'
                );
            }
        );

        Schema::table(
            'tbl_consultor_capacitacion_fepade',
            function (Blueprint $table): void {

                $table->unique(
                    [
                        'id_consultor',
                        'codigo_evento_externo',
                    ],
                    'uq_consultor_evento_fepade'
                );
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Capacitación staging SAF
        |--------------------------------------------------------------------------
        */
        Schema::table(
            'tbl_saf_capacitacion_importacion',
            function (Blueprint $table): void {

                $table->dropUnique(
                    'uq_saf_capacitacion_importacion'
                );
            }
        );

        Schema::table(
            'tbl_saf_capacitacion_importacion',
            function (Blueprint $table): void {

                $table
                    ->string('tema', 250)
                    ->nullable()
                    ->after('curso_nombre');

                $table
                    ->boolean('activo')
                    ->default(true);

                $table->dropColumn([
                    'programa_curso_id',
                    'estado_curso_nombre',
                    'tipo_evento_nombre',
                    'encuesta_id',
                    'encuesta_nombre',
                    'promedio_encuesta',
                    'fecha_evaluacion',
                ]);
            }
        );

        Schema::table(
            'tbl_saf_capacitacion_importacion',
            function (Blueprint $table): void {

                $table->renameColumn(
                    'codigo_evento',
                    'codigo_evento_externo'
                );

                $table->renameColumn(
                    'curso_nombre',
                    'nombre_evento'
                );

                $table->renameColumn(
                    'cliente',
                    'institucion'
                );

                $table->renameColumn(
                    'no_horas_real',
                    'horas'
                );
            }
        );

        Schema::table(
            'tbl_saf_capacitacion_importacion',
            function (Blueprint $table): void {

                $table->unique(
                    [
                        'id_instructor',
                        'codigo_evento_externo',
                    ],
                    'uq_saf_capacitacion_importacion'
                );
            }
        );

        /*
        |--------------------------------------------------------------------------
        | Instructor staging SAF
        |--------------------------------------------------------------------------
        */
        Schema::table(
            'tbl_saf_instructor_importacion',
            function (Blueprint $table): void {

                $table->dropColumn([
                    'tipo_identificacion',
                    'correo_saf',
                ]);
            }
        );

        Schema::table(
            'tbl_saf_instructor_importacion',
            function (Blueprint $table): void {

                $table->renameColumn(
                    'numero_identificacion',
                    'dui'
                );
            }
        );
    }
};