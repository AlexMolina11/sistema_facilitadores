<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::dropIfExists('tbl_consultor_tipo_consultoria');
        Schema::dropIfExists('tbl_tipo_consultoria');
    }

    public function down(): void
    {
        Schema::create('tbl_tipo_consultoria', function (Blueprint $table) {
            $table->increments('id_tipo_consultoria');
            $table->string('nombre', 150);
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();
        });

        Schema::create('tbl_consultor_tipo_consultoria', function (Blueprint $table) {
            $table->increments('id_consultor_tipo_consultoria');
            $table->unsignedInteger('id_consultor');
            $table->unsignedInteger('id_tipo_consultoria');
            $table->boolean('activo')->default(true);
            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_consultor')->references('id_consultor')->on('tbl_consultor');
            $table->foreign('id_tipo_consultoria')->references('id_tipo_consultoria')->on('tbl_tipo_consultoria');
            $table->unique(['id_consultor', 'id_tipo_consultoria'], 'uq_consultor_tipo_consultoria');
        });
    }
};