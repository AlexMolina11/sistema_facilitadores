<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentoSeeder extends Seeder
{
    public function run(): void
    {
        $departamentos = [
            ['id_departamento' => 1,  'id_pais' => 1,   'nombre_departamento' => 'AHUACHAPAN',              'mh_codigo_depto' => '01',   'georeferencia' => '13.91990, -89.84838'],
            ['id_departamento' => 2,  'id_pais' => 1,   'nombre_departamento' => 'SANTA ANA',               'mh_codigo_depto' => '02',   'georeferencia' => '13.994865131704081, -89.55734711001655'],
            ['id_departamento' => 3,  'id_pais' => 1,   'nombre_departamento' => 'SONSONATE',               'mh_codigo_depto' => '03',   'georeferencia' => '13.7212809583141, -89.72820465817179'],
            ['id_departamento' => 4,  'id_pais' => 1,   'nombre_departamento' => 'CHALATENANGO',            'mh_codigo_depto' => '04',   'georeferencia' => '14.041465490969983, -88.940136476361'],
            ['id_departamento' => 5,  'id_pais' => 1,   'nombre_departamento' => 'LA LIBERTAD',             'mh_codigo_depto' => '05',   'georeferencia' => '13.67649774723456, -89.28933716613753'],
            ['id_departamento' => 6,  'id_pais' => 1,   'nombre_departamento' => 'SAN SALVADOR',            'mh_codigo_depto' => '06',   'georeferencia' => '13.705390443140885, -89.18934494805714'],
            ['id_departamento' => 7,  'id_pais' => 1,   'nombre_departamento' => 'CUSCATLAN',               'mh_codigo_depto' => '07',   'georeferencia' => '13.719702563006578, -88.93275268700701'],
            ['id_departamento' => 8,  'id_pais' => 1,   'nombre_departamento' => 'LA PAZ',                  'mh_codigo_depto' => '08',   'georeferencia' => '13.508325999223839, -88.86934386001928'],
            ['id_departamento' => 9,  'id_pais' => 1,   'nombre_departamento' => 'CABAÑAS',                 'mh_codigo_depto' => '09',   'georeferencia' => '13.876078305025285, -88.62742510234689'],
            ['id_departamento' => 10, 'id_pais' => 1,   'nombre_departamento' => 'SAN VICENTE',             'mh_codigo_depto' => '10',   'georeferencia' => '13.64548643255488, -88.78569418885374'],
            ['id_departamento' => 11, 'id_pais' => 1,   'nombre_departamento' => 'USULUTAN',                'mh_codigo_depto' => '11',   'georeferencia' => '13.34463527371611, -88.43945294467943'],
            ['id_departamento' => 12, 'id_pais' => 1,   'nombre_departamento' => 'SAN MIGUEL',              'mh_codigo_depto' => '12',   'georeferencia' => '13.482725985289227, -88.17539290050274'],
            ['id_departamento' => 13, 'id_pais' => 1,   'nombre_departamento' => 'MORAZAN',                 'mh_codigo_depto' => '13',   'georeferencia' => '13.695348616660194, -88.10626924570427'],
            ['id_departamento' => 14, 'id_pais' => 1,   'nombre_departamento' => 'LA UNION',                'mh_codigo_depto' => '14',   'georeferencia' => '13.337798558539337, -87.84332160234985'],
            ['id_departamento' => 15, 'id_pais' => 9,   'nombre_departamento' => 'GENÉRICO',                'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 16, 'id_pais' => 2,   'nombre_departamento' => 'GENÉRICO',                'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 17, 'id_pais' => 13,  'nombre_departamento' => 'BRASIL NO DEFINIDO',      'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 18, 'id_pais' => 11,  'nombre_departamento' => 'CANADA NO DEFINIDO',      'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 19, 'id_pais' => 17,  'nombre_departamento' => 'JAPON NO DEFINIDO',       'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 20, 'id_pais' => 4,   'nombre_departamento' => 'MEXICO DF',               'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 21, 'id_pais' => 2,   'nombre_departamento' => 'CHICAGO',                 'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 22, 'id_pais' => 9,   'nombre_departamento' => 'GUATEMALA',               'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 23, 'id_pais' => 2,   'nombre_departamento' => 'CALIFORNIA',              'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 24, 'id_pais' => 2,   'nombre_departamento' => 'FLORIDA',                 'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 25, 'id_pais' => 28,  'nombre_departamento' => 'QUITO',                   'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 26, 'id_pais' => 3,   'nombre_departamento' => 'LA CEIBA',                'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 27, 'id_pais' => 3,   'nombre_departamento' => 'TEGUCIGALPA',             'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 28, 'id_pais' => 3,   'nombre_departamento' => 'SAN PEDRO SULA',          'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 29, 'id_pais' => 1,   'nombre_departamento' => 'EL SALVADOR NO DEFINIDO', 'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 30, 'id_pais' => 27,  'nombre_departamento' => 'NICARAGUA',               'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 31, 'id_pais' => 7,   'nombre_departamento' => 'COSTA RICA',              'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 32, 'id_pais' => 29,  'nombre_departamento' => 'BOGOTA',                  'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 33, 'id_pais' => 2,   'nombre_departamento' => 'ESTADOS UNIDOS',          'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 34, 'id_pais' => 30,  'nombre_departamento' => 'LUXEMBURGO',              'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 35, 'id_pais' => 4,   'nombre_departamento' => 'PUEBLA',                  'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 36, 'id_pais' => 39,  'nombre_departamento' => 'REPUBLICA DOMINICANA',    'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 37, 'id_pais' => 8,   'nombre_departamento' => 'PANAMÁ',                  'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 38, 'id_pais' => 40,  'nombre_departamento' => 'BELICE',                  'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 39, 'id_pais' => 41,  'nombre_departamento' => 'BERLIN',                  'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 40, 'id_pais' => 42,  'nombre_departamento' => 'ITALIA',                  'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 41, 'id_pais' => 5,   'nombre_departamento' => 'PUERTO RICO',             'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 42, 'id_pais' => 19,  'nombre_departamento' => 'JAPON',                   'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 43, 'id_pais' => 26,  'nombre_departamento' => 'VENEZUELA',               'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 44, 'id_pais' => 31,  'nombre_departamento' => 'ARGENTINA',               'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 45, 'id_pais' => 32,  'nombre_departamento' => 'PORTUGAL',                'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 46, 'id_pais' => 35,  'nombre_departamento' => 'HOLANDA',                 'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 47, 'id_pais' => 36,  'nombre_departamento' => 'SERBIA',                  'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 48, 'id_pais' => 37,  'nombre_departamento' => 'SUIZA',                   'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 49, 'id_pais' => 38,  'nombre_departamento' => 'ESPAÑA',                  'mh_codigo_depto' => null,   'georeferencia' => null],
            // id 50 ausente en el origen — se respeta
            ['id_departamento' => 51, 'id_pais' => 44,  'nombre_departamento' => 'LONDRES',                 'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 52, 'id_pais' => 29,  'nombre_departamento' => 'BARRANQUILLA',            'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 53, 'id_pais' => 46,  'nombre_departamento' => 'BOLIVIA',                 'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 54, 'id_pais' => 47,  'nombre_departamento' => 'LIMA',                    'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 55, 'id_pais' => 29,  'nombre_departamento' => 'MEDELLIN',                'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 56, 'id_pais' => 4,   'nombre_departamento' => 'NUEVO LEÓN',              'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 57, 'id_pais' => 49,  'nombre_departamento' => 'SINGAPORE',               'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 58, 'id_pais' => 17,  'nombre_departamento' => 'GENÉRICO',                'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 59, 'id_pais' => 52,  'nombre_departamento' => 'IRLANDA',                 'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 60, 'id_pais' => 2,   'nombre_departamento' => 'ARKANSAS',                'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 61, 'id_pais' => 7,   'nombre_departamento' => 'ALAJUELA',                'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 62, 'id_pais' => 41,  'nombre_departamento' => 'BREMEN',                  'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 63, 'id_pais' => 48,  'nombre_departamento' => 'MONTEVIDEO',              'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 64, 'id_pais' => 7,   'nombre_departamento' => 'SAN JOSE',                'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 65, 'id_pais' => 54,  'nombre_departamento' => 'GENERICO',                'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 66, 'id_pais' => 31,  'nombre_departamento' => 'BUENOS AIRES',            'mh_codigo_depto' => null,   'georeferencia' => null],
            ['id_departamento' => 67, 'id_pais' => 970, 'nombre_departamento' => 'MOSCÚ',                   'mh_codigo_depto' => null,   'georeferencia' => null],
        ];

        $now = now();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($departamentos as $depto) {
            DB::table('tbl_departamento')->insert([
                'id_departamento'     => $depto['id_departamento'],
                'id_pais'             => $depto['id_pais'],
                'nombre_departamento' => trim($depto['nombre_departamento']),
                'mh_codigo_depto'     => $depto['mh_codigo_depto'],
                'georeferencia'       => $depto['georeferencia'],
                'activo'              => true,
                'usuario_crea'        => null,
                'usuario_mod'         => null,
                'usuario_elim'        => null,
                'created_at'          => $now,
                'updated_at'          => $now,
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}