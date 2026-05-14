<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_consultor', function (Blueprint $table) {
            $table->increments('id_consultor');

            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('apellido_casa', 100)->nullable();
            $table->string('estado_civil', 20)->nullable();
            $table->string('nacionalidad', 50)->nullable();
            $table->string('tipo_identificacion', 30)->nullable();
            $table->string('numero_identificacion', 30)->nullable();
            $table->string('nit', 20)->nullable();
            $table->string('nrc', 20)->nullable();
            $table->char('sexo', 1)->nullable();
            $table->date('fecha_nacimiento')->nullable();

            $table->unsignedInteger('id_pais')->nullable();
            $table->unsignedInteger('id_municipio')->nullable();

            $table->string('direccion_residencia', 200)->nullable();
            $table->string('ruta_foto', 300)->nullable();
            $table->string('emergencia_contacto', 150)->nullable();

            $table->boolean('vigente')->default(true);
            $table->integer('id_instructor')->nullable();
            $table->integer('id_entidad')->nullable();
            $table->boolean('activo')->default(true);

            $table->unsignedInteger('usuario_crea')->nullable();
            $table->unsignedInteger('usuario_mod')->nullable();
            $table->unsignedInteger('usuario_elim')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_pais')
                ->references('id_pais')
                ->on('tbl_pais')
                ->nullOnDelete();

            $table->foreign('id_municipio')
                ->references('id_municipio')
                ->on('tbl_municipio')
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
        Schema::dropIfExists('tbl_consultor');
    }
};