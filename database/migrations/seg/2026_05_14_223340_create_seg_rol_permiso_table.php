<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seg_rol_permiso', function (Blueprint $table) {

            $table->increments('id_rol_permiso');

            $table->unsignedInteger('id_rol');
            $table->unsignedInteger('id_permiso');

            $table->unsignedInteger('usuario_crea')->nullable();
            $table->timestamps();

            $table->unique(
                ['id_rol', 'id_permiso'],
                'uq_seg_rol_permiso'
            );

            $table->foreign('id_rol')
                ->references('id_rol')
                ->on('seg_roles')
                ->cascadeOnDelete();

            $table->foreign('id_permiso')
                ->references('id_permiso')
                ->on('seg_permisos')
                ->cascadeOnDelete();

            $table->foreign('usuario_crea')
                ->references('id_usuario')
                ->on('seg_usuarios')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seg_rol_permiso');
    }
};