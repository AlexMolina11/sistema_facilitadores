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
        Schema::create(
            'tbl_saf_capacitacion_importacion',
            function (Blueprint $table): void {
                $table->bigIncrements('id_importacion');

                /*
                 * Instructor propietario de la capacitación.
                 */
                $table->unsignedBigInteger('id_instructor');

                /*
                 * Información de la capacitación.
                 */
                $table->string(
                    'codigo_evento_externo',
                    100
                );

                $table->string(
                    'nombre',
                    250
                );

                $table
                    ->date('fecha_inicio')
                    ->nullable();

                $table
                    ->date('fecha_fin')
                    ->nullable();

                $table
                    ->integer('horas')
                    ->nullable();

                $table
                    ->boolean('activo')
                    ->default(true);

                /*
                 * Control del procesamiento.
                 */
                $table
                    ->string('estado', 30)
                    ->default('PENDIENTE');

                $table
                    ->unsignedInteger('intentos')
                    ->default(0);

                $table
                    ->text('mensaje_error')
                    ->nullable();

                $table
                    ->timestamp('fecha_recepcion')
                    ->useCurrent();

                $table
                    ->timestamp('fecha_procesamiento')
                    ->nullable();

                $table
                    ->unsignedBigInteger('id_sincronizacion')
                    ->nullable();

                $table->timestamps();

                /*
                 * Un evento solamente puede aparecer una vez
                 * para el mismo instructor.
                 */
                $table->unique(
                    [
                        'id_instructor',
                        'codigo_evento_externo',
                    ],
                    'uq_saf_capacitacion_importacion'
                );

                /*
                 * Índices utilizados por el procesador.
                 */
                $table->index(
                    'estado',
                    'idx_saf_capacitacion_estado'
                );

                $table->index(
                    'fecha_recepcion',
                    'idx_saf_capacitacion_recepcion'
                );

                $table->index(
                    'id_sincronizacion',
                    'idx_saf_capacitacion_sincronizacion'
                );
            }
        );
    }

    /**
     * Revierte la migración.
     */
    public function down(): void
    {
        Schema::dropIfExists(
            'tbl_saf_capacitacion_importacion'
        );
    }
};