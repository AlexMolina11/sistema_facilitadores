<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seg_aceptaciones_terminos', function (Blueprint $table) {
            $table->bigIncrements('id_aceptacion');

            $table->unsignedInteger('id_usuario');
            $table->unsignedInteger('id_consultor');
            $table->unsignedInteger('id_invitacion');

            $table->string('version_terminos', 50);
            $table->string('hash_terminos', 64);

            $table->ipAddress('ip')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('fecha_aceptacion');

            $table->timestamps();

            $table->foreign('id_usuario')
                ->references('id_usuario')
                ->on('seg_usuarios')
                ->cascadeOnDelete();

            $table->foreign('id_consultor')
                ->references('id_consultor')
                ->on('tbl_consultor')
                ->cascadeOnDelete();

            $table->foreign('id_invitacion')
                ->references('id_invitacion')
                ->on('seg_invitaciones')
                ->cascadeOnDelete();

            $table->index(
                ['id_consultor', 'fecha_aceptacion'],
                'idx_aceptaciones_consultor_fecha'
            );

            $table->index(
                ['id_usuario', 'fecha_aceptacion'],
                'idx_aceptaciones_usuario_fecha'
            );
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seg_aceptaciones_terminos');
    }
};