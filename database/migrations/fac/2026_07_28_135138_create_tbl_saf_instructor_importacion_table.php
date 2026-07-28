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
            'tbl_saf_instructor_importacion',
            function (Blueprint $table): void {
                $table->bigIncrements('id_importacion');

                /*
                 * Identificadores provenientes de SAF.
                 */
                $table->unsignedBigInteger('id_instructor');
                $table->unsignedBigInteger('id_entidad');

                /*
                 * Información básica del instructor.
                 */
                $table->string('nombres', 150);
                $table->string('apellidos', 150);
                $table->string('dui', 20)->nullable();
                $table->boolean('activo')->default(true);

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
                 * Un instructor SAF solamente debe tener un registro
                 * vigente dentro de la tabla de importación.
                 */
                $table->unique(
                    [
                        'id_instructor',
                        'id_entidad',
                    ],
                    'uq_saf_instructor_importacion'
                );

                /*
                 * Índices utilizados por el procesador.
                 */
                $table->index(
                    'estado',
                    'idx_saf_instructor_estado'
                );

                $table->index(
                    'fecha_recepcion',
                    'idx_saf_instructor_recepcion'
                );

                $table->index(
                    'id_sincronizacion',
                    'idx_saf_instructor_sincronizacion'
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
            'tbl_saf_instructor_importacion'
        );
    }
};