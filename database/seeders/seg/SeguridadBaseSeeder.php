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
        /*
        |--------------------------------------------------------------------------
        | Permisos del sistema
        |--------------------------------------------------------------------------
        */

        $permisos = [

            /*
            |--------------------------------------------------------------------------
            | FAC - Dashboard
            |--------------------------------------------------------------------------
            */

            [
                'codigo' => 'fac.dashboard.ver',
                'nombre' => 'Ver dashboard',
                'modulo' => 'FAC',
            ],

            /*
            |--------------------------------------------------------------------------
            | FAC - Consultores
            |--------------------------------------------------------------------------
            */

            [
                'codigo' => 'fac.consultores.ver',
                'nombre' => 'Ver consultores',
                'modulo' => 'FAC',
            ],
            [
                'codigo' => 'fac.consultores.crear',
                'nombre' => 'Crear consultores',
                'modulo' => 'FAC',
            ],
            [
                'codigo' => 'fac.consultores.editar',
                'nombre' => 'Editar consultores',
                'modulo' => 'FAC',
            ],
            [
                'codigo' => 'fac.consultores.eliminar',
                'nombre' => 'Eliminar consultores',
                'modulo' => 'FAC',
            ],

            /*
            |--------------------------------------------------------------------------
            | FAC - Búsqueda avanzada
            |--------------------------------------------------------------------------
            */

            [
                'codigo' => 'fac.busqueda.ver',
                'nombre' => 'Ver búsqueda avanzada',
                'modulo' => 'FAC',
            ],

            /*
            |--------------------------------------------------------------------------
            | FAC - CV
            |--------------------------------------------------------------------------
            */

            [
                'codigo' => 'fac.cv.generar',
                'nombre' => 'Generar CV de consultores',
                'modulo' => 'FAC',
            ],

            /*
            |--------------------------------------------------------------------------
            | FAC - Reportería
            |--------------------------------------------------------------------------
            */

            [
                'codigo' => 'fac.reportes.ver',
                'nombre' => 'Ver reportería',
                'modulo' => 'FAC',
            ],
            [
                'codigo' => 'fac.reportes.exportar',
                'nombre' => 'Exportar reportes',
                'modulo' => 'FAC',
            ],

            /*
            |--------------------------------------------------------------------------
            | FAC - Catálogos
            |--------------------------------------------------------------------------
            */

            [
                'codigo' => 'fac.catalogos.gestionar',
                'nombre' => 'Gestionar catálogos',
                'modulo' => 'FAC',
            ],

            /*
            |--------------------------------------------------------------------------
            | SEG - Seguridad
            |--------------------------------------------------------------------------
            */

            [
                'codigo' => 'seg.usuarios.gestionar',
                'nombre' => 'Gestionar usuarios',
                'modulo' => 'SEG',
            ],
            [
                'codigo' => 'seg.roles.gestionar',
                'nombre' => 'Gestionar roles',
                'modulo' => 'SEG',
            ],
            [
                'codigo' => 'seg.permisos.gestionar',
                'nombre' => 'Gestionar permisos',
                'modulo' => 'SEG',
            ],
            [
                'codigo' => 'seg.bitacora.ver',
                'nombre' => 'Ver bitácora de accesos',
                'modulo' => 'SEG',
            ],
            [
                'codigo' => 'seg.bitacora-saf.ver',
                'nombre' => 'Ver bitácora de sincronizaciones SAF',
                'modulo' => 'SEG',
            ],
            [
                'codigo' => 'seg.invitaciones.gestionar',
                'nombre' => 'Gestionar invitaciones',
                'modulo' => 'SEG',
            ],
            [
                'codigo' => 'seg.bitacora_terminos.ver',
                'nombre' => 'Ver bitácora de aceptación de términos',
                'modulo' => 'SEG',
            ],
        ];

        foreach ($permisos as $permiso) {
            Permiso::updateOrCreate(
                ['codigo' => $permiso['codigo']],
                $permiso + ['activo' => true]
            );
        }


        /*
        |--------------------------------------------------------------------------
        | Desactivar permiso antiguo demasiado amplio
        |--------------------------------------------------------------------------
        |
        | Este permiso anteriormente permitía crear, editar y eliminar
        | consultores bajo una única autorización.
        |
        | A partir de Fase 13 se utilizan permisos granulares.
        |
        */

        Permiso::where(
            'codigo',
            'fac.consultores.gestionar'
        )->update([
            'activo' => false,
        ]);


        /*
        |--------------------------------------------------------------------------
        | Roles
        |--------------------------------------------------------------------------
        */

        $admin = Rol::updateOrCreate(
            ['nombre' => 'Administrador'],
            [
                'descripcion' => 'Acceso total al sistema',
                'activo' => true,
            ]
        );

        $consultor = Rol::updateOrCreate(
            ['nombre' => 'Consultor'],
            [
                'descripcion' => 'Usuario consultor asociado a su propio perfil',
                'activo' => true,
            ]
        );

        $gestor = Rol::updateOrCreate(
            ['nombre' => 'Gestor'],
            [
                'descripcion' => 'Gestión, consulta y revisión de perfiles de consultores',
                'activo' => true,
            ]
        );


        /*
        |--------------------------------------------------------------------------
        | Administrador
        |--------------------------------------------------------------------------
        |
        | Obtiene todos los permisos activos del sistema.
        |
        */

        $admin->permisos()->sync(
            Permiso::where('activo', true)
                ->pluck('id_permiso')
                ->toArray()
        );


        /*
        |--------------------------------------------------------------------------
        | Consultor
        |--------------------------------------------------------------------------
        |
        | No recibe permisos administrativos.
        |
        | El acceso a su expediente se controla mediante:
        |
        | consultor.owner
        |
        */

        $consultor->permisos()->sync([]);


        /*
        |--------------------------------------------------------------------------
        | Gestor
        |--------------------------------------------------------------------------
        |
        | Puede:
        |
        | - Ver dashboard.
        | - Consultar consultores.
        | - Editar consultores.
        | - Realizar búsqueda avanzada.
        | - Generar CV.
        | - Gestionar invitaciones.
        | - Consultar y exportar reportería.
        |
        | No puede:
        |
        | - Crear consultores.
        | - Eliminar consultores.
        | - Gestionar catálogos.
        | - Gestionar seguridad.
        |
        */

        $gestor->permisos()->sync(
            Permiso::whereIn('codigo', [

                'fac.dashboard.ver',

                'fac.consultores.ver',
                'fac.consultores.editar',

                'fac.busqueda.ver',

                'fac.cv.generar',

                'seg.invitaciones.gestionar',

                'fac.reportes.ver',
                'fac.reportes.exportar',

            ])
                ->where('activo', true)
                ->pluck('id_permiso')
                ->toArray()
        );


        /*
        |--------------------------------------------------------------------------
        | Usuario administrador inicial
        |--------------------------------------------------------------------------
        */

        $usuarioAdmin = Usuario::updateOrCreate(
            [
                'email' => 'admin@fepade.test',
            ],
            [
                'nombres' => 'Administrador',
                'apellidos' => 'FEPADE',
                'password' => Hash::make('Admin12345*'),
                'activo' => true,
            ]
        );

        $usuarioAdmin->roles()->syncWithoutDetaching([
            $admin->id_rol,
        ]);
    }
}