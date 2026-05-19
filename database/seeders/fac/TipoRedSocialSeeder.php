<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoRedSocialSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['id_tipo_red_social' => 1, 'nombre' => 'LinkedIn',              'icono' => 'fab fa-linkedin'],
            ['id_tipo_red_social' => 2, 'nombre' => 'GitHub',                'icono' => 'fab fa-github'],
            ['id_tipo_red_social' => 3, 'nombre' => 'Twitter / X',           'icono' => 'fab fa-x-twitter'],
            ['id_tipo_red_social' => 4, 'nombre' => 'Facebook',              'icono' => 'fab fa-facebook'],
            ['id_tipo_red_social' => 5, 'nombre' => 'Instagram',             'icono' => 'fab fa-instagram'],
            ['id_tipo_red_social' => 6, 'nombre' => 'YouTube',               'icono' => 'fab fa-youtube'],
            ['id_tipo_red_social' => 7, 'nombre' => 'Sitio Web Personal',    'icono' => 'fas fa-globe'],
            ['id_tipo_red_social' => 8, 'nombre' => 'Behance',               'icono' => 'fab fa-behance'],
            ['id_tipo_red_social' => 9, 'nombre' => 'Dribbble',              'icono' => 'fab fa-dribbble'],
        ];

        foreach ($tipos as $tipo) {
            DB::table('tbl_tipo_red_social')->insert([
                'id_tipo_red_social' => $tipo['id_tipo_red_social'],
                'nombre'             => $tipo['nombre'],
                'icono'              => $tipo['icono'],
                'activo'             => true,
                'usuario_crea'       => null,
                'usuario_mod'        => null,
                'usuario_elim'       => null,
                'created_at'         => now(),
                'updated_at'         => now(),
            ]);
        }
    }
}