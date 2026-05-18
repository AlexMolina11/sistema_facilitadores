<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::create('seg_bitacora_accesos', function (Blueprint $table) {

            $table->bigIncrements('id_bitacora');

            $table->unsignedInteger('id_usuario')->nullable();

            $table->string('evento', 50);
            $table->ipAddress('ip')->nullable();
            $table->text('user_agent')->nullable();

            $table->timestamp('fecha_evento');

            $table->timestamps();

            $table->foreign('id_usuario')
                ->references('id_usuario')
                ->on('seg_usuarios')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::dropIfExists('seg_bitacora_accesos');
    }
};