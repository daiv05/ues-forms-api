<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class TipoPreguntaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $tiposPreguntas = [
            ['nombre' => 'Abiertas'],
            ['nombre' => 'Cerradas'],
        ];

        foreach ($tiposPreguntas as $tipoPregunta) {
            \App\Models\Catalogo\TipoPregunta::create($tipoPregunta);
        }
    }
}
