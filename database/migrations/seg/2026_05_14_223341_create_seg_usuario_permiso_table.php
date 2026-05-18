<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seg_usuario_permiso', function (Blueprint $table) {

            $table->increments('id_usuario_permiso');

            $table->unsignedInteger('id_usuario');
            $table->unsignedInteger('id_permiso');

            $table->boolean('permitido')->default(true);

            $table->unsignedInteger('usuario_crea')->nullable();
            $table->timestamps();

            $table->unique(
                ['id_usuario', 'id_permiso'],
                'uq_seg_usuario_permiso'
            );

            $table->foreign('id_usuario')
                ->references('id_usuario')
                ->on('seg_usuarios')
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
        Schema::dropIfExists('seg_usuario_permiso');
    }
};