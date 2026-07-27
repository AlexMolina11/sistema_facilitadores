<?php

use Illuminate\Database\Migrations\Migration;
use Illuminate\Database\Schema\Blueprint;
use Illuminate\Support\Facades\Schema;

return new class extends Migration
{
    /**
     * Crea la tabla de errores individuales producidos
     * durante las sincronizaciones con SAF.
     */
    public function up(): void
    {
        Schema::create(
            'tbl_sincronizacion_saf_error',
            function (Blueprint $table) {
                /*
                 |--------------------------------------------------------------------------
                 | Llave primaria
                 |--------------------------------------------------------------------------
                 */
                $table->increments('id_sincronizacion_saf_error');

                /*
                 |--------------------------------------------------------------------------
                 | Sincronización relacionada
                 |--------------------------------------------------------------------------
                 |
                 | Todo error debe pertenecer a una ejecución registrada
                 | previamente en tbl_sincronizacion_saf.
                 |
                 | Si una sincronización fuera eliminada directamente desde
                 | la base de datos, también se eliminarían sus errores.
                 |
                 */
                $table->unsignedInteger('id_sincronizacion_saf');

                /*
                 |--------------------------------------------------------------------------
                 | Clasificación del registro
                 |--------------------------------------------------------------------------
                 |
                 | Valores previstos inicialmente:
                 |
                 | - CONSULTOR
                 | - CAPACITACION
                 | - GENERAL
                 |
                 | GENERAL se utilizará para errores que afectan al proceso
                 | completo y que no pertenecen a un registro específico.
                 |
                 */
                $table->string('tipo_registro', 40)
                    ->default('GENERAL');

                /*
                 |--------------------------------------------------------------------------
                 | Operación que se intentaba ejecutar
                 |--------------------------------------------------------------------------
                 |
                 | Valores previstos:
                 |
                 | - CONSULTAR
                 | - CREAR
                 | - ACTUALIZAR
                 | - DESACTIVAR
                 | - VALIDAR
                 | - PROCESAR
                 |
                 */
                $table->string('tipo_operacion', 40)
                    ->nullable();

                /*
                 |--------------------------------------------------------------------------
                 | Identificadores del registro
                 |--------------------------------------------------------------------------
                 |
                 | id_registro_externo:
                 | Identificador enviado por SAF, por ejemplo id_instructor
                 | o codigo_evento_externo.
                 |
                 | Se utiliza string porque distintos tipos de registros
                 | podrían utilizar identificadores numéricos o alfanuméricos.
                 |
                 | id_registro_local:
                 | Llave primaria local relacionada con el error, cuando exista.
                 |
                 */
                $table->string('id_registro_externo', 150)
                    ->nullable();

                $table->unsignedBigInteger('id_registro_local')
                    ->nullable();

                /*
                 |--------------------------------------------------------------------------
                 | Código y mensaje del error
                 |--------------------------------------------------------------------------
                 |
                 | codigo_error:
                 | Identificador técnico o funcional del tipo de error.
                 |
                 | Ejemplos:
                 | - SAF_VALIDATION_ERROR
                 | - CONSULTOR_NOT_FOUND
                 | - DUPLICATE_INSTRUCTOR
                 | - DATABASE_ERROR
                 |
                 | mensaje:
                 | Explicación legible para administradores.
                 |
                 */
                $table->string('codigo_error', 100)
                    ->nullable();

                $table->text('mensaje');

                /*
                 |--------------------------------------------------------------------------
                 | Información técnica
                 |--------------------------------------------------------------------------
                 |
                 | detalle_tecnico:
                 | Mensaje ampliado destinado a soporte técnico.
                 |
                 | excepcion:
                 | Nombre completo de la clase de excepción.
                 |
                 | archivo y linea:
                 | Ubicación donde se produjo una excepción interna.
                 |
                 */
                $table->longText('detalle_tecnico')
                    ->nullable();

                $table->string('excepcion', 255)
                    ->nullable();

                $table->text('archivo')
                    ->nullable();

                $table->unsignedInteger('linea')
                    ->nullable();

                /*
                 |--------------------------------------------------------------------------
                 | Datos recibidos
                 |--------------------------------------------------------------------------
                 |
                 | Almacena una copia controlada de los datos asociados con
                 | el registro que produjo el error.
                 |
                 | Antes de guardar esta información, el servicio deberá
                 | excluir contraseñas, tokens u otros datos confidenciales.
                 |
                 */
                $table->json('datos_recibidos')
                    ->nullable();

                /*
                 |--------------------------------------------------------------------------
                 | Control de resolución
                 |--------------------------------------------------------------------------
                 |
                 | Un error puede ser revisado posteriormente por un usuario.
                 |
                 | resuelto:
                 | Indica si el incidente ya fue atendido.
                 |
                 | fecha_resolucion:
                 | Momento en que fue marcado como resuelto.
                 |
                 | usuario_resuelve:
                 | Usuario responsable de revisar o resolver el incidente.
                 |
                 | observacion_resolucion:
                 | Explicación de la acción tomada.
                 |
                 */
                $table->boolean('resuelto')
                    ->default(false);

                $table->timestamp('fecha_resolucion')
                    ->nullable();

                $table->unsignedInteger('usuario_resuelve')
                    ->nullable();

                $table->text('observacion_resolucion')
                    ->nullable();

                /*
                 |--------------------------------------------------------------------------
                 | Timestamps de Laravel
                 |--------------------------------------------------------------------------
                 */
                $table->timestamps();

                /*
                 |--------------------------------------------------------------------------
                 | Índices
                 |--------------------------------------------------------------------------
                 */
                $table->index(
                    'id_sincronizacion_saf',
                    'idx_saf_error_sincronizacion'
                );

                $table->index(
                    'tipo_registro',
                    'idx_saf_error_tipo_registro'
                );

                $table->index(
                    'tipo_operacion',
                    'idx_saf_error_tipo_operacion'
                );

                $table->index(
                    'id_registro_externo',
                    'idx_saf_error_registro_externo'
                );

                $table->index(
                    'codigo_error',
                    'idx_saf_error_codigo'
                );

                $table->index(
                    'resuelto',
                    'idx_saf_error_resuelto'
                );

                $table->index(
                    ['id_sincronizacion_saf', 'resuelto'],
                    'idx_saf_error_sincronizacion_resuelto'
                );

                $table->index(
                    ['tipo_registro', 'id_registro_externo'],
                    'idx_saf_error_tipo_registro_externo'
                );

                $table->index(
                    'usuario_resuelve',
                    'idx_saf_error_usuario_resuelve'
                );

                /*
                 |--------------------------------------------------------------------------
                 | Llaves foráneas
                 |--------------------------------------------------------------------------
                 */
                $table->foreign('id_sincronizacion_saf')
                    ->references('id_sincronizacion_saf')
                    ->on('tbl_sincronizacion_saf')
                    ->cascadeOnDelete();

                $table->foreign('usuario_resuelve')
                    ->references('id_usuario')
                    ->on('seg_usuarios')
                    ->nullOnDelete();
            }
        );
    }

    /**
     * Elimina la tabla de errores de sincronización SAF.
     */
    public function down(): void
    {
        Schema::dropIfExists('tbl_sincronizacion_saf_error');
    }
};