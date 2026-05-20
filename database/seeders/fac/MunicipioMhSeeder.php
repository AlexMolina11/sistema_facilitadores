<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class MunicipioMhSeeder extends Seeder
{
    public function run(): void
    {
        $municipiosMh = [
            ['id_municipio_mh' => 1,  'municipio_mh_nombre' => 'OTRO (PARA EXTRANJEROS)', 'mh_codigo_municipio' => '00', 'id_departamento' => 0],
            ['id_municipio_mh' => 2,  'municipio_mh_nombre' => 'CABAÑAS OESTE',            'mh_codigo_municipio' => '10', 'id_departamento' => 9],
            ['id_municipio_mh' => 3,  'municipio_mh_nombre' => 'CABAÑAS ESTE',             'mh_codigo_municipio' => '11', 'id_departamento' => 9],
            ['id_municipio_mh' => 4,  'municipio_mh_nombre' => 'AHUACHAPÁN NORTE',         'mh_codigo_municipio' => '13', 'id_departamento' => 1],
            ['id_municipio_mh' => 5,  'municipio_mh_nombre' => 'AHUACHAPÁN CENTRO',        'mh_codigo_municipio' => '14', 'id_departamento' => 1],
            ['id_municipio_mh' => 6,  'municipio_mh_nombre' => 'SANTA ANA NORTE',          'mh_codigo_municipio' => '14', 'id_departamento' => 2],
            ['id_municipio_mh' => 7,  'municipio_mh_nombre' => 'AHUACHAPÁN SUR',           'mh_codigo_municipio' => '15', 'id_departamento' => 1],
            ['id_municipio_mh' => 8,  'municipio_mh_nombre' => 'SANTA ANA CENTRO',         'mh_codigo_municipio' => '15', 'id_departamento' => 2],
            ['id_municipio_mh' => 9,  'municipio_mh_nombre' => 'SANTA ANA ESTE',           'mh_codigo_municipio' => '16', 'id_departamento' => 2],
            ['id_municipio_mh' => 10, 'municipio_mh_nombre' => 'SANTA ANA OESTE',          'mh_codigo_municipio' => '17', 'id_departamento' => 2],
            ['id_municipio_mh' => 11, 'municipio_mh_nombre' => 'SONSONATE NORTE',          'mh_codigo_municipio' => '17', 'id_departamento' => 3],
            ['id_municipio_mh' => 12, 'municipio_mh_nombre' => 'CUSCATLÁN NORTE',          'mh_codigo_municipio' => '17', 'id_departamento' => 7],
            ['id_municipio_mh' => 13, 'municipio_mh_nombre' => 'SONSONATE CENTRO',         'mh_codigo_municipio' => '18', 'id_departamento' => 3],
            ['id_municipio_mh' => 14, 'municipio_mh_nombre' => 'CUSCATLÁN SUR',            'mh_codigo_municipio' => '18', 'id_departamento' => 7],
            ['id_municipio_mh' => 15, 'municipio_mh_nombre' => 'SONSONATE ESTE',           'mh_codigo_municipio' => '19', 'id_departamento' => 3],
            ['id_municipio_mh' => 16, 'municipio_mh_nombre' => 'SONSONATE OESTE',          'mh_codigo_municipio' => '20', 'id_departamento' => 3],
            ['id_municipio_mh' => 17, 'municipio_mh_nombre' => 'SAN SALVADOR NORTE',       'mh_codigo_municipio' => '20', 'id_departamento' => 6],
            ['id_municipio_mh' => 18, 'municipio_mh_nombre' => 'SAN SALVADOR OESTE',       'mh_codigo_municipio' => '21', 'id_departamento' => 6],
            ['id_municipio_mh' => 19, 'municipio_mh_nombre' => 'SAN SALVADOR ESTE',        'mh_codigo_municipio' => '22', 'id_departamento' => 6],
            ['id_municipio_mh' => 20, 'municipio_mh_nombre' => 'LA LIBERTAD NORTE',        'mh_codigo_municipio' => '23', 'id_departamento' => 5],
            ['id_municipio_mh' => 21, 'municipio_mh_nombre' => 'SAN SALVADOR CENTRO',      'mh_codigo_municipio' => '23', 'id_departamento' => 6],
            ['id_municipio_mh' => 22, 'municipio_mh_nombre' => 'LA PAZ OESTE',             'mh_codigo_municipio' => '23', 'id_departamento' => 8],
            ['id_municipio_mh' => 23, 'municipio_mh_nombre' => 'LA LIBERTAD CENTRO',       'mh_codigo_municipio' => '24', 'id_departamento' => 5],
            ['id_municipio_mh' => 24, 'municipio_mh_nombre' => 'SAN SALVADOR SUR',         'mh_codigo_municipio' => '24', 'id_departamento' => 6],
            ['id_municipio_mh' => 25, 'municipio_mh_nombre' => 'LA PAZ CENTRO',            'mh_codigo_municipio' => '24', 'id_departamento' => 8],
            ['id_municipio_mh' => 26, 'municipio_mh_nombre' => 'LA LIBERTAD OESTE',        'mh_codigo_municipio' => '25', 'id_departamento' => 5],
            ['id_municipio_mh' => 27, 'municipio_mh_nombre' => 'LA PAZ ESTE',              'mh_codigo_municipio' => '25', 'id_departamento' => 8],
            ['id_municipio_mh' => 28, 'municipio_mh_nombre' => 'LA LIBERTAD ESTE',         'mh_codigo_municipio' => '26', 'id_departamento' => 5],
            ['id_municipio_mh' => 29, 'municipio_mh_nombre' => 'LA LIBERTAD COSTA',        'mh_codigo_municipio' => '27', 'id_departamento' => 5],
            ['id_municipio_mh' => 30, 'municipio_mh_nombre' => 'LA LIBERTAD SUR',          'mh_codigo_municipio' => '28', 'id_departamento' => 5],
            ['id_municipio_mh' => 31, 'municipio_mh_nombre' => 'CHALATENANGO NORTE',       'mh_codigo_municipio' => '34', 'id_departamento' => 4],
            ['id_municipio_mh' => 32, 'municipio_mh_nombre' => 'CHALATENANGO CENTRO',      'mh_codigo_municipio' => '35', 'id_departamento' => 4],
            ['id_municipio_mh' => 33, 'municipio_mh_nombre' => 'CHALATENANGO SUR',         'mh_codigo_municipio' => '36', 'id_departamento' => 4],
            ['id_municipio_mh' => 34, 'municipio_mh_nombre' => 'SAN VICENTE NORTE',        'mh_codigo_municipio' => '14', 'id_departamento' => 10],
            ['id_municipio_mh' => 35, 'municipio_mh_nombre' => 'LA UNIÓN NORTE',           'mh_codigo_municipio' => '19', 'id_departamento' => 14],
            ['id_municipio_mh' => 36, 'municipio_mh_nombre' => 'LA UNIÓN SUR',             'mh_codigo_municipio' => '20', 'id_departamento' => 14],
            ['id_municipio_mh' => 37, 'municipio_mh_nombre' => 'MORAZÁN NORTE',            'mh_codigo_municipio' => '27', 'id_departamento' => 13],
            ['id_municipio_mh' => 38, 'municipio_mh_nombre' => 'MORAZÁN SUR',              'mh_codigo_municipio' => '28', 'id_departamento' => 13],
            ['id_municipio_mh' => 39, 'municipio_mh_nombre' => 'SAN MIGUEL CENTRO',        'mh_codigo_municipio' => '22', 'id_departamento' => 12],
            ['id_municipio_mh' => 40, 'municipio_mh_nombre' => 'SAN MIGUEL NORTE',         'mh_codigo_municipio' => '21', 'id_departamento' => 12],
            ['id_municipio_mh' => 41, 'municipio_mh_nombre' => 'SAN MIGUEL OESTE',         'mh_codigo_municipio' => '23', 'id_departamento' => 12],
            ['id_municipio_mh' => 42, 'municipio_mh_nombre' => 'SAN VICENTE SUR',          'mh_codigo_municipio' => '15', 'id_departamento' => 10],
            ['id_municipio_mh' => 43, 'municipio_mh_nombre' => 'USULUTÁN ESTE',            'mh_codigo_municipio' => '25', 'id_departamento' => 11],
            ['id_municipio_mh' => 44, 'municipio_mh_nombre' => 'USULUTÁN NORTE',           'mh_codigo_municipio' => '24', 'id_departamento' => 11],
            ['id_municipio_mh' => 45, 'municipio_mh_nombre' => 'USULUTÁN OESTE',           'mh_codigo_municipio' => '26', 'id_departamento' => 11],
        ];

        $now = now();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($municipiosMh as $item) {
            DB::table('tbl_municipio_mh')->insert([
                'id_municipio_mh'      => $item['id_municipio_mh'],
                'municipio_mh_nombre'  => trim($item['municipio_mh_nombre']),
                'mh_codigo_municipio'  => $item['mh_codigo_municipio'],
                'id_departamento'      => $item['id_departamento'] === 0 ? null : $item['id_departamento'],
                'activo'               => true,
                'usuario_crea'         => null,
                'usuario_mod'          => null,
                'usuario_elim'         => null,
                'created_at'           => $now,
                'updated_at'           => $now,
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}