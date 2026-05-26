<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class SexoSeeder extends Seeder
{
    public function run(): void
    {
        $now = now();

        $sexos = [
            ['id_sexo' => 1, 'nombre' => 'Masculino'],
            ['id_sexo' => 2, 'nombre' => 'Femenino'],
            ['id_sexo' => 3, 'nombre' => 'Otro'],
        ];

        foreach ($sexos as $sexo) {
            DB::table('tbl_sexo')->updateOrInsert(
                ['id_sexo' => $sexo['id_sexo']],
                [
                    'nombre' => $sexo['nombre'],
                    'activo' => true,
                    'usuario_crea' => null,
                    'usuario_mod' => null,
                    'usuario_elim' => null,
                    'created_at' => $now,
                    'updated_at' => $now,
                    'deleted_at' => null,
                ]
            );
        }
    }
}