<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_tipo_referencia', function (Blueprint $table) {
            $table->increments('id_tipo_referencia');

            $table->string('nombre', 100);

            $table->boolean('activo')->default(true);

            // Auditoría
            $table->unsignedBigInteger('usuario_crea')->nullable();
            $table->unsignedBigInteger('usuario_mod')->nullable();
            $table->unsignedBigInteger('usuario_elim')->nullable();

            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_tipo_referencia');
    }
};