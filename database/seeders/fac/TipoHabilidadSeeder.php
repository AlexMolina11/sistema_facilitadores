<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoHabilidadSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            [
                'nombre' => 'Áreas de especialización',
                'activo' => true,
            ],
            [
                'nombre' => 'Habilidades blandas',
                'activo' => true,
            ],
            [
                'nombre' => 'Habilidades técnicas',
                'activo' => true,
            ],
        ];

        foreach ($tipos as $tipo) {
            DB::table('tbl_tipo_habilidad')->updateOrInsert(
                [
                    'nombre' => $tipo['nombre'],
                ],
                [
                    'activo' => $tipo['activo'],
                    'usuario_crea' => null,
                    'usuario_mod' => null,
                    'usuario_elim' => null,
                    'deleted_at' => null,
                    'created_at' => now(),
                    'updated_at' => now(),
                ]
            );
        }
    }
}