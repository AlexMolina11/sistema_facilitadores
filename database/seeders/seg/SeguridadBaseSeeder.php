<?php

namespace Database\Seeders\seg;

use App\Modules\Seg\Models\Permiso;
use App\Modules\Seg\Models\Rol;
use App\Modules\Seg\Models\Usuario;
use Illuminate\Database\Seeder;
use Illuminate\Support\Facades\Hash;

class SeguridadBaseSeeder extends Seeder
{
    public function run(): void
    {
        $permisos = [
            ['codigo' => 'fac.dashboard.ver', 'nombre' => 'Ver dashboard', 'modulo' => 'FAC'],
            ['codigo' => 'fac.consultores.ver', 'nombre' => 'Ver consultores', 'modulo' => 'FAC'],
            ['codigo' => 'fac.consultores.gestionar', 'nombre' => 'Gestionar consultores', 'modulo' => 'FAC'],
            ['codigo' => 'fac.catalogos.gestionar', 'nombre' => 'Gestionar catálogos', 'modulo' => 'FAC'],
            ['codigo' => 'seg.usuarios.gestionar', 'nombre' => 'Gestionar usuarios', 'modulo' => 'SEG'],
            ['codigo' => 'seg.roles.gestionar', 'nombre' => 'Gestionar roles', 'modulo' => 'SEG'],
            ['codigo' => 'seg.permisos.gestionar', 'nombre' => 'Gestionar permisos', 'modulo' => 'SEG'],
            ['codigo' => 'seg.bitacora.ver', 'nombre' => 'Ver bitácora de accesos', 'modulo' => 'SEG'],
            ['codigo' => 'seg.invitaciones.gestionar', 'nombre' => 'Gestionar invitaciones', 'modulo' => 'SEG'],
        ];

        foreach ($permisos as $permiso) {
            Permiso::updateOrCreate(
                ['codigo' => $permiso['codigo']],
                $permiso + ['activo' => true]
            );
        }

        $admin = Rol::updateOrCreate(
            ['nombre' => 'Administrador'],
            ['descripcion' => 'Acceso total al sistema', 'activo' => true]
        );

        $consultor = Rol::updateOrCreate(
            ['nombre' => 'Consultor'],
            ['descripcion' => 'Usuario consultor asociado a su propio perfil', 'activo' => true]
        );

        $gestor = Rol::updateOrCreate(
            ['nombre' => 'Gestor'],
            ['descripcion' => 'Consulta y revisión de perfiles', 'activo' => true]
        );

        $admin->permisos()->sync(Permiso::pluck('id_permiso')->toArray());

        $consultor->permisos()->sync(
            Permiso::whereIn('codigo', [
                'fac.dashboard.ver',
                'fac.consultores.ver',
            ])->pluck('id_permiso')->toArray()
        );

        $gestor->permisos()->sync(
            Permiso::whereIn('codigo', [
                'fac.dashboard.ver',
                'fac.consultores.ver',
            ])->pluck('id_permiso')->toArray()
        );

        $usuarioAdmin = Usuario::updateOrCreate(
            ['email' => 'admin@fepade.test'],
            [
                'nombres' => 'Administrador',
                'apellidos' => 'FEPADE',
                'password' => Hash::make('Admin12345*'),
                'activo' => true,
            ]
        );

        $usuarioAdmin->roles()->syncWithoutDetaching([$admin->id_rol]);
    }
}
