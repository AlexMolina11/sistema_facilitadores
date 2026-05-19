<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoDisponibilidadSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['id_tipo_disponibilidad' => 1, 'nombre' => 'Disponible a partir de una fecha'],
            ['id_tipo_disponibilidad' => 2, 'nombre' => 'Inmediata'],
            ['id_tipo_disponibilidad' => 3, 'nombre' => 'No disponible actualmente'],
        ];

        $now = now();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($tipos as $tipo) {
            DB::table('tbl_tipo_disponibilidad')->insert([
                'id_tipo_disponibilidad' => $tipo['id_tipo_disponibilidad'],
                'nombre'                 => $tipo['nombre'],
                'activo'                 => true,
                'usuario_crea'           => null,
                'usuario_mod'            => null,
                'usuario_elim'           => null,
                'created_at'             => $now,
                'updated_at'             => $now,
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}