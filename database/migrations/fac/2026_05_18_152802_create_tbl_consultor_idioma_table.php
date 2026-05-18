<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_consultor_idioma', function (Blueprint $table) {
            $table->increments('id_consultor_idioma');

            $table->unsignedInteger('id_consultor');
            $table->unsignedInteger('id_idioma');
            $table->unsignedInteger('id_idioma_nivel');

            $table->string('url_certificado', 500)->nullable();

            $table->boolean('activo')->default(true);

            $table->unsignedInteger('usuario_crea')->nullable();
            $table->unsignedInteger('usuario_mod')->nullable();
            $table->unsignedInteger('usuario_elim')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_consultor')->references('id_consultor')->on('tbl_consultor')->cascadeOnDelete();
            $table->foreign('id_idioma')->references('id_idioma')->on('tbl_idioma');
            $table->foreign('id_idioma_nivel')->references('id_idioma_nivel')->on('tbl_idioma_nivel');
            $table->foreign('usuario_crea')->references('id_usuario')->on('seg_usuarios')->nullOnDelete();
            $table->foreign('usuario_mod')->references('id_usuario')->on('seg_usuarios')->nullOnDelete();
            $table->foreign('usuario_elim')->references('id_usuario')->on('seg_usuarios')->nullOnDelete();

            $table->unique(['id_consultor', 'id_idioma'], 'uq_consultor_idioma');
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_consultor_idioma');
    }
};