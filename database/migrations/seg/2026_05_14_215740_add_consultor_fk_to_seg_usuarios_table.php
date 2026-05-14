<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('seg_usuarios', function (Blueprint $table) {
            $table->foreign('id_consultor')
                ->references('id_consultor')
                ->on('tbl_consultor')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('seg_usuarios', function (Blueprint $table) {
            $table->dropForeign(['id_consultor']);
        });
    }
};