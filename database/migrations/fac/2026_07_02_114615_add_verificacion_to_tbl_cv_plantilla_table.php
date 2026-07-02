<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::table('tbl_cv_plantilla', function (Blueprint $table) {
            if (! Schema::hasColumn('tbl_cv_plantilla', 'vista_verificada')) {
                $table->boolean('vista_verificada')->default(false)->after('vista_blade');
            }

            if (! Schema::hasColumn('tbl_cv_plantilla', 'fecha_verificacion')) {
                $table->timestamp('fecha_verificacion')->nullable()->after('vista_verificada');
            }
        });
    }

    public function down(): void
    {
        Schema::table('tbl_cv_plantilla', function (Blueprint $table) {
            if (Schema::hasColumn('tbl_cv_plantilla', 'fecha_verificacion')) {
                $table->dropColumn('fecha_verificacion');
            }

            if (Schema::hasColumn('tbl_cv_plantilla', 'vista_verificada')) {
                $table->dropColumn('vista_verificada');
            }
        });
    }
};