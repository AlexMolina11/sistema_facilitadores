<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_consultor_habilidad', function (Blueprint $table) {
            $table->increments('id_consultor_habilidad');

            $table->unsignedInteger('id_consultor');
            $table->unsignedInteger('id_habilidad');

            $table->boolean('activo')->default(true);

            $table->unsignedInteger('usuario_crea')->nullable();
            $table->unsignedInteger('usuario_mod')->nullable();
            $table->unsignedInteger('usuario_elim')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_consultor')->references('id_consultor')->on('tbl_consultor')->cascadeOnDelete();
            $table->foreign('id_habilidad')->references('id_habilidad')->on('tbl_habilidad');
            $table->foreign('usuario_crea')->references('id_usuario')->on('seg_usuarios')->nullOnDelete();
            $table->foreign('usuario_mod')->references('id_usuario')->on('seg_usuarios')->nullOnDelete();
            $table->foreign('usuario_elim')->references('id_usuario')->on('seg_usuarios')->nullOnDelete();

            $table->unique(['id_consultor', 'id_habilidad'], 'uq_consultor_habilidad');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_consultor_habilidad');
    }
};