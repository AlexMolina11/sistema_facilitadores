<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoReferenciaSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            [
                'id_tipo_referencia' => 1,
                'nombre' => 'Referencias Personales'
            ],
            [
                'id_tipo_referencia' => 2,
                'nombre' => 'Referencias Laborales'
            ],
        ];

        foreach ($tipos as $tipo) {
            DB::table('tbl_tipo_referencia')->insertOrIgnore([
                'id_tipo_referencia' => $tipo['id_tipo_referencia'],
                'nombre'             => $tipo['nombre'],
                'activo'             => true,

                // Auditoría
                'usuario_crea'       => 1,
                'usuario_mod'        => null,
                'usuario_elim'       => null,

                'created_at'         => now(),
                'updated_at'         => now(),
                'deleted_at'         => null,
            ]);
        }
    }
}