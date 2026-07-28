<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Prepara las capacitaciones FEPADE para ser actualizadas
     * y auditadas mediante la integración con SAF.
     */
    public function up(): void
    {
        Schema::table(
            'tbl_consultor_capacitacion_fepade',
            function (Blueprint $table) {
                /*
                 |--------------------------------------------------------------------------
                 | Fecha de última sincronización SAF
                 |--------------------------------------------------------------------------
                 |
                 | Registra la última fecha y hora en la que esta capacitación
                 | fue insertada o actualizada a partir de información recibida
                 | desde SAF.
                 |
                 | El campo será NULL para registros que todavía no hayan sido
                 | sincronizados desde SAF.
                 |
                 */
                $table->timestamp('fecha_ultima_sincronizacion_saf')
                    ->nullable()
                    ->after('fuente');

                /*
                 |--------------------------------------------------------------------------
                 | Hash de los datos recibidos
                 |--------------------------------------------------------------------------
                 |
                 | Guardará una firma SHA-256 de los datos relevantes de la
                 | capacitación.
                 |
                 | Permitirá identificar si los datos enviados por SAF son
                 | distintos a los datos que ya están almacenados.
                 |
                 | Si el hash recibido es igual al hash guardado, el proceso
                 | podrá omitir una actualización innecesaria.
                 |
                 */
                $table->char('hash_datos_saf', 64)
                    ->nullable()
                    ->after('fecha_ultima_sincronizacion_saf');

                /*
                 |--------------------------------------------------------------------------
                 | Índice de fecha de sincronización
                 |--------------------------------------------------------------------------
                 |
                 | Facilita consultas de auditoría, por ejemplo:
                 |
                 | - Capacitaciones sincronizadas hoy.
                 | - Capacitaciones no actualizadas recientemente.
                 | - Capacitaciones sincronizadas durante un período.
                 |
                 */
                $table->index(
                    'fecha_ultima_sincronizacion_saf',
                    'idx_cap_fepade_fecha_sinc_saf'
                );

                /*
                 |--------------------------------------------------------------------------
                 | Índice por fuente y estado
                 |--------------------------------------------------------------------------
                 |
                 | El campo fuente ya existe en la tabla.
                 |
                 | Este índice facilitará consultas como:
                 |
                 | - Capacitaciones provenientes de SAF.
                 | - Capacitaciones activas provenientes de SAF.
                 | - Capacitaciones manuales o provenientes de FEPADE.
                 |
                 */
                $table->index(
                    ['fuente', 'activo'],
                    'idx_cap_fepade_fuente_activo'
                );
            }
        );
    }

    /**
     * Revierte los cambios realizados por esta migración.
     */
    public function down(): void
    {
        Schema::table(
            'tbl_consultor_capacitacion_fepade',
            function (Blueprint $table) {
                /*
                 |--------------------------------------------------------------------------
                 | Eliminar índices
                 |--------------------------------------------------------------------------
                 |
                 | Los índices deben eliminarse antes que las columnas.
                 |
                 */
                $table->dropIndex(
                    'idx_cap_fepade_fecha_sinc_saf'
                );

                $table->dropIndex(
                    'idx_cap_fepade_fuente_activo'
                );

                /*
                 |--------------------------------------------------------------------------
                 | Eliminar columnas
                 |--------------------------------------------------------------------------
                 */
                $table->dropColumn([
                    'fecha_ultima_sincronizacion_saf',
                    'hash_datos_saf',
                ]);
            }
        );
    }
};