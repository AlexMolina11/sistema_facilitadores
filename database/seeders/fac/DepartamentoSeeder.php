<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
use Carbon\Carbon;

class DepartamentoSeeder extends Seeder
{
    public function run(): void
    {
        $usuarioCrea = DB::table('seg_usuarios')
            ->where('username', 'clainezr')
            ->value('id_usuario');

        $now = Carbon::today();

        $departamentos = [
            ['id_departamento' => 1,  'id_pais' => 1,   'nombre' => 'AHUACHAPAN'],
            ['id_departamento' => 2,  'id_pais' => 1,   'nombre' => 'SANTA ANA'],
            ['id_departamento' => 3,  'id_pais' => 1,   'nombre' => 'SONSONATE'],
            ['id_departamento' => 4,  'id_pais' => 1,   'nombre' => 'CHALATENANGO'],
            ['id_departamento' => 5,  'id_pais' => 1,   'nombre' => 'LA LIBERTAD'],
            ['id_departamento' => 6,  'id_pais' => 1,   'nombre' => 'SAN SALVADOR'],
            ['id_departamento' => 7,  'id_pais' => 1,   'nombre' => 'CUSCATLAN'],
            ['id_departamento' => 8,  'id_pais' => 1,   'nombre' => 'LA PAZ'],
            ['id_departamento' => 9,  'id_pais' => 1,   'nombre' => 'CABAÑAS'],
            ['id_departamento' => 10, 'id_pais' => 1,   'nombre' => 'SAN VICENTE'],
            ['id_departamento' => 11, 'id_pais' => 1,   'nombre' => 'USULUTAN'],
            ['id_departamento' => 12, 'id_pais' => 1,   'nombre' => 'SAN MIGUEL'],
            ['id_departamento' => 13, 'id_pais' => 1,   'nombre' => 'MORAZAN'],
            ['id_departamento' => 14, 'id_pais' => 1,   'nombre' => 'LA UNION'],
            ['id_departamento' => 15, 'id_pais' => 9,   'nombre' => 'GENÉRICO'],
            ['id_departamento' => 16, 'id_pais' => 2,   'nombre' => 'GENÉRICO'],
            ['id_departamento' => 17, 'id_pais' => 13,  'nombre' => 'BRASIL NO DEFINIDO'],
            ['id_departamento' => 18, 'id_pais' => 11,  'nombre' => 'CANADA NO DEFINIDO'],
            ['id_departamento' => 19, 'id_pais' => 17,  'nombre' => 'JAPON NO DEFINIDO'],
            ['id_departamento' => 20, 'id_pais' => 4,   'nombre' => 'MEXICO DF'],
            ['id_departamento' => 21, 'id_pais' => 2,   'nombre' => 'CHICAGO'],
            ['id_departamento' => 22, 'id_pais' => 9,   'nombre' => 'GUATEMALA'],
            ['id_departamento' => 23, 'id_pais' => 2,   'nombre' => 'CALIFORNIA'],
            ['id_departamento' => 24, 'id_pais' => 2,   'nombre' => 'FLORIDA'],
            ['id_departamento' => 25, 'id_pais' => 28,  'nombre' => 'QUITO'],
            ['id_departamento' => 26, 'id_pais' => 3,   'nombre' => 'LA CEIBA'],
            ['id_departamento' => 27, 'id_pais' => 3,   'nombre' => 'TEGUCIGALPA'],
            ['id_departamento' => 28, 'id_pais' => 3,   'nombre' => 'SAN PEDRO SULA'],
            ['id_departamento' => 29, 'id_pais' => 1,   'nombre' => 'EL SALVADOR NO DEFINIDO'],
            ['id_departamento' => 30, 'id_pais' => 27,  'nombre' => 'NICARAGUA'],
            ['id_departamento' => 31, 'id_pais' => 7,   'nombre' => 'COSTA RICA'],
            ['id_departamento' => 32, 'id_pais' => 29,  'nombre' => 'BOGOTA'],
            ['id_departamento' => 33, 'id_pais' => 2,   'nombre' => 'ESTADOS UNIDOS'],
            ['id_departamento' => 34, 'id_pais' => 30,  'nombre' => 'LUXEMBURGO'],
            ['id_departamento' => 35, 'id_pais' => 4,   'nombre' => 'PUEBLA'],
            ['id_departamento' => 36, 'id_pais' => 39,  'nombre' => 'REPUBLICA DOMINICANA'],
            ['id_departamento' => 37, 'id_pais' => 8,   'nombre' => 'PANAMÁ'],
            ['id_departamento' => 38, 'id_pais' => 40,  'nombre' => 'BELICE'],
            ['id_departamento' => 39, 'id_pais' => 41,  'nombre' => 'BERLIN'],
            ['id_departamento' => 40, 'id_pais' => 42,  'nombre' => 'ITALIA'],
            ['id_departamento' => 41, 'id_pais' => 5,   'nombre' => 'PUERTO RICO'],
            ['id_departamento' => 42, 'id_pais' => 19,  'nombre' => 'JAPON'],
            ['id_departamento' => 43, 'id_pais' => 26,  'nombre' => 'VENEZUELA'],
            ['id_departamento' => 44, 'id_pais' => 31,  'nombre' => 'ARGENTINA'],
            ['id_departamento' => 45, 'id_pais' => 32,  'nombre' => 'PORTUGAL'],
            ['id_departamento' => 46, 'id_pais' => 35,  'nombre' => 'HOLANDA'],
            ['id_departamento' => 47, 'id_pais' => 36,  'nombre' => 'SERBIA'],
            ['id_departamento' => 48, 'id_pais' => 37,  'nombre' => 'SUIZA'],
            ['id_departamento' => 49, 'id_pais' => 38,  'nombre' => 'ESPAÑA'],
            ['id_departamento' => 51, 'id_pais' => 44,  'nombre' => 'LONDRES'],
            ['id_departamento' => 52, 'id_pais' => 29,  'nombre' => 'BARRANQUILLA'],
            ['id_departamento' => 53, 'id_pais' => 46,  'nombre' => 'BOLIVIA'],
            ['id_departamento' => 54, 'id_pais' => 47,  'nombre' => 'LIMA'],
            ['id_departamento' => 55, 'id_pais' => 29,  'nombre' => 'MEDELLIN'],
            ['id_departamento' => 56, 'id_pais' => 4,   'nombre' => 'NUEVO LEÓN'],
            ['id_departamento' => 57, 'id_pais' => 49,  'nombre' => 'SINGAPORE'],
            ['id_departamento' => 58, 'id_pais' => 17,  'nombre' => 'GENÉRICO'],
            ['id_departamento' => 59, 'id_pais' => 52,  'nombre' => 'IRLANDA'],
            ['id_departamento' => 60, 'id_pais' => 2,   'nombre' => 'ARKANSAS'],
            ['id_departamento' => 61, 'id_pais' => 7,   'nombre' => 'ALAJUELA'],
            ['id_departamento' => 62, 'id_pais' => 41,  'nombre' => 'BREMEN'],
            ['id_departamento' => 63, 'id_pais' => 48,  'nombre' => 'MONTEVIDEO'],
            ['id_departamento' => 64, 'id_pais' => 7,   'nombre' => 'SAN JOSE'],
            ['id_departamento' => 65, 'id_pais' => 54,  'nombre' => 'GENERICO'],
            ['id_departamento' => 66, 'id_pais' => 31,  'nombre' => 'BUENOS AIRES'],
            ['id_departamento' => 67, 'id_pais' => 970, 'nombre' => 'MOSCÚ'],
        ];

        $rows = array_map(fn($d) => [
            'id_departamento' => $d['id_departamento'],
            'id_pais'         => $d['id_pais'],
            'nombre'          => $d['nombre'],
            'activo'          => true,
            'usuario_crea'    => $usuarioCrea,
            'usuario_mod'     => null,
            'usuario_elim'    => null,
            'created_at'      => $now,
            'updated_at'      => $now,
            'deleted_at'      => null,
        ], $departamentos);

        DB::table('tbl_departamento')->insertOrIgnore($rows);
    }
}