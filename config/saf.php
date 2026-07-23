<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Integración SAF
    |--------------------------------------------------------------------------
    |
    | Laravel no se conecta directamente a SQL Server. Esta configuración
    | identifica si el ambiente debe mostrar y procesar información asociada
    | con las sincronizaciones realizadas por la rutina externa de SAF.
    |
    */

    'habilitada' => env('SAF_INTEGRACION_HABILITADA', false),

    'origen' => [
        'nombre' => env('SAF_ORIGEN_NOMBRE', 'SAF'),
        'tipo' => env('SAF_ORIGEN_TIPO', 'SQLServer'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Estados permitidos
    |--------------------------------------------------------------------------
    */

    'estados_sincronizacion' => [
        'Iniciado',
        'Exitoso',
        'Parcial',
        'Fallido',
    ],

    /*
    |--------------------------------------------------------------------------
    | Tipos de registro para errores
    |--------------------------------------------------------------------------
    */

    'tipos_registro_error' => [
        'Consultor',
        'Capacitacion',
        'Lote',
        'Conexion',
    ],

    /*
    |--------------------------------------------------------------------------
    | Operaciones auditables
    |--------------------------------------------------------------------------
    */

    'operaciones' => [
        'Insertar',
        'Actualizar',
        'Validar',
        'Finalizar',
    ],

];