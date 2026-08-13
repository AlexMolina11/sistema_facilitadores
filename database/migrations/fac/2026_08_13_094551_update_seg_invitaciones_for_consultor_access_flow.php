<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seg_invitaciones', function (Blueprint $table) {

            /*
            |--------------------------------------------------------------------------
            | Consultor asociado
            |--------------------------------------------------------------------------
            |
            | La invitación deja de ser genérica y pasa a pertenecer a un
            | consultor previamente creado en tbl_consultor.
            |
            */
            $table->unsignedInteger('id_consultor')
                ->nullable()
                ->after('id_invitacion');

            $table->foreign('id_consultor')
                ->references('id_consultor')
                ->on('tbl_consultor')
                ->nullOnDelete();

            $table->index(
                'id_consultor',
                'idx_seg_invitaciones_id_consultor'
            );
        });

        /*
        |--------------------------------------------------------------------------
        | Límites opcionales
        |--------------------------------------------------------------------------
        |
        | NULL en duracion_horas / fecha_expiracion significará:
        |   tiempo ilimitado.
        |
        | NULL en max_usos significará:
        |   usos ilimitados.
        |
        */
        Schema::table('seg_invitaciones', function (Blueprint $table) {
            $table->integer('duracion_horas')
                ->nullable()
                ->default(null)
                ->change();

            $table->integer('max_usos')
                ->nullable()
                ->default(null)
                ->change();
        });
    }

    public function down(): void
    {
        /*
        |--------------------------------------------------------------------------
        | Importante
        |--------------------------------------------------------------------------
        |
        | Antes de volver a NOT NULL debemos reemplazar posibles valores NULL
        | para que el rollback no falle.
        |
        */

        \DB::table('seg_invitaciones')
            ->whereNull('duracion_horas')
            ->update(['duracion_horas' => 24]);

        \DB::table('seg_invitaciones')
            ->whereNull('max_usos')
            ->update(['max_usos' => 1]);

        Schema::table('seg_invitaciones', function (Blueprint $table) {
            $table->integer('duracion_horas')
                ->nullable(false)
                ->default(24)
                ->change();

            $table->integer('max_usos')
                ->nullable(false)
                ->default(1)
                ->change();
        });

        Schema::table('seg_invitaciones', function (Blueprint $table) {
            $table->dropForeign(['id_consultor']);

            $table->dropIndex(
                'idx_seg_invitaciones_id_consultor'
            );

            $table->dropColumn('id_consultor');
        });
    }
};