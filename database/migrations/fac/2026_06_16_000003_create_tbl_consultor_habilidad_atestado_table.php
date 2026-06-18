<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_consultor_habilidad_atestado', function (Blueprint $table) {
            $table->increments('id_habilidad_atestado');

            $table->unsignedInteger('id_consultor');
            $table->unsignedInteger('id_habilidad');
            $table->unsignedInteger('id_atestado');

            $table->boolean('activo')->default(true);

            $table->unsignedInteger('usuario_crea')->nullable();
            $table->unsignedInteger('usuario_mod')->nullable();
            $table->unsignedInteger('usuario_elim')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_consultor')->references('id_consultor')->on('tbl_consultor')->cascadeOnDelete();
            $table->foreign('id_habilidad')->references('id_habilidad')->on('tbl_habilidad')->cascadeOnDelete();
            $table->foreign('id_atestado')->references('id_atestado')->on('tbl_consultor_atestado')->cascadeOnDelete();
            $table->foreign('usuario_crea')->references('id_usuario')->on('seg_usuarios')->nullOnDelete();
            $table->foreign('usuario_mod')->references('id_usuario')->on('seg_usuarios')->nullOnDelete();
            $table->foreign('usuario_elim')->references('id_usuario')->on('seg_usuarios')->nullOnDelete();

            $table->unique(['id_consultor', 'id_habilidad', 'id_atestado'], 'uq_consultor_habilidad_atestado');
            $table->index(['id_habilidad', 'activo'], 'idx_habilidad_atestado_activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_consultor_habilidad_atestado');
    }
};
