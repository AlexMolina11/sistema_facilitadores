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

            $table->unsignedInteger('id_departamento');
            $table->unsignedInteger('id_municipio_mh')->nullable();

            $table->string('nombre', 100);

            $table->boolean('activo')->default(true);

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_departamento')
                ->references('id_departamento')
                ->on('tbl_departamento');

            $table->foreign('id_municipio_mh')
                ->references('id_municipio_mh')
                ->on('tbl_municipio_mh')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_municipio');
    }
};