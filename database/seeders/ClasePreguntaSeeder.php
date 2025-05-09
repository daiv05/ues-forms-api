<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class ClasePreguntaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $clasesPreguntas = [
            // Tipo de preguntas abiertas
            ['id_tipo_pregunta' => 1, 'nombre' => 'Texto', 'requiere_lista' => false],
            // Tipo de preguntas cerradas
            ['id_tipo_pregunta' => 2, 'nombre' => 'Selección', 'requiere_lista' => true],
            ['id_tipo_pregunta' => 2, 'nombre' => 'Ranking', 'requiere_lista' => true],
            ['id_tipo_pregunta' => 2, 'nombre' => 'Escala', 'requiere_lista' => false],
            ['id_tipo_pregunta' => 2, 'nombre' => 'Falso/Verdadero', 'requiere_lista' => true],
        ];

        foreach ($clasesPreguntas as $clasePregunta) {
            \App\Models\Catalogo\ClasePregunta::create($clasePregunta);
        }
    }
}
