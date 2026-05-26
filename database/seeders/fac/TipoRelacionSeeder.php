<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class TipoRelacionSeeder extends Seeder
{
    public function run(): void
    {
        $items = ['Amigo/a', 'Hermano/a', 'Madre', 'Padre', 'Otro'];

        foreach ($items as $nombre) {
            DB::table('tbl_tipo_relacion')->updateOrInsert(
                ['nombre' => $nombre],
                [
                    'activo' => true,
                    'updated_at' => now(),
                    'created_at' => now(),
                ]
            );
        }
    }
}
