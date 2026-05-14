<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seg_usuarios', function (Blueprint $table) {
            $table->increments('id_usuario');

            $table->unsignedInteger('id_consultor')->nullable();

            $table->string('nombres', 100);
            $table->string('apellidos', 100);
            $table->string('email', 150)->unique();
            $table->string('password', 255);

            $table->boolean('activo')->default(true);
            $table->dateTime('ultimo_acceso')->nullable();

            $table->unsignedInteger('usuario_crea')->nullable();
            $table->unsignedInteger('usuario_mod')->nullable();
            $table->unsignedInteger('usuario_elim')->nullable();

            $table->timestamps();
            $table->softDeletes();

            $table->unique('id_consultor', 'uq_seg_usuarios_id_consultor');
        });

        Schema::table('seg_usuarios', function (Blueprint $table) {
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
        Schema::table('seg_usuarios', function (Blueprint $table) {
            $table->dropForeign(['usuario_crea']);
            $table->dropForeign(['usuario_mod']);
            $table->dropForeign(['usuario_elim']);
        });

        Schema::dropIfExists('seg_usuarios');
    }
};