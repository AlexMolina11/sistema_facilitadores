<?php
 
namespace Database\Seeders\fac;
 
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\DB;
 
class TipoDocumentoSeeder extends Seeder
{
    public function run(): void
    {
        $tipos = [
            ['id_tipo_documento' => 1, 'nombre' => 'NIT'],
            ['id_tipo_documento' => 2, 'nombre' => 'DUI'],
            ['id_tipo_documento' => 3, 'nombre' => 'NRC'],
            ['id_tipo_documento' => 4, 'nombre' => 'PASAPORTE'],
            ['id_tipo_documento' => 5, 'nombre' => 'CARNET DE RESIDENTE'],
        ];
 
        foreach ($tipos as $tipo) {
            DB::table('tbl_tipo_documento')->insert([
                'id_tipo_documento' => $tipo['id_tipo_documento'],
                'nombre'            => $tipo['nombre'],
                'activo'            => true,
                'usuario_crea'      => null,
                'usuario_mod'       => null,
                'usuario_elim'      => null,
                'created_at'        => now(),
                'updated_at'        => now(),
            ]);
        }
    }
}
 