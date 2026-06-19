<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaEspecializacionSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            'Gestión de proyectos',
            'Transformación digital',
            'Educación y formación',
            'Desarrollo organizacional',
            'Finanzas y administración',
            'Marketing digital',
            'Tecnología e innovación',
        ];

        foreach ($areas as $area) {
            DB::table('tbl_area_especializacion')->updateOrInsert(
                ['nombre' => $area],
                ['activo' => true, 'created_at' => now(), 'updated_at' => now()]
            );
        }
    }
}
