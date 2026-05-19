<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IdiomaNivelSeeder extends Seeder
{
    public function run(): void
    {

        $niveles = [
            ['id_idioma_nivel' => 1,  'nombre' => 'Básico'],
            ['id_idioma_nivel' => 2,  'nombre' => 'Intermedio'],
            ['id_idioma_nivel' => 3,  'nombre' => 'Avanzado'],
            ['id_idioma_nivel' => 4,  'nombre' => 'Técnico'],
            ['id_idioma_nivel' => 7,  'nombre' => 'Nativo'],
            ];

        foreach ($niveles as $nivel) {
            DB::table('tbl_idioma_nivel')->insertOrIgnore([
                'id_idioma_nivel' => $nivel['id_idioma_nivel'],
                'nombre'          => $nivel['nombre'],
                'activo'          => true,
                'usuario_crea'    => null,
                'usuario_mod'     => null,
                'usuario_elim'    => null,
                'created_at'      => now(),
                'updated_at'      => now(),
            ]);
        }
    }
}