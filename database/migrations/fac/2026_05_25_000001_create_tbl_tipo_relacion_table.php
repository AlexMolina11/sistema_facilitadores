<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_tipo_relacion', function (Blueprint $table) {
            $table->increments('id_tipo_relacion');
            $table->string('nombre', 100);
            $table->boolean('activo')->default(true);

            $table->unsignedInteger('usuario_crea')->nullable();
            $table->unsignedInteger('usuario_mod')->nullable();
            $table->unsignedInteger('usuario_elim')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('usuario_crea')->references('id_usuario')->on('seg_usuarios')->nullOnDelete();
            $table->foreign('usuario_mod')->references('id_usuario')->on('seg_usuarios')->nullOnDelete();
            $table->foreign('usuario_elim')->references('id_usuario')->on('seg_usuarios')->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_tipo_relacion');
    }
};
