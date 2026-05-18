<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_consultor_formacion_academica', function (Blueprint $table) {
            $table->increments('id_atestado');

            $table->unsignedInteger('id_consultor');
            $table->unsignedInteger('id_tipo_atestado');
            $table->unsignedInteger('id_nivel_academico');
            $table->unsignedInteger('id_pais')->nullable();

            $table->string('descripcion', 250)->nullable();
            $table->string('institucion', 250)->nullable();
            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->string('url', 500)->nullable();

            $table->boolean('activo')->default(true);

            $table->unsignedInteger('usuario_crea')->nullable();
            $table->unsignedInteger('usuario_mod')->nullable();
            $table->unsignedInteger('usuario_elim')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_consultor')->references('id_consultor')->on('tbl_consultor')->cascadeOnDelete();
            $table->foreign('id_tipo_atestado')->references('id_tipo_atestado')->on('tbl_tipo_atestado');
            $table->foreign('id_nivel_academico')->references('id_nivel_academico')->on('tbl_nivel_academico');
            $table->foreign('id_pais')->references('id_pais')->on('tbl_pais')->nullOnDelete();
            $table->foreign('usuario_crea')->references('id_usuario')->on('seg_usuarios')->nullOnDelete();
            $table->foreign('usuario_mod')->references('id_usuario')->on('seg_usuarios')->nullOnDelete();
            $table->foreign('usuario_elim')->references('id_usuario')->on('seg_usuarios')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_consultor_formacion_academica');
    }
};