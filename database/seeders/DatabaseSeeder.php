<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;
use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Database\Seeders\fac\TipoHabilidadSeeder;
use Database\Seeders\fac\PaisSeeder;
use Database\Seeders\fac\DepartamentoSeeder;
use Database\Seeders\fac\MunicipioSeeder;
use Database\Seeders\fac\IdiomaSeeder;
use Database\Seeders\fac\IdiomaNivelSeeder;
use Database\Seeders\fac\TipoFormacionSeeder;
use Database\Seeders\fac\TipoAtestadoSeeder;
use Database\Seeders\fac\NivelAcademicoSeeder;
use Database\Seeders\fac\TipoReferenciaSeeder;
use Database\Seeders\fac\TipoTelefonoSeeder;
use Database\Seeders\fac\MunicipioMhSeeder;
use Database\Seeders\fac\TipoDisponibilidadSeeder;
use Database\Seeders\fac\TipoDocumentoSeeder;
use Database\Seeders\fac\TipoRedSocialSeeder;

class DatabaseSeeder extends Seeder
{
    use WithoutModelEvents;

    public function run(): void
    {
        $this->call([
            TipoHabilidadSeeder::class,
            PaisSeeder::class,
            DepartamentoSeeder::class,
            MunicipioSeeder::class,
            IdiomaNivelSeeder::class,
            IdiomaSeeder::class,
            TipoFormacionSeeder::class,
            TipoAtestadoSeeder::class,
            NivelAcademicoSeeder::class,
            TipoReferenciaSeeder::class,
            TipoTelefonoSeeder::class,
            MunicipioMhSeeder::class,
            TipoDisponibilidadSeeder::class,
            TipoDocumentoSeeder::class,
            TipoRedSocialSeeder::class,
        ]);
    }
}