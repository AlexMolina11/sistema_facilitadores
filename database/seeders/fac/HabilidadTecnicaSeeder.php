<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HabilidadTecnicaSeeder extends Seeder
{
    public function run(): void
    {
        $items = [
            'Desarrollo de Aplicaciones Web' => [
                'Desarrollo web con React',
            ],
            'Desarrollo de Aplicaciones Móviles' => [
                'Desarrollo de aplicaciones móviles',
            ],
            'Inteligencia Artificial' => [
                'Programación en Python',
            ],
            'Análisis de Datos' => [
                'Análisis de datos con Power BI',
            ],
            'Gestión de Proyectos' => [
                'Gestión de proyectos con Scrum',
            ],
            'Infraestructura Cloud' => [
                'Configuración de servidores Linux',
            ],
            'Administración de Bases de Datos' => [
                'Administración de bases de datos MySQL',
            ],
            'Soporte Técnico y Help Desk' => [
                'Implementación de redes Cisco',
                'Soporte técnico de hardware',
            ],
            'Testing y QA' => [
                'Pruebas de software (QA)',
            ],
            'Diseño UX/UI' => [
                'Diseño gráfico con Photoshop',
                'Diseño de interfaces UI/UX',
            ],
            'Gestión de Marketing Digital' => [
                'Edición de video con Premiere',
                'Implementación de campañas en Meta Ads',
            ],
            'SEO y SEM' => [
                'Uso de Google Analytics',
                'Redacción de contenido SEO',
            ],
            'Consultoría en Transformación Digital' => [
                'Automatización con RPA',
            ],
            'Arquitectura de Soluciones' => [
                'Manejo de AutoCAD',
            ],
            'Finanzas Corporativas' => [
                'Administración de SAP',
            ],
        ];

        foreach ($items as $areaNombre => $habilidades) {
            $area = DB::table('tbl_area_especializacion')
                ->where('nombre', $areaNombre)
                ->first();

            if (!$area) {
                continue;
            }

            foreach ($habilidades as $habilidad) {
                DB::table('tbl_habilidad_tecnica')->updateOrInsert(
                    [
                        'id_area_especializacion' => $area->id_area_especializacion,
                        'nombre' => $habilidad,
                    ],
                    [
                        'descripcion' => null,
                        'activo' => true,
                        'created_at' => now(),
                        'updated_at' => now(),
                    ]
                );
            }
        }
    }
}
