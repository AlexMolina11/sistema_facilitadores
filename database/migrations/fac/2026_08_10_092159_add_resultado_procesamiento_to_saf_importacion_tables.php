<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table(
            'tbl_saf_instructor_importacion',
            function (Blueprint $table): void {
                $table
                    ->string('resultado_procesamiento', 30)
                    ->nullable()
                    ->after('estado');

                $table
                    ->unsignedBigInteger('id_registro_local')
                    ->nullable()
                    ->after('id_sincronizacion');

                $table->index(
                    [
                        'id_sincronizacion',
                        'estado',
                    ],
                    'idx_saf_instructor_sync_estado'
                );

                $table->index(
                    [
                        'id_sincronizacion',
                        'resultado_procesamiento',
                    ],
                    'idx_saf_instructor_sync_resultado'
                );
            }
        );

        Schema::table(
            'tbl_saf_capacitacion_importacion',
            function (Blueprint $table): void {
                $table
                    ->string('resultado_procesamiento', 30)
                    ->nullable()
                    ->after('estado');

                $table
                    ->unsignedBigInteger('id_registro_local')
                    ->nullable()
                    ->after('id_sincronizacion');

                $table->index(
                    [
                        'id_sincronizacion',
                        'estado',
                    ],
                    'idx_saf_capacitacion_sync_estado'
                );

                $table->index(
                    [
                        'id_sincronizacion',
                        'resultado_procesamiento',
                    ],
                    'idx_saf_capacitacion_sync_resultado'
                );
            }
        );
    }

    public function down(): void
    {
        Schema::table(
            'tbl_saf_instructor_importacion',
            function (Blueprint $table): void {
                $table->dropIndex(
                    'idx_saf_instructor_sync_estado'
                );

                $table->dropIndex(
                    'idx_saf_instructor_sync_resultado'
                );

                $table->dropColumn([
                    'resultado_procesamiento',
                    'id_registro_local',
                ]);
            }
        );

        Schema::table(
            'tbl_saf_capacitacion_importacion',
            function (Blueprint $table): void {
                $table->dropIndex(
                    'idx_saf_capacitacion_sync_estado'
                );

                $table->dropIndex(
                    'idx_saf_capacitacion_sync_resultado'
                );

                $table->dropColumn([
                    'resultado_procesamiento',
                    'id_registro_local',
                ]);
            }
        );
    }
};