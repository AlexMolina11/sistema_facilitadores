<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaEspecializacionSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            'Desarrollo de Aplicaciones Web',
            'Desarrollo de Aplicaciones Móviles',
            'Inteligencia Artificial',
            'Análisis de Datos',
            'Gestión de Proyectos',
            'Ingeniería de Software',
            'Arquitectura de Soluciones',
            'Infraestructura Cloud',
            'Administración de Bases de Datos',
            'Soporte Técnico y Help Desk',
            'Testing y QA',
            'Diseño UX/UI',
            'Gestión de Marketing Digital',
            'SEO y SEM',
            'Estrategia de Marca',
            'Finanzas Corporativas',
            'Auditoría Financiera',
            'Recursos Humanos',
            'Desarrollo Organizacional',
            'Relaciones Laborales',
            'Planificación Fiscal',
            'Gestión de Riesgo Financiero',
            'Gestión de Inversiones',
            'Cadena de Suministro y Logística',
            'Gestión de Compras',
            'Consultoría en Transformación Digital',
            'Educación y Formación en Línea',
            'Comercio Electrónico',
            'Sostenibilidad y Responsabilidad Social',
        ];

        foreach ($areas as $area) {
            DB::table('tbl_area_especializacion')->updateOrInsert(
                ['nombre' => $area],
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
