<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seg_invitaciones', function (Blueprint $table) {

            $table->increments('id_invitacion');

            $table->unsignedInteger('id_rol');

            $table->string('alias', 150);

            $table->string('token', 255)->unique();

            $table->string('url_invitacion', 500)->nullable();
            $table->string('ruta_qr', 500)->nullable();

            $table->integer('duracion_horas')->default(24);

            $table->integer('max_usos')->default(1);
            $table->integer('usos_actuales')->default(0);

            $table->timestamp('fecha_expiracion')->nullable();

            $table->boolean('activa')->default(true);
            $table->boolean('revocada')->default(false);

            $table->unsignedInteger('usuario_crea')->nullable();
            $table->unsignedInteger('usuario_mod')->nullable();
            $table->unsignedInteger('usuario_elim')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->foreign('id_rol')
                ->references('id_rol')
                ->on('seg_roles');

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
        Schema::dropIfExists('seg_invitaciones');
    }
};