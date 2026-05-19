<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class IdiomaSeeder extends Seeder
{
    public function run(): void
    {
        $idiomas = [
            ['id_idioma' => 1,  'nombre' => 'Español'],
            ['id_idioma' => 2,  'nombre' => 'Inglés'],
            ['id_idioma' => 3,  'nombre' => 'Francés'],
            ['id_idioma' => 4,  'nombre' => 'Árabe'],
            ['id_idioma' => 5,  'nombre' => 'Chino Mandarín'],
            ['id_idioma' => 6,  'nombre' => 'Ruso'],
            ['id_idioma' => 7,  'nombre' => 'Portugués'],
            ['id_idioma' => 8,  'nombre' => 'Alemán'],
            ['id_idioma' => 9,  'nombre' => 'Japonés'],
            ['id_idioma' => 10, 'nombre' => 'Hindi'],
            ['id_idioma' => 11, 'nombre' => 'Italiano'],
            ['id_idioma' => 12, 'nombre' => 'Coreano'],
            ['id_idioma' => 13, 'nombre' => 'Turco'],
            ['id_idioma' => 14, 'nombre' => 'Neerlandés'],
            ['id_idioma' => 15, 'nombre' => 'Polaco'],
            ['id_idioma' => 16, 'nombre' => 'Sueco'],
            ['id_idioma' => 17, 'nombre' => 'Noruego'],
            ['id_idioma' => 18, 'nombre' => 'Danés'],
            ['id_idioma' => 19, 'nombre' => 'Finlandés'],
            ['id_idioma' => 20, 'nombre' => 'Griego'],
            ['id_idioma' => 21, 'nombre' => 'Checo'],
            ['id_idioma' => 22, 'nombre' => 'Rumano'],
            ['id_idioma' => 23, 'nombre' => 'Húngaro'],
            ['id_idioma' => 24, 'nombre' => 'Hebreo'],
            ['id_idioma' => 25, 'nombre' => 'Indonesio'],
            ['id_idioma' => 26, 'nombre' => 'Malayo'],
            ['id_idioma' => 27, 'nombre' => 'Tailandés'],
            ['id_idioma' => 28, 'nombre' => 'Vietnamita'],
            ['id_idioma' => 29, 'nombre' => 'Bengalí'],
            ['id_idioma' => 30, 'nombre' => 'Swahili'],
            ['id_idioma' => 31, 'nombre' => 'Ucraniano'],
            ['id_idioma' => 32, 'nombre' => 'Catalán'],
            ['id_idioma' => 33, 'nombre' => 'Croata'],
            ['id_idioma' => 34, 'nombre' => 'Eslovaco'],
            ['id_idioma' => 35, 'nombre' => 'Búlgaro'],
            ['id_idioma' => 36, 'nombre' => 'Esloveno'],
            ['id_idioma' => 37, 'nombre' => 'Lituano'],
            ['id_idioma' => 38, 'nombre' => 'Letón'],
            ['id_idioma' => 39, 'nombre' => 'Estonio'],
            ['id_idioma' => 40, 'nombre' => 'Albanés'],
        ];

        foreach ($idiomas as $idioma) {
            DB::table('tbl_idioma')->insertOrIgnore([
                'id_idioma'    => $idioma['id_idioma'],
                'nombre'       => $idioma['nombre'],
                'activo'       => true,
                'usuario_crea' => null,
                'usuario_mod'  => null,
                'usuario_elim' => null,
                'created_at'   => now(),
                'updated_at'   => now(),
            ]);
        }
    }
}