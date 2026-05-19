<?php

namespace Database\Seeders;

use App\Models\User;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;
use Database\Seeders\fac\TipoHabilidadSeeder;
use Database\Seeders\fac\PaisSeeder;
use Database\Seeders\fac\DepartamentoSeeder;
use Database\Seeders\fac\MunicipioSeeder;
use Database\Seeders\fac\IdiomaSeeder;
use Database\Seeders\fac\IdiomaLevelSeeder;
use Database\Seeders\fac\TipoAtestadoSeeder;
use Database\Seeders\fac\NivelAcademicoSeeder;
use Database\Seeders\fac\TipoFormacionSeeder;
use Database\Seeders\fac\TipoReferenciaSeeder;
use Database\Seeders\fac\TipoTelefonoSeeder;
class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call([
            TipoHabilidadSeeder::class,
            PaisSeeder::class,
            DepartamentoSeeder::class,
            MunicipioSeeder::class,
            IdiomaSeeder::class,
            IdiomaLevelSeeder::class,
            TipoAtestadoSeeder::class,
            NivelAcademicoSeeder::class,
            TipoFormacionSeeder::class,
            TipoReferenciaSeeder::class,
            TipoTelefonoSeeder::class,
        ]);
    }
}
