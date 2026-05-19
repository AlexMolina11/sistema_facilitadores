<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class DepartamentoSeeder extends Seeder
{
    public function run(): void
    {

        $departamentos = [
            ['id_departamento' => 1,  'id_pais' => 1,   'nombre_departamento' => 'AHUACHAPAN'],
            ['id_departamento' => 2,  'id_pais' => 1,   'nombre_departamento' => 'SANTA ANA'],
            ['id_departamento' => 3,  'id_pais' => 1,   'nombre_departamento' => 'SONSONATE'],
            ['id_departamento' => 4,  'id_pais' => 1,   'nombre_departamento' => 'CHALATENANGO'],
            ['id_departamento' => 5,  'id_pais' => 1,   'nombre_departamento' => 'LA LIBERTAD'],
            ['id_departamento' => 6,  'id_pais' => 1,   'nombre_departamento' => 'SAN SALVADOR'],
            ['id_departamento' => 7,  'id_pais' => 1,   'nombre_departamento' => 'CUSCATLAN'],
            ['id_departamento' => 8,  'id_pais' => 1,   'nombre_departamento' => 'LA PAZ'],
            ['id_departamento' => 9,  'id_pais' => 1,   'nombre_departamento' => 'CABAÑAS'],
            ['id_departamento' => 10, 'id_pais' => 1,   'nombre_departamento' => 'SAN VICENTE'],
            ['id_departamento' => 11, 'id_pais' => 1,   'nombre_departamento' => 'USULUTAN'],
            ['id_departamento' => 12, 'id_pais' => 1,   'nombre_departamento' => 'SAN MIGUEL'],
            ['id_departamento' => 13, 'id_pais' => 1,   'nombre_departamento' => 'MORAZAN'],
            ['id_departamento' => 14, 'id_pais' => 1,   'nombre_departamento' => 'LA UNION'],
            ['id_departamento' => 15, 'id_pais' => 9,   'nombre_departamento' => 'GENÉRICO'],
            ['id_departamento' => 16, 'id_pais' => 2,   'nombre_departamento' => 'GENÉRICO'],
            ['id_departamento' => 17, 'id_pais' => 13,  'nombre_departamento' => 'BRASIL NO DEFINIDO'],
            ['id_departamento' => 18, 'id_pais' => 11,  'nombre_departamento' => 'CANADA NO DEFINIDO'],
            ['id_departamento' => 19, 'id_pais' => 17,  'nombre_departamento' => 'JAPON NO DEFINIDO'],
            ['id_departamento' => 20, 'id_pais' => 4,   'nombre_departamento' => 'MEXICO DF'],
            ['id_departamento' => 21, 'id_pais' => 2,   'nombre_departamento' => 'CHICAGO'],
            ['id_departamento' => 22, 'id_pais' => 9,   'nombre_departamento' => 'GUATEMALA'],
            ['id_departamento' => 23, 'id_pais' => 2,   'nombre_departamento' => 'CALIFORNIA'],
            ['id_departamento' => 24, 'id_pais' => 2,   'nombre_departamento' => 'FLORIDA'],
            ['id_departamento' => 25, 'id_pais' => 28,  'nombre_departamento' => 'QUITO'],
            ['id_departamento' => 26, 'id_pais' => 3,   'nombre_departamento' => 'LA CEIBA'],
            ['id_departamento' => 27, 'id_pais' => 3,   'nombre_departamento' => 'TEGUCIGALPA'],
            ['id_departamento' => 28, 'id_pais' => 3,   'nombre_departamento' => 'SAN PEDRO SULA'],
            ['id_departamento' => 29, 'id_pais' => 1,   'nombre_departamento' => 'EL SALVADOR NO DEFINIDO'],
            ['id_departamento' => 30, 'id_pais' => 27,  'nombre_departamento' => 'NICARAGUA'],
            ['id_departamento' => 31, 'id_pais' => 7,   'nombre_departamento' => 'COSTA RICA'],
            ['id_departamento' => 32, 'id_pais' => 29,  'nombre_departamento' => 'BOGOTA'],
            ['id_departamento' => 33, 'id_pais' => 2,   'nombre_departamento' => 'ESTADOS UNIDOS'],
            ['id_departamento' => 34, 'id_pais' => 30,  'nombre_departamento' => 'LUXEMBURGO'],
            ['id_departamento' => 35, 'id_pais' => 4,   'nombre_departamento' => 'PUEBLA'],
            ['id_departamento' => 36, 'id_pais' => 39,  'nombre_departamento' => 'REPUBLICA DOMINICANA'],
            ['id_departamento' => 37, 'id_pais' => 8,   'nombre_departamento' => 'PANAMÁ'],
            ['id_departamento' => 38, 'id_pais' => 40,  'nombre_departamento' => 'BELICE'],
            ['id_departamento' => 39, 'id_pais' => 41,  'nombre_departamento' => 'BERLIN'],
            ['id_departamento' => 40, 'id_pais' => 42,  'nombre_departamento' => 'ITALIA'],
            ['id_departamento' => 41, 'id_pais' => 5,   'nombre_departamento' => 'PUERTO RICO'],
            ['id_departamento' => 42, 'id_pais' => 19,  'nombre_departamento' => 'JAPON'],
            ['id_departamento' => 43, 'id_pais' => 26,  'nombre_departamento' => 'VENEZUELA'],
            ['id_departamento' => 44, 'id_pais' => 31,  'nombre_departamento' => 'ARGENTINA'],
            ['id_departamento' => 45, 'id_pais' => 32,  'nombre_departamento' => 'PORTUGAL'],
            ['id_departamento' => 46, 'id_pais' => 35,  'nombre_departamento' => 'HOLANDA'],
            ['id_departamento' => 47, 'id_pais' => 36,  'nombre_departamento' => 'SERBIA'],
            ['id_departamento' => 48, 'id_pais' => 37,  'nombre_departamento' => 'SUIZA'],
            ['id_departamento' => 49, 'id_pais' => 38,  'nombre_departamento' => 'ESPAÑA'],
            ['id_departamento' => 51, 'id_pais' => 44,  'nombre_departamento' => 'LONDRES'],
            ['id_departamento' => 52, 'id_pais' => 29,  'nombre_departamento' => 'BARRANQUILLA'],
            ['id_departamento' => 53, 'id_pais' => 46,  'nombre_departamento' => 'BOLIVIA'],
            ['id_departamento' => 54, 'id_pais' => 47,  'nombre_departamento' => 'LIMA'],
            ['id_departamento' => 55, 'id_pais' => 29,  'nombre_departamento' => 'MEDELLIN'],
            ['id_departamento' => 56, 'id_pais' => 4,   'nombre_departamento' => 'NUEVO LEÓN'],
            ['id_departamento' => 57, 'id_pais' => 49,  'nombre_departamento' => 'SINGAPORE'],
            ['id_departamento' => 58, 'id_pais' => 17,  'nombre_departamento' => 'GENÉRICO'],
            ['id_departamento' => 59, 'id_pais' => 52,  'nombre_departamento' => 'IRLANDA'],
            ['id_departamento' => 60, 'id_pais' => 2,   'nombre_departamento' => 'ARKANSAS'],
            ['id_departamento' => 61, 'id_pais' => 7,   'nombre_departamento' => 'ALAJUELA'],
            ['id_departamento' => 62, 'id_pais' => 41,  'nombre_departamento' => 'BREMEN'],
            ['id_departamento' => 63, 'id_pais' => 48,  'nombre_departamento' => 'MONTEVIDEO'],
            ['id_departamento' => 64, 'id_pais' => 7,   'nombre_departamento' => 'SAN JOSE'],
            ['id_departamento' => 65, 'id_pais' => 54,  'nombre_departamento' => 'GENERICO'],
            ['id_departamento' => 66, 'id_pais' => 31,  'nombre_departamento' => 'BUENOS AIRES'],
            ['id_departamento' => 67, 'id_pais' => 970, 'nombre_departamento' => 'MOSCÚ'],
        ];

        $rows = array_map(fn($d) => [
            'id_departamento' => $d['id_departamento'],
            'id_pais'         => $d['id_pais'],
            'nombre_departamento'          => $d['nombre_departamento'],
            'activo'          => true,
            'usuario_crea'    => null,
            'usuario_mod'     => null,
            'usuario_elim'    => null,
            'created_at'      => now(),
            'updated_at'      => now(),
            'deleted_at'      => null,
        ], $departamentos);

        DB::table('tbl_departamento')->insertOrIgnore($rows);
    }
}