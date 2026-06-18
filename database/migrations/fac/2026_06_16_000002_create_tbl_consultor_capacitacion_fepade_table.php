<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_consultor_capacitacion_fepade', function (Blueprint $table) {
            $table->increments('id_capacitacion_fepade');

            $table->unsignedInteger('id_consultor');

            $table->string('codigo_evento_externo', 100)->nullable();
            $table->string('nombre_evento', 250);
            $table->string('tema', 250)->nullable();
            $table->string('institucion', 250)->nullable();
            $table->string('modalidad', 100)->nullable();

            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->integer('horas')->nullable();

            $table->string('fuente', 100)->default('FEPADE');
            $table->boolean('activo')->default(true);

            $table->unsignedInteger('usuario_crea')->nullable();
            $table->unsignedInteger('usuario_mod')->nullable();
            $table->unsignedInteger('usuario_elim')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_consultor')->references('id_consultor')->on('tbl_consultor')->cascadeOnDelete();
            $table->foreign('usuario_crea')->references('id_usuario')->on('seg_usuarios')->nullOnDelete();
            $table->foreign('usuario_mod')->references('id_usuario')->on('seg_usuarios')->nullOnDelete();
            $table->foreign('usuario_elim')->references('id_usuario')->on('seg_usuarios')->nullOnDelete();

            $table->unique(['id_consultor', 'codigo_evento_externo'], 'uq_consultor_evento_fepade');
            $table->index(['id_consultor', 'activo'], 'idx_cap_fepade_consultor_activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_consultor_capacitacion_fepade');
    }
};
