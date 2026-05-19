<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoHabilidadSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['nombre' => 'Áreas de especialización'],
            ['nombre' => 'Habilidades blandas'],
            ['nombre' => 'Habilidades técnicas'],
        ];

        foreach ($tipos as $tipo) {
            DB::table('tbl_tipo_habilidad')->updateOrInsert(
                [
                    'nombre' => $tipo['nombre'],
                ],
                [
                    'activo'       => true,
                    'usuario_crea' => null,
                    'usuario_mod'  => null,
                    'usuario_elim' => null,
                    'deleted_at'   => null,
                    'created_at'   => now(),
                    'updated_at'   => now(),
                ]
            );
        }
    }
}