<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoFormacionSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['id_tipo_formacion' => 1, 'nombre' => 'Educación formal'],
            ['id_tipo_formacion' => 2, 'nombre' => 'Educación continua'],
            ['id_tipo_formacion' => 3, 'nombre' => 'Acreditación'],
            ['id_tipo_formacion' => 4, 'nombre' => 'Capacitación recibida'],
            ['id_tipo_formacion' => 5, 'nombre' => 'Capacitación impartida'],
            ['id_tipo_formacion' => 6, 'nombre' => 'Consultoría realizada'],
        ];

        foreach ($tipos as $tipo) {
            DB::table('tbl_tipo_formacion')->updateOrInsert(
                ['id_tipo_formacion' => $tipo['id_tipo_formacion']],
                [
                    'nombre'       => $tipo['nombre'],
                    'activo'       => true,
                    'usuario_crea' => null,
                    'usuario_mod'  => null,
                    'usuario_elim' => null,
                    'updated_at'   => now(),
                    'deleted_at'   => null,
                ]
            );
        }
    }
}
