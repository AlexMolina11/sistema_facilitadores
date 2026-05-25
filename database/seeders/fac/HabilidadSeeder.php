<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class HabilidadSeeder extends Seeder
{
    public function run(): void
    {
        $habilidades = [
            // Tipo 2 - Habilidades blandas
            ['id_tipo_habilidad' => 2, 'nombre' => 'Trabajo en equipo'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Pensamiento crítico'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Resolución de problemas'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Liderazgo'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Gestión del tiempo'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Empatía'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Creatividad'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Adaptabilidad'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Toma de decisiones'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Negociación'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Gestión del estrés'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Orientación a resultados'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Capacidad de aprendizaje'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Escucha activa'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Responsabilidad'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Manejo de conflictos'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Proactividad'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Pensamiento analítico'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Iniciativa'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Organización'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Capacidad de síntesis'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Atención al detalle'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Orientación al cliente'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Motivación'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Autoconfianza'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Ética profesional'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Flexibilidad'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Autogestión'],
            ['id_tipo_habilidad' => 2, 'nombre' => 'Aprendizaje continuo'],
            // Tipo 3 - Habilidades técnicas específicas
            ['id_tipo_habilidad' => 3, 'nombre' => 'Programación en Python'],
            ['id_tipo_habilidad' => 3, 'nombre' => 'Desarrollo web con React'],
            ['id_tipo_habilidad' => 3, 'nombre' => 'Análisis de datos con Power BI'],
            ['id_tipo_habilidad' => 3, 'nombre' => 'Administración de bases de datos MySQL'],
            ['id_tipo_habilidad' => 3, 'nombre' => 'Diseño gráfico con Photoshop'],
            ['id_tipo_habilidad' => 3, 'nombre' => 'Gestión de proyectos con Scrum'],
            ['id_tipo_habilidad' => 3, 'nombre' => 'Automatización con RPA'],
            ['id_tipo_habilidad' => 3, 'nombre' => 'Implementación de redes Cisco'],
            ['id_tipo_habilidad' => 3, 'nombre' => 'Edición de video con Premiere'],
            ['id_tipo_habilidad' => 3, 'nombre' => 'Desarrollo de aplicaciones móviles'],
            ['id_tipo_habilidad' => 3, 'nombre' => 'Uso de Google Analytics'],
            ['id_tipo_habilidad' => 3, 'nombre' => 'Soporte técnico de hardware'],
            ['id_tipo_habilidad' => 3, 'nombre' => 'Configuración de servidores Linux'],
            ['id_tipo_habilidad' => 3, 'nombre' => 'Diseño de interfaces UI/UX'],
            ['id_tipo_habilidad' => 3, 'nombre' => 'Pruebas de software (QA)'],
            ['id_tipo_habilidad' => 3, 'nombre' => 'Redacción de contenido SEO'],
            ['id_tipo_habilidad' => 3, 'nombre' => 'Manejo de AutoCAD'],
            ['id_tipo_habilidad' => 3, 'nombre' => 'Administración de SAP'],
            ['id_tipo_habilidad' => 3, 'nombre' => 'Implementación de campañas en Meta Ads'],
            // Tipo 1 - Áreas de especialización
            ['id_tipo_habilidad' => 1, 'nombre' => 'Desarrollo de Aplicaciones Web'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Desarrollo de Aplicaciones Móviles'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Inteligencia Artificial'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Análisis de Datos'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Gestión de Proyectos'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Ingeniería de Software'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Arquitectura de Soluciones'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Infraestructura Cloud'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Administración de Bases de Datos'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Soporte Técnico y Help Desk'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Testing y QA'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Diseño UX/UI'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Gestión de Marketing Digital'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'SEO y SEM'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Estrategia de Marca'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Finanzas Corporativas'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Auditoría Financiera'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Recursos Humanos'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Desarrollo Organizacional'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Relaciones Laborales'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Planificación Fiscal'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Gestión de Riesgo Financiero'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Gestión de Inversiones'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Cadena de Suministro y Logística'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Gestión de Compras'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Consultoría en Transformación Digital'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Educación y Formación en Línea'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Comercio Electrónico'],
            ['id_tipo_habilidad' => 1, 'nombre' => 'Sostenibilidad y Responsabilidad Social'],
        ];

        $now = now();

        DB::statement('SET FOREIGN_KEY_CHECKS=0;');

        foreach ($habilidades as $index => $habilidad) {
            DB::table('tbl_habilidad')->insert([
                'id_habilidad'     => $index + 1,
                'id_tipo_habilidad'=> $habilidad['id_tipo_habilidad'],
                'nombre'           => $habilidad['nombre'],
                'activo'           => true,
                'usuario_crea'     => null,
                'usuario_mod'      => null,
                'usuario_elim'     => null,
                'created_at'       => $now,
                'updated_at'       => $now,
            ]);
        }

        DB::statement('SET FOREIGN_KEY_CHECKS=1;');
    }
}
