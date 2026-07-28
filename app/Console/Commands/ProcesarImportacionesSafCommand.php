<?php

namespace App\Console\Commands;

use App\Modules\Fac\Models\SafCapacitacionImportacion;
use App\Modules\Fac\Models\SafInstructorImportacion;
use App\Modules\Fac\Models\SincronizacionSaf;
use App\Modules\Fac\Services\Saf\SafAuditService;
use App\Modules\Fac\Services\Saf\SafCapacitacionImportacionProcessor;
use App\Modules\Fac\Services\Saf\SafInstructorImportacionProcessor;
use Illuminate\Console\Command;
use Throwable;

class ProcesarImportacionesSafCommand extends Command
{
    /**
     * Nombre y opciones del comando.
     *
     * @var string
     */
    protected $signature = 'saf:procesar-importaciones
        {--limite-instructores=100 : Número máximo de instructores por ejecución}
        {--limite-capacitaciones=100 : Número máximo de capacitaciones por ejecución}';

    /**
     * Descripción del comando.
     *
     * @var string
     */
    protected $description = 'Procesa los instructores y capacitaciones pendientes recibidos desde SAF';

    public function __construct(
        private readonly SafAuditService $auditoria,
        private readonly SafInstructorImportacionProcessor $instructores,
        private readonly SafCapacitacionImportacionProcessor $capacitaciones
    ) {
        parent::__construct();
    }

    /**
     * Ejecuta el comando.
     */
    public function handle(): int
    {
        $limiteInstructores = $this->resolverLimite(
            'limite-instructores'
        );

        $limiteCapacitaciones = $this->resolverLimite(
            'limite-capacitaciones'
        );

        if (
            $limiteInstructores === false
            || $limiteCapacitaciones === false
        ) {
            return self::FAILURE;
        }

        $totalInstructores = $this->contarInstructores(
            $limiteInstructores
        );

        $totalCapacitaciones = $this->contarCapacitaciones(
            $limiteCapacitaciones
        );

        $totalRegistros = $totalInstructores
            + $totalCapacitaciones;

        $this->newLine();

        $this->info(
            'Iniciando procesamiento de importaciones SAF.'
        );

        $this->line(
            sprintf(
                'Instructores pendientes a procesar: %d',
                $totalInstructores
            )
        );

        $this->line(
            sprintf(
                'Capacitaciones pendientes a procesar: %d',
                $totalCapacitaciones
            )
        );

        $this->line(
            sprintf(
                'Total de registros detectados: %d',
                $totalRegistros
            )
        );

        $this->newLine();

        $ejecucion = null;

        try {
            $ejecucion = $this->auditoria->iniciar(
                SincronizacionSaf::TIPO_MANUAL
            );

            $ejecucion = $this->auditoria->establecerTotal(
                $ejecucion,
                $totalRegistros
            );

            $resumenInstructores = $this->procesarInstructores(
                $ejecucion,
                $limiteInstructores
            );

            $resumenCapacitaciones =
                $this->procesarCapacitaciones(
                    $ejecucion,
                    $limiteCapacitaciones
                );

            $resumenGeneral = [
                'instructores' => $resumenInstructores,

                'capacitaciones' => $resumenCapacitaciones,
            ];

            $ejecucion = $this->auditoria->finalizar(
                $ejecucion,
                $resumenGeneral
            );

            $this->mostrarResumen(
                $resumenInstructores,
                $resumenCapacitaciones
            );

            $this->newLine();

            $this->info(
                sprintf(
                    'Sincronización SAF finalizada. ID de ejecución: %s',
                    $ejecucion->getKey()
                )
            );

            return self::SUCCESS;
        } catch (Throwable $exception) {
            $this->newLine();

            $this->error(
                'La ejecución SAF terminó por un error no controlado.'
            );

            $this->error(
                $exception->getMessage()
            );

            return self::FAILURE;
        }
    }

    /**
     * Procesa instructores pendientes.
     *
     * @return array{
     *     detectados: int,
     *     procesados: int,
     *     exitosos: int,
     *     con_error: int
     * }
     */
    private function procesarInstructores(
        SincronizacionSaf $ejecucion,
        int $limite
    ): array {
        $this->info(
            'Procesando instructores SAF...'
        );

        $resumen = $this->instructores->procesar(
            ejecucion: $ejecucion,
            limite: $limite
        );

        $this->line(
            sprintf(
                'Instructores procesados: %d',
                $resumen['procesados']
            )
        );

        return $resumen;
    }

    /**
     * Procesa capacitaciones pendientes.
     *
     * @return array{
     *     detectados: int,
     *     procesados: int,
     *     exitosos: int,
     *     con_error: int
     * }
     */
    private function procesarCapacitaciones(
        SincronizacionSaf $ejecucion,
        int $limite
    ): array {
        $this->newLine();

        $this->info(
            'Procesando capacitaciones SAF...'
        );

        $resumen = $this->capacitaciones->procesar(
            ejecucion: $ejecucion,
            limite: $limite
        );

        $this->line(
            sprintf(
                'Capacitaciones procesadas: %d',
                $resumen['procesados']
            )
        );

        return $resumen;
    }

    /**
     * Cuenta instructores pendientes respetando el límite.
     */
    private function contarInstructores(
        int $limite
    ): int {
        $pendientes = SafInstructorImportacion::query()
            ->pendientes()
            ->count();

        return min(
            $pendientes,
            $limite
        );
    }

    /**
     * Cuenta capacitaciones pendientes respetando el límite.
     */
    private function contarCapacitaciones(
        int $limite
    ): int {
        $pendientes = SafCapacitacionImportacion::query()
            ->pendientes()
            ->count();

        return min(
            $pendientes,
            $limite
        );
    }

    /**
     * Obtiene y valida una opción numérica.
     */
    private function resolverLimite(
        string $opcion
    ): int|false {
        $valor = $this->option($opcion);

        if (
            ! is_numeric($valor)
            || (int) $valor < 1
        ) {
            $this->error(
                sprintf(
                    'La opción --%s debe ser un número entero mayor que cero.',
                    $opcion
                )
            );

            return false;
        }

        return (int) $valor;
    }

    /**
     * Muestra el resumen de la ejecución.
     *
     * @param array{
     *     detectados: int,
     *     procesados: int,
     *     exitosos: int,
     *     con_error: int
     * } $instructores
     * @param array{
     *     detectados: int,
     *     procesados: int,
     *     exitosos: int,
     *     con_error: int
     * } $capacitaciones
     */
    private function mostrarResumen(
        array $instructores,
        array $capacitaciones
    ): void {
        $this->newLine();

        $this->info(
            'Resumen de la ejecución SAF'
        );

        $this->table(
            [
                'Tipo',
                'Detectados',
                'Procesados',
                'Exitosos',
                'Con error',
            ],
            [
                [
                    'Instructores',
                    $instructores['detectados'],
                    $instructores['procesados'],
                    $instructores['exitosos'],
                    $instructores['con_error'],
                ],
                [
                    'Capacitaciones',
                    $capacitaciones['detectados'],
                    $capacitaciones['procesados'],
                    $capacitaciones['exitosos'],
                    $capacitaciones['con_error'],
                ],
                [
                    'Total',
                    $instructores['detectados']
                        + $capacitaciones['detectados'],

                    $instructores['procesados']
                        + $capacitaciones['procesados'],

                    $instructores['exitosos']
                        + $capacitaciones['exitosos'],

                    $instructores['con_error']
                        + $capacitaciones['con_error'],
                ],
            ]
        );
    }
}
