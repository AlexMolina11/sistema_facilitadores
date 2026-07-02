<?php

namespace Database\Seeders\fac;

use App\Modules\Fac\Models\CvPlantilla;
use Illuminate\Database\Seeder;

class CvPlantillaSeeder extends Seeder
{
    public function run(): void
    {
        $plantillas = [
            [
                'codigo' => 'fepade',
                'nombre' => 'Formato CV FEPADE',
                'descripcion' => 'Hoja de vida institucional FEPADE.',
                'orden' => 1,
            ],
            [
                'codigo' => 'mineducyt_birf',
                'nombre' => 'Formato CV MINEDUCYT / BIRF',
                'descripcion' => 'Currículum para procesos MINEDUCYT / BIRF.',
                'orden' => 2,
            ],
            [
                'codigo' => 'resumen_personal',
                'nombre' => 'Resumen del CV del personal propuesto',
                'descripcion' => 'Resumen ejecutivo del personal propuesto.',
                'orden' => 3,
            ],
            [
                'codigo' => 'profesional',
                'nombre' => 'CV profesional completo',
                'descripcion' => 'Plantilla visual completa para mostrar toda la información registrada.',
                'orden' => 4,
            ],
        ];

        foreach ($plantillas as $plantilla) {
            CvPlantilla::updateOrCreate(
                ['codigo' => $plantilla['codigo']],
                [
                    'nombre' => $plantilla['nombre'],
                    'descripcion' => $plantilla['descripcion'],
                    'vista_blade' => 'fac.cv.pdf.' . $plantilla['codigo'],
                    'tamanio_papel' => 'letter',
                    'orientacion' => 'portrait',
                    'orden' => $plantilla['orden'],
                    'activa' => true,
                    'activo' => true,
                ]
            );
        }
    }
}