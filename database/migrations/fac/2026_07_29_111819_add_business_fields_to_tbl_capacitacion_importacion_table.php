<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Ejecuta la migración.
     */
    public function up(): void
    {
        Schema::table(
            'tbl_saf_capacitacion_importacion',
            function (Blueprint $table): void {

                /*
                 * Renombramos nombre
                 * para mantener consistencia
                 * con la tabla funcional.
                 */
                $table->renameColumn(
                    'nombre',
                    'nombre_evento'
                );

                /*
                 * Información completa
                 * enviada por SAF.
                 */
                $table
                    ->string(
                        'tema',
                        250
                    )
                    ->nullable()
                    ->after('nombre_evento');

                $table
                    ->string(
                        'institucion',
                        250
                    )
                    ->nullable()
                    ->after('tema');

                $table
                    ->string(
                        'modalidad',
                        100
                    )
                    ->nullable()
                    ->after('institucion');
            }
        );
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::table(
            'tbl_saf_capacitacion_importacion',
            function (Blueprint $table): void {

                $table->dropColumn([
                    'tema',
                    'institucion',
                    'modalidad',
                ]);

                $table->renameColumn(
                    'nombre_evento',
                    'nombre'
                );
            }
        );
    }
};