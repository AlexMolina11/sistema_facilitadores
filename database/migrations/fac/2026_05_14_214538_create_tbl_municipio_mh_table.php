<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_municipio_mh', function (Blueprint $table) {
            $table->increments('id_municipio_mh');
            $table->string('municipio_mh_nombre', 100);

            $table->string('mh_codigo_municipio', 50)->nullable();

            $table->unsignedInteger('id_departamento')->nullable();

            $table->boolean('activo')->default(true);

            $table->unsignedInteger('usuario_crea')->nullable();
            $table->unsignedInteger('usuario_mod')->nullable();
            $table->unsignedInteger('usuario_elim')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_departamento')
                ->references('id_departamento')
                ->on('tbl_departamento');

            $table->foreign('usuario_crea')
                ->references('id_usuario')
                ->on('seg_usuarios')
                ->nullOnDelete();

            $table->foreign('usuario_mod')
                ->references('id_usuario')
                ->on('seg_usuarios')
                ->nullOnDelete();

            $table->foreign('usuario_elim')
                ->references('id_usuario')
                ->on('seg_usuarios')
                ->nullOnDelete();

        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_municipio_mh');
    }
};
