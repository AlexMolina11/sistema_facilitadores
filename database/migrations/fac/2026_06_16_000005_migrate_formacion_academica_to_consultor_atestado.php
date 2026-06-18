<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    public function up(): void
    {
        if (! Schema::hasTable('tbl_consultor_formacion_academica') || ! Schema::hasTable('tbl_consultor_atestado')) {
            return;
        }

        DB::table('tbl_consultor_formacion_academica')
            ->whereNull('deleted_at')
            ->orderBy('id_atestado')
            ->chunkById(100, function ($registros) {
                foreach ($registros as $registro) {
                    $idTipoFormacion = DB::table('tbl_tipo_atestado')
                        ->where('id_tipo_atestado', $registro->id_tipo_atestado)
                        ->value('id_tipo_formacion') ?? 1;

                    $existe = DB::table('tbl_consultor_atestado')
                        ->where('id_consultor', $registro->id_consultor)
                        ->where('id_tipo_atestado', $registro->id_tipo_atestado)
                        ->where('id_nivel_academico', $registro->id_nivel_academico)
                        ->where(function ($query) use ($registro) {
                            $query->where('descripcion', $registro->descripcion)
                                ->orWhere(function ($subQuery) use ($registro) {
                                    $subQuery->whereNull('descripcion')
                                        ->whereRaw('? IS NULL', [$registro->descripcion]);
                                });
                        })
                        ->where(function ($query) use ($registro) {
                            $query->where('institucion', $registro->institucion)
                                ->orWhere(function ($subQuery) use ($registro) {
                                    $subQuery->whereNull('institucion')
                                        ->whereRaw('? IS NULL', [$registro->institucion]);
                                });
                        })
                        ->where(function ($query) use ($registro) {
                            $query->where('fecha_inicio', $registro->fecha_inicio)
                                ->orWhere(function ($subQuery) use ($registro) {
                                    $subQuery->whereNull('fecha_inicio')
                                        ->whereRaw('? IS NULL', [$registro->fecha_inicio]);
                                });
                        })
                        ->where(function ($query) use ($registro) {
                            $query->where('fecha_fin', $registro->fecha_fin)
                                ->orWhere(function ($subQuery) use ($registro) {
                                    $subQuery->whereNull('fecha_fin')
                                        ->whereRaw('? IS NULL', [$registro->fecha_fin]);
                                });
                        })
                        ->where(function ($query) use ($registro) {
                            $query->where('url_archivo', $registro->url)
                                ->orWhere(function ($subQuery) use ($registro) {
                                    $subQuery->whereNull('url_archivo')
                                        ->whereRaw('? IS NULL', [$registro->url]);
                                });
                        })
                        ->exists();

                    if ($existe) {
                        continue;
                    }

                    DB::table('tbl_consultor_atestado')->insert([
                        'id_consultor'              => $registro->id_consultor,
                        'id_tipo_formacion'         => $idTipoFormacion,
                        'id_tipo_atestado'          => $registro->id_tipo_atestado,
                        'id_nivel_academico'        => $registro->id_nivel_academico,
                        'id_pais'                   => $registro->id_pais,
                        'titulo'                    => $registro->descripcion ?: 'Atestado migrado',
                        'descripcion'               => $registro->descripcion,
                        'institucion'               => $registro->institucion,
                        'entidad_acreditadora'      => null,
                        'cliente_institucion'       => null,
                        'codigo_acreditacion'       => null,
                        'fecha_inicio'              => $registro->fecha_inicio,
                        'fecha_fin'                 => $registro->fecha_fin,
                        'fecha_emision'             => null,
                        'fecha_vencimiento'         => null,
                        'horas'                     => null,
                        'url_archivo'               => $registro->url,
                        'nombre_archivo_original'   => null,
                        'activo'                    => $registro->activo,
                        'usuario_crea'              => $registro->usuario_crea,
                        'usuario_mod'               => $registro->usuario_mod,
                        'usuario_elim'              => $registro->usuario_elim,
                        'created_at'                => $registro->created_at,
                        'updated_at'                => $registro->updated_at,
                        'deleted_at'                => null,
                    ]);
                }
            }, 'id_atestado');
    }

    public function down(): void
    {
        // No se elimina información migrada para evitar pérdida accidental de datos.
        // Si se requiere revertir manualmente, comparar contra tbl_consultor_formacion_academica antes de borrar.
    }
};
