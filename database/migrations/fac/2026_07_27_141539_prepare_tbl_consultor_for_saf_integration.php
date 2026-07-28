<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Prepara la tabla de consultores para recibir y controlar
     * registros provenientes del sistema SAF.
     */
    public function up(): void
    {
        Schema::table('tbl_consultor', function (Blueprint $table) {
            /*
             |--------------------------------------------------------------------------
             | Origen del registro
             |--------------------------------------------------------------------------
             |
             | Permite identificar si el consultor fue creado manualmente
             | dentro del Sistema de Facilitadores o si fue recibido desde SAF.
             |
             | Valores previstos:
             | - MANUAL
             | - SAF
             |
             | Los registros existentes quedarán como MANUAL.
             |
             */
            $table->string('origen_registro', 30)
                ->default('MANUAL')
                ->after('id_entidad');

            /*
             |--------------------------------------------------------------------------
             | Fecha de última sincronización SAF
             |--------------------------------------------------------------------------
             |
             | Registra la última fecha y hora en la que SAF insertó o actualizó
             | los datos principales del consultor.
             |
             | Será NULL para los consultores que nunca hayan sido sincronizados.
             |
             */
            $table->timestamp('fecha_ultima_sincronizacion_saf')
                ->nullable()
                ->after('origen_registro');

            /*
             |--------------------------------------------------------------------------
             | Hash de los datos SAF
             |--------------------------------------------------------------------------
             |
             | Guardará una firma SHA-256 de los datos recibidos desde SAF.
             |
             | Esto permitirá comparar el registro recibido con el registro
             | almacenado y evitar actualizaciones innecesarias.
             |
             */
            $table->char('hash_datos_saf', 64)
                ->nullable()
                ->after('fecha_ultima_sincronizacion_saf');

            /*
             |--------------------------------------------------------------------------
             | Índice único de id_instructor
             |--------------------------------------------------------------------------
             |
             | id_instructor será el identificador externo principal del
             | instructor enviado por SAF.
             |
             | La columna se mantiene nullable porque los consultores creados
             | manualmente pueden no tener todavía un id_instructor.
             |
             | MySQL permite múltiples valores NULL en un índice único.
             |
             */
            $table->unique(
                'id_instructor',
                'uq_consultor_id_instructor_saf'
            );

            /*
             |--------------------------------------------------------------------------
             | Índice de entidad
             |--------------------------------------------------------------------------
             |
             | Una entidad puede tener varios instructores, por lo que
             | id_entidad no debe ser único.
             |
             */
            $table->index(
                'id_entidad',
                'idx_consultor_id_entidad_saf'
            );

            /*
             |--------------------------------------------------------------------------
             | Índice por origen
             |--------------------------------------------------------------------------
             |
             | Facilita filtros como:
             | - Consultores manuales
             | - Consultores provenientes de SAF
             |
             */
            $table->index(
                'origen_registro',
                'idx_consultor_origen_registro'
            );

            /*
             |--------------------------------------------------------------------------
             | Índice por fecha de sincronización
             |--------------------------------------------------------------------------
             |
             | Facilita consultas de auditoría y reportes por períodos.
             |
             */
            $table->index(
                'fecha_ultima_sincronizacion_saf',
                'idx_consultor_fecha_sincronizacion_saf'
            );
        });
    }

    /**
     * Revierte completamente los cambios de esta migración.
     */
    public function down(): void
    {
        Schema::table('tbl_consultor', function (Blueprint $table) {
            /*
             |--------------------------------------------------------------------------
             | Eliminar índices
             |--------------------------------------------------------------------------
             |
             | Los índices se eliminan antes que las columnas relacionadas.
             |
             */
            $table->dropUnique(
                'uq_consultor_id_instructor_saf'
            );

            $table->dropIndex(
                'idx_consultor_id_entidad_saf'
            );

            $table->dropIndex(
                'idx_consultor_origen_registro'
            );

            $table->dropIndex(
                'idx_consultor_fecha_sincronizacion_saf'
            );

            /*
             |--------------------------------------------------------------------------
             | Eliminar columnas agregadas
             |--------------------------------------------------------------------------
             */
            $table->dropColumn([
                'origen_registro',
                'fecha_ultima_sincronizacion_saf',
                'hash_datos_saf',
            ]);
        });
    }
};