<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HabilidadSeeder extends Seeder
{
    public function run(): void
    {
        $habilidades = [

            [
                'id_habilidad' => 1,
                'id_tipo_habilidad' => 1,
                'nombre' => 'Comunicación efectiva',
            ],

            [
                'id_habilidad' => 2,
                'id_tipo_habilidad' => 1,
                'nombre' => 'Trabajo en equipo',
            ],

            [
                'id_habilidad' => 3,
                'id_tipo_habilidad' => 2,
                'nombre' => 'Laravel',
            ],

        ];

        foreach ($habilidades as $habilidad) {

            DB::table('tbl_habilidad')->insertOrIgnore([

                'id_habilidad' => $habilidad['id_habilidad'],
                'id_tipo_habilidad' => $habilidad['id_tipo_habilidad'],
                'nombre' => $habilidad['nombre'],

                'activo' => true,

                'usuario_crea' => null,
                'usuario_mod' => null,
                'usuario_elim' => null,

                'created_at' => now(),
                'updated_at' => now(),
            ]);
        }
    }
}