<?php

namespace Database\Seeders\fac;

use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;

class AreaEspecializacionSeeder extends Seeder
{
    public function run(): void
    {
        $areas = [
            'Administración, finanzas y gestión empresarial',
            'Agropecuario, pesca y agroindustria',
            'Alimentos, bebidas y gastronomía',
            'Artes, cultura, comunicación y economía creativa',
            'Artesanías, oficios y economía popular',
            'Automotriz, movilidad y transporte técnico',
            'Belleza, estética y servicios personales',
            'Comercio, ventas y servicios al cliente',
            'Construcción, infraestructura y vivienda',
            'Deporte, recreación y vida activa',
            'Educación, formación y desarrollo humano',
            'Emprendimiento, innovación y desarrollo productivo',
            'Energía, agua y saneamiento',
            'Gobierno, legal y servicios públicos',
            'Industria manufacturera y procesos productivos',
            'Logística, transporte y cadena de suministro',
            'Medio ambiente, sostenibilidad y economía circular',
            'Minería, hidrocarburos y recursos naturales',
            'Salud, cuidado y bienestar comunitario',
            'Seguridad, prevención y gestión de riesgos',
            'Servicios técnicos, mantenimiento y reparación',
            'Tecnología, datos y telecomunicaciones',
            'Textil, confección, cuero y moda',
            'Turismo, hotelería y eventos',
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
