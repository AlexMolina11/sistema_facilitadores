<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HabilidadTecnicaSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            'Gestión de proyectos' => ['Planificación de proyectos', 'Gestión de riesgos', 'Metodologías ágiles'],
            'Transformación digital' => ['Mapeo de procesos', 'Automatización de procesos', 'Gestión del cambio'],
            'Educación y formación' => ['Diseño instruccional', 'Facilitación de talleres', 'Evaluación de aprendizajes'],
            'Tecnología e innovación' => ['Análisis de datos', 'Desarrollo web', 'Administración de bases de datos'],
        ];

        foreach ($items as $areaNombre => $habilidades) {
            $area = DB::table('tbl_area_especializacion')->where('nombre', $areaNombre)->first();
            if (!$area) {
                continue;
            }

            foreach ($habilidades as $habilidad) {
                DB::table('tbl_habilidad_tecnica')->updateOrInsert(
                    ['id_area_especializacion' => $area->id_area_especializacion, 'nombre' => $habilidad],
                    ['activo' => true, 'created_at' => now(), 'updated_at' => now()]
                );
            }
        }
    }
}
