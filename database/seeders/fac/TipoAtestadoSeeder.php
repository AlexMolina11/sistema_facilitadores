<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoAtestadoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['id_tipo_atestado' => 1,  'id_tipo_formacion' => 1, 'nombre' => 'Título'],
            ['id_tipo_atestado' => 2,  'id_tipo_formacion' => 1, 'nombre' => 'Diploma de graduación'],
            ['id_tipo_atestado' => 3,  'id_tipo_formacion' => 1, 'nombre' => 'Certificación de notas'],
            ['id_tipo_atestado' => 4,  'id_tipo_formacion' => 1, 'nombre' => 'Certificación de título'],
            ['id_tipo_atestado' => 5,  'id_tipo_formacion' => 1, 'nombre' => 'Pensum / plan de estudios'],
            ['id_tipo_atestado' => 6,  'id_tipo_formacion' => 1, 'nombre' => 'Constancia de egreso'],
            ['id_tipo_atestado' => 7,  'id_tipo_formacion' => 1, 'nombre' => 'Equivalencias'],
            ['id_tipo_atestado' => 8,  'id_tipo_formacion' => 1, 'nombre' => 'Incorporación de título extranjero'],
            ['id_tipo_atestado' => 9,  'id_tipo_formacion' => 1, 'nombre' => 'Auténtica de título'],
            ['id_tipo_atestado' => 10, 'id_tipo_formacion' => 1, 'nombre' => 'Escalafón docente'],

            ['id_tipo_atestado' => 11, 'id_tipo_formacion' => 2, 'nombre' => 'Diploma'],
            ['id_tipo_atestado' => 12, 'id_tipo_formacion' => 2, 'nombre' => 'Certificado de aprobación'],
            ['id_tipo_atestado' => 13, 'id_tipo_formacion' => 2, 'nombre' => 'Certificado de participación'],
            ['id_tipo_atestado' => 14, 'id_tipo_formacion' => 2, 'nombre' => 'Constancia'],
            ['id_tipo_atestado' => 15, 'id_tipo_formacion' => 2, 'nombre' => 'Credencial'],
            ['id_tipo_atestado' => 16, 'id_tipo_formacion' => 2, 'nombre' => 'Badge digital'],
            ['id_tipo_atestado' => 17, 'id_tipo_formacion' => 2, 'nombre' => 'Carta de finalización'],

            ['id_tipo_atestado' => 18, 'id_tipo_formacion' => 3, 'nombre' => 'Certificación profesional'],
            ['id_tipo_atestado' => 19, 'id_tipo_formacion' => 3, 'nombre' => 'Acreditación institucional'],
            ['id_tipo_atestado' => 20, 'id_tipo_formacion' => 3, 'nombre' => 'Licencia o autorización'],

            ['id_tipo_atestado' => 21, 'id_tipo_formacion' => 4, 'nombre' => 'Constancia de capacitación recibida'],
            ['id_tipo_atestado' => 22, 'id_tipo_formacion' => 4, 'nombre' => 'Diploma de capacitación recibida'],
            ['id_tipo_atestado' => 23, 'id_tipo_formacion' => 4, 'nombre' => 'Certificado de capacitación recibida'],

            ['id_tipo_atestado' => 24, 'id_tipo_formacion' => 5, 'nombre' => 'Constancia de capacitación impartida'],
            ['id_tipo_atestado' => 25, 'id_tipo_formacion' => 5, 'nombre' => 'Carta de satisfacción'],
            ['id_tipo_atestado' => 26, 'id_tipo_formacion' => 5, 'nombre' => 'Informe de evento impartido'],

            ['id_tipo_atestado' => 27, 'id_tipo_formacion' => 6, 'nombre' => 'Contrato de consultoría'],
            ['id_tipo_atestado' => 28, 'id_tipo_formacion' => 6, 'nombre' => 'Constancia de consultoría realizada'],
            ['id_tipo_atestado' => 29, 'id_tipo_formacion' => 6, 'nombre' => 'Carta de recomendación del cliente'],
            ['id_tipo_atestado' => 30, 'id_tipo_formacion' => 6, 'nombre' => 'Informe de consultoría'],
        ];

        foreach ($tipos as $tipo) {
            DB::table('tbl_tipo_atestado')->updateOrInsert(
                ['id_tipo_atestado' => $tipo['id_tipo_atestado']],
                [
                    'id_tipo_formacion' => $tipo['id_tipo_formacion'],
                    'nombre'            => $tipo['nombre'],
                    'activo'            => true,
                    'usuario_crea'      => null,
                    'usuario_mod'       => null,
                    'usuario_elim'      => null,
                    'updated_at'        => now(),
                    'deleted_at'        => null,
                ]
            );
        }
    }
}
