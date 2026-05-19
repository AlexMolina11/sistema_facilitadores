<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class NivelAcademicoSeeder extends Seeder
{
    public function run(): void
    {
        $niveles = [
            ['id_nivel_academico' => 1, 'nombre' => 'Bachiller'],
            ['id_nivel_academico' => 2, 'nombre' => 'Técnico'],
            ['id_nivel_academico' => 3, 'nombre' => 'Universitario'],
            ['id_nivel_academico' => 4, 'nombre' => 'Postgrado'],
            ['id_nivel_academico' => 5, 'nombre' => 'Maestría'],
            ['id_nivel_academico' => 6, 'nombre' => 'Doctorado'],
            ['id_nivel_academico' => 7, 'nombre' => 'Otros'],
        ];

        foreach ($niveles as $nivel) {
            DB::table('tbl_nivel_academico')->insertOrIgnore([
                'id_nivel_academico' => $nivel['id_nivel_academico'],
                'nombre'             => $nivel['nombre'],
                'activo'             => true,
                'usuario_crea'       => null,
                'usuario_mod'        => null,
                'usuario_elim'       => null,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }
    }
}