<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoAtestadoSeeder extends Seeder
{
    public function run(): void
    {
        $usuarioCrea = DB::table('seg_usuarios')
            ->where('username', 'clainezr')
            ->value('id_usuario');

        $tipos = [
            ['id_tipo_atestado' => 1, 'id_tipo_formacion' => 1, 'nombre' => 'NIT'],
            ['id_tipo_atestado' => 2, 'id_tipo_formacion' => 1, 'nombre' => 'NRC'],
            ['id_tipo_atestado' => 3, 'id_tipo_formacion' => 1, 'nombre' => 'DUI'],
            ['id_tipo_atestado' => 4, 'id_tipo_formacion' => 1, 'nombre' => 'PASAPORTE'],
            ['id_tipo_atestado' => 5, 'id_tipo_formacion' => 1, 'nombre' => 'DIPLOMA'],
            ['id_tipo_atestado' => 6, 'id_tipo_formacion' => 1, 'nombre' => 'ACREDITACION'],
        ];

        foreach ($tipos as $tipo) {
            DB::table('tbl_tipo_atestado')->insertOrIgnore([
                'id_tipo_atestado'  => $tipo['id_tipo_atestado'],
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