<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoAtestadoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['id_tipo_atestado' => 1, 'id_tipo_formacion' => 1, 'nombre' => 'Título'],
            ['id_tipo_atestado' => 2, 'id_tipo_formacion' => 1, 'nombre' => 'Diploma de graduación'],
            ['id_tipo_atestado' => 3, 'id_tipo_formacion' => 1, 'nombre' => 'Certificación de notas'],
            ['id_tipo_atestado' => 4, 'id_tipo_formacion' => 1, 'nombre' => 'Certificación de título'],
            ['id_tipo_atestado' => 5, 'id_tipo_formacion' => 1, 'nombre' => 'Pensum / plan de estudios'],
            ['id_tipo_atestado' => 6, 'id_tipo_formacion' => 1, 'nombre' => 'Constancia de egreso'],
            ['id_tipo_atestado' => 7, 'id_tipo_formacion' => 1, 'nombre' => 'Equivalencias'],
            ['id_tipo_atestado' => 8, 'id_tipo_formacion' => 1, 'nombre' => 'Incorporación de título extranjero'],
            ['id_tipo_atestado' => 9, 'id_tipo_formacion' => 1, 'nombre' => 'Auténtica de título'],
            ['id_tipo_atestado' => 10, 'id_tipo_formacion' => 1, 'nombre' => 'Escalafón docente'],
            ['id_tipo_atestado' => 11, 'id_tipo_formacion' => 2, 'nombre' => 'Diploma'],
            ['id_tipo_atestado' => 12, 'id_tipo_formacion' => 2, 'nombre' => 'Certificado de aprobación'],
            ['id_tipo_atestado' => 13, 'id_tipo_formacion' => 2, 'nombre' => 'Certificado de participación'],
            ['id_tipo_atestado' => 14, 'id_tipo_formacion' => 2, 'nombre' => 'Constancia'],
            ['id_tipo_atestado' => 15, 'id_tipo_formacion' => 2, 'nombre' => 'Credencial'],
            ['id_tipo_atestado' => 16, 'id_tipo_formacion' => 2, 'nombre' => 'Badge digital'],
            ['id_tipo_atestado' => 17, 'id_tipo_formacion' => 2, 'nombre' => 'Carta de finalización'],
            ];

        foreach ($tipos as $tipo) {
            DB::table('tbl_tipo_atestado')->insertOrIgnore([
                'id_tipo_atestado'  => $tipo['id_tipo_atestado'],
                'id_tipo_formacion' => $tipo['id_tipo_formacion'],
                'nombre'            => $tipo['nombre'],
                'activo'            => true,
                'usuario_crea'      => null,
                'usuario_mod'       => null,
                'usuario_elim'      => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }
}