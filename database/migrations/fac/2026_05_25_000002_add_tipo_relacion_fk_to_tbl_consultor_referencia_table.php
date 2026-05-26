<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_consultor_referencia', function (Blueprint $table) {
            if (!Schema::hasColumn('tbl_consultor_referencia', 'id_tipo_relacion')) {
                $table->unsignedInteger('id_tipo_relacion')->nullable()->after('cargo');
            }
        });

        Schema::table('tbl_consultor_referencia', function (Blueprint $table) {
            $table->foreign('id_tipo_relacion', 'fk_referencia_tipo_relacion')
                ->references('id_tipo_relacion')
                ->on('tbl_tipo_relacion')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::table('tbl_consultor_referencia', function (Blueprint $table) {
            $table->dropForeign('fk_referencia_tipo_relacion');
        });

        Schema::table('tbl_consultor_referencia', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_consultor_referencia', 'id_tipo_relacion')) {
                $table->dropColumn('id_tipo_relacion');
            }
        });
    }
};
