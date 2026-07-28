<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla principal de auditoría de sincronizaciones SAF.
     */
    public function up(): void
    {
        Schema::create('tbl_sincronizacion_saf', function (Blueprint $table) {
            /*
             |--------------------------------------------------------------------------
             | Llave primaria
             |--------------------------------------------------------------------------
             */
            $table->increments('id_sincronizacion_saf');

            /*
             |--------------------------------------------------------------------------
             | Identificación de la ejecución
             |--------------------------------------------------------------------------
             |
             | UUID permite identificar de manera única cada ejecución sin
             | depender únicamente del correlativo interno de la base.
             |
             */
            $table->uuid('uuid')
                ->unique('uq_sincronizacion_saf_uuid');

            /*
             |--------------------------------------------------------------------------
             | Tipo de ejecución
             |--------------------------------------------------------------------------
             |
             | Valores previstos:
             |
             | - MANUAL
             | - AUTOMATICA
             |
             | MANUAL:
             | Ejecutada por un usuario desde el sistema.
             |
             | AUTOMATICA:
             | Ejecutada por un comando programado, tarea cron o proceso externo.
             |
             */
            $table->string('tipo_ejecucion', 30)
                ->default('MANUAL');

            /*
             |--------------------------------------------------------------------------
             | Estado de la sincronización
             |--------------------------------------------------------------------------
             |
             | Estados previstos:
             |
             | - PENDIENTE
             | - EN_PROCESO
             | - COMPLETADA
             | - COMPLETADA_CON_ERRORES
             | - FALLIDA
             |
             */
            $table->string('estado', 40)
                ->default('PENDIENTE');

            /*
             |--------------------------------------------------------------------------
             | Fechas de ejecución
             |--------------------------------------------------------------------------
             |
             | fecha_inicio:
             | Momento exacto en que comenzó el procesamiento.
             |
             | fecha_fin:
             | Momento exacto en que terminó el procesamiento.
             |
             | fecha_fin permanecerá NULL mientras el proceso esté pendiente
             | o todavía se encuentre en ejecución.
             |
             */
            $table->timestamp('fecha_inicio')
                ->nullable();

            $table->timestamp('fecha_fin')
                ->nullable();

            /*
             |--------------------------------------------------------------------------
             | Totales generales
             |--------------------------------------------------------------------------
             */
            $table->unsignedInteger('total_registros_recibidos')
                ->default(0);

            $table->unsignedInteger('total_registros_procesados')
                ->default(0);

            $table->unsignedInteger('total_registros_exitosos')
                ->default(0);

            $table->unsignedInteger('total_registros_con_error')
                ->default(0);

            /*
             |--------------------------------------------------------------------------
             | Contadores de consultores
             |--------------------------------------------------------------------------
             */
            $table->unsignedInteger('consultores_creados')
                ->default(0);

            $table->unsignedInteger('consultores_actualizados')
                ->default(0);

            $table->unsignedInteger('consultores_sin_cambios')
                ->default(0);

            $table->unsignedInteger('consultores_con_error')
                ->default(0);

            /*
             |--------------------------------------------------------------------------
             | Contadores de capacitaciones
             |--------------------------------------------------------------------------
             */
            $table->unsignedInteger('capacitaciones_creadas')
                ->default(0);

            $table->unsignedInteger('capacitaciones_actualizadas')
                ->default(0);

            $table->unsignedInteger('capacitaciones_sin_cambios')
                ->default(0);

            /*
             | Permitirá contabilizar capacitaciones SAF que ya no estén
             | vigentes en la fuente y que sean desactivadas localmente.
             */
            $table->unsignedInteger('capacitaciones_desactivadas')
                ->default(0);

            $table->unsignedInteger('capacitaciones_con_error')
                ->default(0);

            /*
             |--------------------------------------------------------------------------
             | Información del resultado
             |--------------------------------------------------------------------------
             |
             | mensaje:
             | Descripción general legible para el usuario.
             |
             | resumen:
             | Información estructurada adicional en formato JSON.
             |
             */
            $table->text('mensaje')
                ->nullable();

            $table->json('resumen')
                ->nullable();

            /*
             |--------------------------------------------------------------------------
             | Usuario que ejecutó la sincronización
             |--------------------------------------------------------------------------
             |
             | Será NULL cuando la sincronización sea automática.
             |
             | Si el usuario se elimina en el futuro, el registro de auditoría
             | se conservará y usuario_ejecuta pasará a NULL.
             |
             */
            $table->unsignedInteger('usuario_ejecuta')
                ->nullable();

            /*
             |--------------------------------------------------------------------------
             | Timestamps de Laravel
             |--------------------------------------------------------------------------
             */
            $table->timestamps();

            /*
             |--------------------------------------------------------------------------
             | Índices
             |--------------------------------------------------------------------------
             */
            $table->index(
                'tipo_ejecucion',
                'idx_sincronizacion_saf_tipo'
            );

            $table->index(
                'estado',
                'idx_sincronizacion_saf_estado'
            );

            $table->index(
                'fecha_inicio',
                'idx_sincronizacion_saf_fecha_inicio'
            );

            $table->index(
                ['estado', 'fecha_inicio'],
                'idx_sincronizacion_saf_estado_fecha'
            );

            $table->index(
                'usuario_ejecuta',
                'idx_sincronizacion_saf_usuario'
            );

            /*
             |--------------------------------------------------------------------------
             | Llave foránea
             |--------------------------------------------------------------------------
             */
            $table->foreign('usuario_ejecuta')
                ->references('id_usuario')
                ->on('seg_usuarios')
                ->nullOnDelete();
        });
    }

    /**
     * Elimina la tabla principal de sincronizaciones SAF.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_sincronizacion_saf');
    }
};