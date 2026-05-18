<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_consultor_referencia', function (Blueprint $table) {
            $table->increments('id_referencia');

            $table->unsignedInteger('id_consultor');
            $table->unsignedInteger('id_tipo_referencia');

            $table->string('nombre', 150);
            $table->string('telefono', 20)->nullable();
            $table->string('correo', 120)->nullable();
            $table->string('empresa', 150)->nullable();
            $table->string('cargo', 100)->nullable();

            $table->unsignedInteger('id_tipo_relacion')->nullable();

            $table->boolean('activo')->default(true);

            $table->unsignedInteger('usuario_crea')->nullable();
            $table->unsignedInteger('usuario_mod')->nullable();
            $table->unsignedInteger('usuario_elim')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_consultor')->references('id_consultor')->on('tbl_consultor')->cascadeOnDelete();
            $table->foreign('id_tipo_referencia')->references('id_tipo_referencia')->on('tbl_tipo_referencia');
            $table->foreign('usuario_crea')->references('id_usuario')->on('seg_usuarios')->nullOnDelete();
            $table->foreign('usuario_mod')->references('id_usuario')->on('seg_usuarios')->nullOnDelete();
            $table->foreign('usuario_elim')->references('id_usuario')->on('seg_usuarios')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_consultor_referencia');
    }
};