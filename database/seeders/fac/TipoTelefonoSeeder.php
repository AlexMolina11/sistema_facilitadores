<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoTelefonoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['id_tipo_telefono' => 1, 'nombre' => 'Móvil'],
            ['id_tipo_telefono' => 2, 'nombre' => 'Residencial'],
            ['id_tipo_telefono' => 3, 'nombre' => 'Trabajo'],
            ['id_tipo_telefono' => 4, 'nombre' => 'WhatsApp'],
            ['id_tipo_telefono' => 5, 'nombre' => 'Emergencia'],
        ];

        foreach ($tipos as $tipo) {
            DB::table('tbl_tipo_telefono')->insertOrIgnore([
                'id_tipo_telefono' => $tipo['id_tipo_telefono'],
                'nombre'           => $tipo['nombre'],
                'activo'           => true,
                'created_at'       => now(),
                'updated_at'       => now(),
            ]);
        }
    }
}