<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoFormacionSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            [
                'id_tipo_formacion' => 1,
                'nombre' => 'Académica',
            ],
            [
                'id_tipo_formacion' => 2,
                'nombre' => 'Técnica',
            ],
            [
                'id_tipo_formacion' => 3,
                'nombre' => 'Profesional',
            ],
        ];

        foreach ($tipos as $tipo) {

            DB::table('tbl_tipo_formacion')->insertOrIgnore([

                'id_tipo_formacion' => $tipo['id_tipo_formacion'],
                'nombre'            => $tipo['nombre'],
                'activo'            => true,

                'usuario_crea'      => 1,
                'usuario_mod'       => null,
                'usuario_elim'      => null,

                'created_at'        => now(),
                'updated_at'        => now(),
                'deleted_at'        => null,
            ]);
        }
    }
}