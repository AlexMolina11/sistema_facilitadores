<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_consultor_atestado', function (Blueprint $table) {
            $table->increments('id_atestado');

            $table->unsignedInteger('id_consultor');
            $table->unsignedInteger('id_tipo_formacion');
            $table->unsignedInteger('id_tipo_atestado')->nullable();
            $table->unsignedInteger('id_nivel_academico')->nullable();
            $table->unsignedInteger('id_pais')->nullable();

            $table->string('titulo', 250);
            $table->text('descripcion')->nullable();
            $table->string('institucion', 250)->nullable();
            $table->string('entidad_acreditadora', 250)->nullable();
            $table->string('cliente_institucion', 250)->nullable();
            $table->string('codigo_acreditacion', 100)->nullable();

            $table->date('fecha_inicio')->nullable();
            $table->date('fecha_fin')->nullable();
            $table->date('fecha_emision')->nullable();
            $table->date('fecha_vencimiento')->nullable();
            $table->integer('horas')->nullable();

            $table->string('url_archivo', 500)->nullable();
            $table->string('nombre_archivo_original', 255)->nullable();

            $table->boolean('activo')->default(true);

            $table->unsignedInteger('usuario_crea')->nullable();
            $table->unsignedInteger('usuario_mod')->nullable();
            $table->unsignedInteger('usuario_elim')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_consultor')->references('id_consultor')->on('tbl_consultor')->cascadeOnDelete();
            $table->foreign('id_tipo_formacion')->references('id_tipo_formacion')->on('tbl_tipo_formacion');
            $table->foreign('id_tipo_atestado')->references('id_tipo_atestado')->on('tbl_tipo_atestado')->nullOnDelete();
            $table->foreign('id_nivel_academico')->references('id_nivel_academico')->on('tbl_nivel_academico')->nullOnDelete();
            $table->foreign('id_pais')->references('id_pais')->on('tbl_pais')->nullOnDelete();
            $table->foreign('usuario_crea')->references('id_usuario')->on('seg_usuarios')->nullOnDelete();
            $table->foreign('usuario_mod')->references('id_usuario')->on('seg_usuarios')->nullOnDelete();
            $table->foreign('usuario_elim')->references('id_usuario')->on('seg_usuarios')->nullOnDelete();

            $table->index(['id_consultor', 'id_tipo_formacion'], 'idx_atestado_consultor_formacion');
            $table->index(['id_consultor', 'activo'], 'idx_atestado_consultor_activo');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_consultor_atestado');
    }
};
