<?php

namespace Database\Seeders;

use Illuminate\Database\Console\Seeds\WithoutModelEvents;
use Illuminate\Database\Seeder;

class CategoriaPreguntaSeeder extends Seeder
{
    /**
     * Run the database seeds.
     */
    public function run(): void
    {
        $categoriasPreguntas = [
            [
                'codigo' => 'short_text',
                'nombre' => 'Texto corto',
                'descripcion' => 'Preguntas con respuestas de texto corto',
                'id_clase_pregunta' => 1,
                'max_text_length' => 25,
            ],
            [
                'codigo' => 'long_text',
                'nombre' => 'Texto largo',
                'descripcion' => 'Preguntas con respuestas de texto largo',
                'id_clase_pregunta' => 1,
                'max_text_length' => 150,
            ],
            [
                'codigo' => 'multiple_choice',
                'nombre' => 'Selección múltiple',
                'descripcion' => 'Preguntas con selección múltiple de opciones',
                'id_clase_pregunta' => 2,
                'max_seleccion_items' => 10,
                'permite_otros' => true,
            ],
            [
                'codigo' => 'single_choice',
                'nombre' => 'Selección única',
                'descripcion' => 'Preguntas con selección única de opciones',
                'id_clase_pregunta' => 2,
                'max_seleccion_items' => 1,
                'permite_otros' => true,
            ],
            [
                'codigo' => 'ranking',
                'nombre' => 'Ordenamiento/Ranking',
                'descripcion' => 'Preguntas con ordenamiento de opciones',
                'id_clase_pregunta' => 3,
                'max_seleccion_items' => 10,
            ],
            [
                'codigo' => 'numeric_scale',
                'nombre' => 'Escala numérica',
                'descripcion' => 'Preguntas con escala numérica',
                'id_clase_pregunta' => 4,
                'es_escala_numerica' => true,
            ],
            [
                'codigo' => 'likert_scale',
                'nombre' => 'Escala/Escala Likert',
                'descripcion' => 'Preguntas de escala o Likert',
                'id_clase_pregunta' => 4
            ],
            [
                'codigo' => 'true_false',
                'nombre' => 'Falso/Verdadero',
                'descripcion' => 'Preguntas de verdadero o falso',
                'id_clase_pregunta' => 5,
                'es_booleano' => true,
            ]
        ];

        foreach ($categoriasPreguntas as $categoriaPregunta) {
            \App\Models\Catalogo\CategoriaPregunta::create($categoriaPregunta);
        }
    }
}
