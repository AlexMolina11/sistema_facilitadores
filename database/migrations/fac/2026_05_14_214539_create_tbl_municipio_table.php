<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_municipio', function (Blueprint $table) {

            $table->increments('id_municipio');

            $table->unsignedInteger('id_pais');
            $table->unsignedInteger('id_departamento');
            $table->unsignedInteger('id_municipio_mh')->nullable();

            $table->string('nombre_distrito', 100);

            $table->string('mh_codigo_distrito', 50)->nullable();

            $table->string('georeferencia', 100)->nullable();

            $table->boolean('activo')->default(true);

            $table->unsignedInteger('usuario_crea')->nullable();
            $table->unsignedInteger('usuario_mod')->nullable();
            $table->unsignedInteger('usuario_elim')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_pais')
                ->references('id_pais')
                ->on('tbl_pais');

            $table->foreign('id_departamento')
                ->references('id_departamento')
                ->on('tbl_departamento');

            $table->foreign('id_municipio_mh')
                ->references('id_municipio_mh')
                ->on('tbl_municipio_mh')
                ->nullOnDelete();

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
        Schema::dropIfExists('tbl_municipio');
    }
};