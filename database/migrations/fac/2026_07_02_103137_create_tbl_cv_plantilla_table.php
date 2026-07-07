<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('tbl_cv_plantilla', function (Blueprint $table) {
            $table->increments('id_cv_plantilla');
            $table->string('codigo', 80)->unique();
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->string('vista_blade', 180);
            $table->string('tamanio_papel', 30)->default('letter');
            $table->string('orientacion', 30)->default('portrait');
            $table->unsignedInteger('orden')->default(1);
            $table->boolean('activa')->default(true);
            $table->boolean('activo')->default(true);
            $table->unsignedInteger('usuario_crea')->nullable();
            $table->unsignedInteger('usuario_mod')->nullable();
            $table->unsignedInteger('usuario_elim')->nullable();
            $table->timestamps();
            $table->softDeletes();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('tbl_cv_plantilla');
    }
};