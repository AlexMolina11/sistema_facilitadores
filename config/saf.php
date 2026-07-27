<?php

return [

    /*
    |--------------------------------------------------------------------------
    | Integración SAF habilitada
    |--------------------------------------------------------------------------
    |
    | Permite activar o desactivar completamente la integración.
    |
    | Cuando sea false:
    | - No se podrá ejecutar la sincronización.
    | - Los comandos deberán detenerse de forma controlada.
    | - Las opciones de interfaz podrán ocultarse o deshabilitarse.
    |
    */
    'enabled' => env('SAF_ENABLED', false),

    /*
    |--------------------------------------------------------------------------
    | Modalidad de integración
    |--------------------------------------------------------------------------
    |
    | Define cómo recibirá la aplicación la información proveniente de SAF.
    |
    | Valores previstos:
    |
    | database:
    | SAF escribirá directamente en las tablas autorizadas de la base
    | de datos del sistema de Facilitadores.
    |
    | api:
    | La información será consultada o recibida mediante una API.
    |
    | file:
    | La información será importada desde archivos controlados.
    |
    | Inicialmente utilizaremos database.
    |
    */
    'mode' => env('SAF_MODE', 'database'),

    /*
    |--------------------------------------------------------------------------
    | Nombre del origen
    |--------------------------------------------------------------------------
    |
    | Valor utilizado para identificar los registros que provienen de SAF.
    |
    */
    'source' => env('SAF_SOURCE', 'SAF'),

    /*
    |--------------------------------------------------------------------------
    | Entidad FEPADE
    |--------------------------------------------------------------------------
    |
    | Identificador de la entidad que SAF utilizará para insertar o
    | actualizar instructores pertenecientes a FEPADE.
    |
    | El valor definitivo deberá ser proporcionado o confirmado por SAF.
    |
    */
    'entity_id' => env('SAF_ENTITY_ID'),

    /*
    |--------------------------------------------------------------------------
    | Procesamiento por lotes
    |--------------------------------------------------------------------------
    |
    | chunk_size:
    | Cantidad de registros procesados en cada bloque.
    |
    | max_records:
    | Límite máximo permitido por ejecución.
    | Cero significa que no existe límite adicional.
    |
    */
    'processing' => [
        'chunk_size' => (int) env('SAF_CHUNK_SIZE', 100),

        'max_records' => (int) env('SAF_MAX_RECORDS', 0),

        /*
         | Si es true, un error individual no detendrá toda la sincronización.
         */
        'continue_on_error' => env(
            'SAF_CONTINUE_ON_ERROR',
            true
        ),

        /*
         | Número máximo de errores individuales permitidos antes de
         | detener completamente una ejecución.
         |
         | Cero significa que no habrá un límite por cantidad de errores.
         */
        'max_errors' => (int) env('SAF_MAX_ERRORS', 0),
    ],

    /*
    |--------------------------------------------------------------------------
    | Sincronización de consultores
    |--------------------------------------------------------------------------
    */
    'consultants' => [

        /*
         | Permite crear consultores que todavía no existan localmente.
         */
        'create' => env('SAF_CONSULTANTS_CREATE', true),

        /*
         | Permite actualizar consultores existentes.
         */
        'update' => env('SAF_CONSULTANTS_UPDATE', true),

        /*
         | Define si los consultores que ya no estén vigentes en SAF
         | podrán ser desactivados localmente.
         |
         | Se mantiene false inicialmente hasta acordar formalmente
         | la regla de negocio con SAF.
         */
        'deactivate_missing' => env(
            'SAF_CONSULTANTS_DEACTIVATE_MISSING',
            false
        ),

        /*
         | Campo externo principal utilizado para relacionar un instructor
         | SAF con un consultor local.
         */
        'external_key' => env(
            'SAF_CONSULTANTS_EXTERNAL_KEY',
            'id_instructor'
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Sincronización de capacitaciones
    |--------------------------------------------------------------------------
    */
    'trainings' => [

        /*
         | Permite crear capacitaciones nuevas recibidas desde SAF.
         */
        'create' => env('SAF_TRAININGS_CREATE', true),

        /*
         | Permite actualizar capacitaciones que ya existen.
         */
        'update' => env('SAF_TRAININGS_UPDATE', true),

        /*
         | Permite desactivar capacitaciones SAF que dejaron de estar
         | vigentes o que ya no sean enviadas por el origen.
         |
         | Se conserva false inicialmente para evitar desactivaciones
         | accidentales mientras se define el comportamiento definitivo.
         */
        'deactivate_missing' => env(
            'SAF_TRAININGS_DEACTIVATE_MISSING',
            false
        ),

        /*
         | Código externo utilizado para identificar una capacitación.
         */
        'external_key' => env(
            'SAF_TRAININGS_EXTERNAL_KEY',
            'codigo_evento_externo'
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Comparación mediante hash
    |--------------------------------------------------------------------------
    |
    | Permite determinar si los datos recibidos son diferentes a los
    | almacenados localmente.
    |
    | Si el hash no cambia, el registro podrá clasificarse como:
    |
    | - consultor sin cambios;
    | - capacitación sin cambios.
    |
    */
    'hash' => [
        'enabled' => env('SAF_HASH_ENABLED', true),

        'algorithm' => env('SAF_HASH_ALGORITHM', 'sha256'),
    ],

    /*
    |--------------------------------------------------------------------------
    | Auditoría
    |--------------------------------------------------------------------------
    */
    'audit' => [

        /*
         | Controla si se registrarán las ejecuciones en:
         | tbl_sincronizacion_saf
         */
        'enabled' => env('SAF_AUDIT_ENABLED', true),

        /*
         | Controla si se guardarán los datos recibidos cuando ocurra
         | un error individual.
         */
        'store_error_payload' => env(
            'SAF_STORE_ERROR_PAYLOAD',
            true
        ),

        /*
         | Controla si se guardará información técnica de las excepciones.
         */
        'store_exception_details' => env(
            'SAF_STORE_EXCEPTION_DETAILS',
            true
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Datos sensibles
    |--------------------------------------------------------------------------
    |
    | Estos campos deberán excluirse de los payloads almacenados en errores
    | y logs.
    |
    */
    'sensitive_fields' => [
        'password',
        'password_confirmation',
        'token',
        'access_token',
        'refresh_token',
        'api_key',
        'secret',
        'authorization',
    ],

    /*
    |--------------------------------------------------------------------------
    | Ejecución automática
    |--------------------------------------------------------------------------
    |
    | Esta configuración quedará preparada para cuando se implemente
    | el comando y la tarea programada de sincronización.
    |
    */
    'schedule' => [
        'enabled' => env('SAF_SCHEDULE_ENABLED', false),

        'time' => env('SAF_SCHEDULE_TIME', '02:00'),

        'timezone' => env(
            'SAF_SCHEDULE_TIMEZONE',
            env('APP_TIMEZONE', 'America/El_Salvador')
        ),
    ],

    /*
    |--------------------------------------------------------------------------
    | Bloqueo de ejecuciones simultáneas
    |--------------------------------------------------------------------------
    |
    | Evitará que dos procesos SAF se ejecuten al mismo tiempo.
    |
    */
    'lock' => [
        'enabled' => env('SAF_LOCK_ENABLED', true),

        /*
         | Tiempo máximo del bloqueo, expresado en segundos.
         */
        'seconds' => (int) env('SAF_LOCK_SECONDS', 3600),
    ],

];