<?php

namespace Database\Seeders;

use Illuminate\Database\Seeder;

class DatabaseSeeder extends Seeder
{
    /**
     * Seed the application's database.
     */
    public function run(): void
    {
        $this->call(SedesSeeder::class);
        $this->call(EntidadesSeeder::class);
        $this->call(FacultadesSeeder::class);
        $this->call(EscuelaSeeder::class);
        $this->call(PuestoSeeder::class);
        $this->call(PermisoSeeder::class);
        $this->call(RolSeeder::class);
        $this->call(PersonaSeeder::class);
        $this->call(UserSeeder::class);
        $this->call(ModalidadSeeder::class);
        $this->call(TipoClaseSeeder::class);
        $this->call(DiaSeeder::class);
        $this->call(TipoEventoSeeder::class);
        $this->call(AulaSeeder::class);
        //$this->call(AsignaturaSeeder::class);
        $this->call(ReporteSeeder::class);
        $this->call(EstadoSeeder::class);
        $this->call(EmpleadoPuestoSeeder::class);
        $this->call(TiposCiclosSeeder::class);
        $this->call(FondoSeeder::class);
        $this->call(RecursoSeeder::class);
        $this->call(UnidadMedidaSeeder::class);
        $this->call(TipoBienSeeder::class);
        $this->call(BienSeeder::class);
    }

}
