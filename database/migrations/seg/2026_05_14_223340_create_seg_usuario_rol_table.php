<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seg_usuario_rol', function (Blueprint $table) {

            $table->increments('id_usuario_rol');

            $table->unsignedInteger('id_usuario');
            $table->unsignedInteger('id_rol');

            $table->unsignedInteger('usuario_crea')->nullable();
            $table->timestamps();

            $table->unique(
                ['id_usuario', 'id_rol'],
                'uq_seg_usuario_rol'
            );

            $table->foreign('id_usuario')
                ->references('id_usuario')
                ->on('seg_usuarios')
                ->cascadeOnDelete();

            $table->foreign('id_rol')
                ->references('id_rol')
                ->on('seg_roles')
                ->cascadeOnDelete();

            $table->foreign('usuario_crea')
                ->references('id_usuario')
                ->on('seg_usuarios')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seg_usuario_rol');
    }
};