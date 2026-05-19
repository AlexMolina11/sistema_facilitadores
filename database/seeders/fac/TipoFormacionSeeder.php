<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoFormacionSeeder extends Seeder
{
    public function run(): void
    {
        $usuarioCrea = DB::table('seg_usuarios')
            ->where('username', 'clainezr')
            ->value('id_usuario');

        $tipos = [
            ['id_tipo_formacion' => 1, 'nombre' => 'Educación Formal'],
            ['id_tipo_formacion' => 2, 'nombre' => 'Educación Continua'],
        ];

        foreach ($tipos as $tipo) {
            DB::table('tbl_tipo_formacion')->insertOrIgnore([
                'id_tipo_formacion' => $tipo['id_tipo_formacion'],
                'nombre'            => $tipo['nombre'],
                'activo'            => true,
                'usuario_crea'      => $usuarioCrea,
                'usuario_mod'       => null,
                'usuario_elim'      => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }
}