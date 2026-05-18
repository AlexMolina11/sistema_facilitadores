<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_consultor_experiencia_laboral', function (Blueprint $table) {
            $table->increments('id_experiencia');
            $table->unsignedInteger('id_consultor');

            $table->string('empresa', 150)->nullable();
            $table->string('cargo', 100)->nullable();
            $table->string('descripcion', 500)->nullable();
            $table->date('desde')->nullable();
            $table->date('hasta')->nullable();
            $table->boolean('trabajo_actual')->default(false);

            $table->string('jefe_nombre', 150)->nullable();
            $table->string('jefe_email', 120)->nullable();
            $table->string('jefe_telefono', 20)->nullable();
            $table->string('url_evidencia', 500)->nullable();

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
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_consultor_experiencia_laboral');
    }
};