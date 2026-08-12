<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Permite mantener historial de cada recepción
     * proveniente de SAF.
     *
     * Las tablas staging son append-only:
     *
     * cada envío de SAF genera un nuevo registro.
     */
    public function up(): void
    {
        Schema::table(
            'tbl_saf_instructor_importacion',
            function (Blueprint $table): void {
                $table->dropUnique(
                    'uq_saf_instructor_importacion'
                );

                /*
                 * Conservamos un índice normal para que
                 * las búsquedas por instructor/entidad
                 * sigan siendo eficientes.
                 */
                $table->index(
                    [
                        'id_instructor',
                        'id_entidad',
                    ],
                    'idx_saf_instructor_entidad'
                );
            }
        );

        Schema::table(
            'tbl_saf_capacitacion_importacion',
            function (Blueprint $table): void {
                $table->dropUnique(
                    'uq_saf_capacitacion_importacion'
                );

                /*
                 * Un mismo evento puede llegar nuevamente
                 * cuando SAF modifica sus datos o registra
                 * posteriormente su encuesta.
                 */
                $table->index(
                    [
                        'id_instructor',
                        'codigo_evento',
                    ],
                    'idx_saf_capacitacion_instructor_evento'
                );
            }
        );
    }

    /**
     * Revierte al diseño anterior.
     *
     * IMPORTANTE:
     * el rollback solo será posible si no existen
     * duplicados históricos.
     */
    public function down(): void
    {
        Schema::table(
            'tbl_saf_instructor_importacion',
            function (Blueprint $table): void {
                $table->dropIndex(
                    'idx_saf_instructor_entidad'
                );

                $table->unique(
                    [
                        'id_instructor',
                        'id_entidad',
                    ],
                    'uq_saf_instructor_importacion'
                );
            }
        );

        Schema::table(
            'tbl_saf_capacitacion_importacion',
            function (Blueprint $table): void {
                $table->dropIndex(
                    'idx_saf_capacitacion_instructor_evento'
                );

                $table->unique(
                    [
                        'id_instructor',
                        'codigo_evento',
                    ],
                    'uq_saf_capacitacion_importacion'
                );
            }
        );
    }
};