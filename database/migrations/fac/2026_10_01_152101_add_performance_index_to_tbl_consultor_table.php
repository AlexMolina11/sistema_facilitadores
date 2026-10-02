<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_consultor', function (Blueprint $table) {
            $table->index(
                ['activo', 'deleted_at', 'updated_at'],
                'idx_consultor_activo_deleted_updated'
            );
        });
    }

    public function down(): void
    {
        Schema::table('tbl_consultor', function (Blueprint $table) {
            $table->dropIndex('idx_consultor_activo_deleted_updated');
        });
    }
};