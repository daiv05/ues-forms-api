<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class EstadoSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $estados = [
            ['nombre' => 'Activo', 'activo' => true],
            ['nombre' => 'Inactivo', 'activo' => true],
            ['nombre' => 'Pendiente', 'activo' => true],
            ['nombre' => 'En edición', 'activo' => true],
            ['nombre' => 'Rechazado', 'activo' => true],
            ['nombre' => 'Aprobado', 'activo' => true],
            ['nombre' => 'Bloqueado', 'activo' => true],

        ];

        foreach ($estados as $estado) {
            \App\Models\Catalogo\Estado::create($estado);
        }
    }
}
