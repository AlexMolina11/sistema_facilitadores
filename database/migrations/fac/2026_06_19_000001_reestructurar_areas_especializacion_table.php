<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('tbl_consultor_habilidad_capacitacion');
        Schema::dropIfExists('tbl_consultor_habilidad_atestado');
        Schema::dropIfExists('tbl_consultor_habilidad');
        Schema::dropIfExists('tbl_habilidad');
        Schema::dropIfExists('tbl_tipo_habilidad');

        Schema::enableForeignKeyConstraints();

        Schema::create('tbl_area_especializacion', function (Blueprint $table) {
            $table->increments('id_area_especializacion');
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedInteger('usuario_crea')->nullable();
            $table->unsignedInteger('usuario_mod')->nullable();
            $table->unsignedInteger('usuario_elim')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique('nombre', 'uq_area_esp_nombre');

            $table->foreign('usuario_crea', 'fk_area_esp_user_crea')
                ->references('id_usuario')
                ->on('seg_usuarios')
                ->nullOnDelete();

            $table->foreign('usuario_mod', 'fk_area_esp_user_mod')
                ->references('id_usuario')
                ->on('seg_usuarios')
                ->nullOnDelete();

            $table->foreign('usuario_elim', 'fk_area_esp_user_elim')
                ->references('id_usuario')
                ->on('seg_usuarios')
                ->nullOnDelete();
        });

        Schema::create('tbl_habilidad_tecnica', function (Blueprint $table) {
            $table->increments('id_habilidad_tecnica');
            $table->unsignedInteger('id_area_especializacion');
            $table->string('nombre', 150);
            $table->text('descripcion')->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedInteger('usuario_crea')->nullable();
            $table->unsignedInteger('usuario_mod')->nullable();
            $table->unsignedInteger('usuario_elim')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(['id_area_especializacion', 'nombre'], 'uq_habtec_area_nombre');

            $table->foreign('id_area_especializacion', 'fk_habtec_area')
                ->references('id_area_especializacion')
                ->on('tbl_area_especializacion')
                ->restrictOnDelete();

            $table->foreign('usuario_crea', 'fk_habtec_user_crea')
                ->references('id_usuario')
                ->on('seg_usuarios')
                ->nullOnDelete();

            $table->foreign('usuario_mod', 'fk_habtec_user_mod')
                ->references('id_usuario')
                ->on('seg_usuarios')
                ->nullOnDelete();

            $table->foreign('usuario_elim', 'fk_habtec_user_elim')
                ->references('id_usuario')
                ->on('seg_usuarios')
                ->nullOnDelete();
        });

        Schema::create('tbl_consultor_area_especializacion', function (Blueprint $table) {
            $table->increments('id_consultor_area');
            $table->unsignedInteger('id_consultor');
            $table->unsignedInteger('id_area_especializacion');
            $table->unsignedInteger('id_atestado')->nullable();
            $table->unsignedInteger('id_capacitacion_fepade')->nullable();
            $table->boolean('activo')->default(true);
            $table->unsignedInteger('usuario_crea')->nullable();
            $table->unsignedInteger('usuario_mod')->nullable();
            $table->unsignedInteger('usuario_elim')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                ['id_consultor', 'id_area_especializacion', 'id_atestado'],
                'uq_cons_area_atestado'
            );

            $table->unique(
                ['id_consultor', 'id_area_especializacion', 'id_capacitacion_fepade'],
                'uq_cons_area_cap'
            );

            $table->foreign('id_consultor', 'fk_cons_area_cons')
                ->references('id_consultor')
                ->on('tbl_consultor')
                ->cascadeOnDelete();

            $table->foreign('id_area_especializacion', 'fk_cons_area_area')
                ->references('id_area_especializacion')
                ->on('tbl_area_especializacion')
                ->restrictOnDelete();

            $table->foreign('id_atestado', 'fk_cons_area_ates')
                ->references('id_atestado')
                ->on('tbl_consultor_atestado')
                ->cascadeOnDelete();

            $table->foreign('id_capacitacion_fepade', 'fk_cons_area_cap')
                ->references('id_capacitacion_fepade')
                ->on('tbl_consultor_capacitacion_fepade')
                ->cascadeOnDelete();

            $table->foreign('usuario_crea', 'fk_cons_area_user_crea')
                ->references('id_usuario')
                ->on('seg_usuarios')
                ->nullOnDelete();

            $table->foreign('usuario_mod', 'fk_cons_area_user_mod')
                ->references('id_usuario')
                ->on('seg_usuarios')
                ->nullOnDelete();

            $table->foreign('usuario_elim', 'fk_cons_area_user_elim')
                ->references('id_usuario')
                ->on('seg_usuarios')
                ->nullOnDelete();
        });

        Schema::create('tbl_consultor_area_habilidad', function (Blueprint $table) {
            $table->increments('id_consultor_area_habilidad');
            $table->unsignedInteger('id_consultor_area');
            $table->unsignedInteger('id_habilidad_tecnica');
            $table->boolean('activo')->default(true);
            $table->unsignedInteger('usuario_crea')->nullable();
            $table->unsignedInteger('usuario_mod')->nullable();
            $table->unsignedInteger('usuario_elim')->nullable();
            $table->timestamps();
            $table->softDeletes();

            $table->unique(
                ['id_consultor_area', 'id_habilidad_tecnica'],
                'uq_cons_area_hab'
            );

            $table->foreign('id_consultor_area', 'fk_cons_area_hab_area')
                ->references('id_consultor_area')
                ->on('tbl_consultor_area_especializacion')
                ->cascadeOnDelete();

            $table->foreign('id_habilidad_tecnica', 'fk_cons_area_hab_tec')
                ->references('id_habilidad_tecnica')
                ->on('tbl_habilidad_tecnica')
                ->restrictOnDelete();

            $table->foreign('usuario_crea', 'fk_cons_area_hab_user_crea')
                ->references('id_usuario')
                ->on('seg_usuarios')
                ->nullOnDelete();

            $table->foreign('usuario_mod', 'fk_cons_area_hab_user_mod')
                ->references('id_usuario')
                ->on('seg_usuarios')
                ->nullOnDelete();

            $table->foreign('usuario_elim', 'fk_cons_area_hab_user_elim')
                ->references('id_usuario')
                ->on('seg_usuarios')
                ->nullOnDelete();
        });
    }

    public function down(): void
    {
        Schema::disableForeignKeyConstraints();

        Schema::dropIfExists('tbl_consultor_area_habilidad');
        Schema::dropIfExists('tbl_consultor_area_especializacion');
        Schema::dropIfExists('tbl_habilidad_tecnica');
        Schema::dropIfExists('tbl_area_especializacion');

        Schema::enableForeignKeyConstraints();
    }
};