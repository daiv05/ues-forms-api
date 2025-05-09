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
                'nombre' => 'short', 
                'descripcion' => 'Texto corto', 
                'id_clase_pregunta' => 1,
                'max_text_length' => 25,
            ],
            [ 
                'nombre' => 'long', 
                'descripcion' => 'Texto largo', 
                'id_clase_pregunta' => 1,
                'max_text_length' => 150,
            ],
            [ 
                'nombre' => 'multiple', 
                'descripcion' => 'Selección múltiple', 
                'id_clase_pregunta' => 2,
                'max_seleccion_items' => 10,
                'permite_otros' => true,
            ],
            [  
                'nombre' => 'single', 
                'descripcion' => 'Selección única', 
                'id_clase_pregunta' => 2,
                'max_seleccion_items' => 1,
                'permite_otros' => true,
            ],
            [ 
                'nombre' => 'order', 
                'descripcion' => 'Orden', 
                'id_clase_pregunta' => 3,
                'max_seleccion_items' => 10,
            ],
            [ 
                'nombre' => 'numeric', 
                'descripcion' => 'Escala numérica', 
                'id_clase_pregunta' => 4,
                'es_escala_numerica' => true,
            ],
            [ 
                'nombre' => 'likert', 
                'descripcion' => 'Escala / Escala Likert', 
                'id_clase_pregunta' => 4
            ],
            [  
                'nombre' => 'truefalse', 
                'descripcion' => 'Falso / Verdadero', 
                'id_clase_pregunta' => 5,
                'es_booleano' => true,
            ]
        ];

        foreach ($categoriasPreguntas as $categoriaPregunta) {
            \App\Models\Catalogo\CategoriaPregunta::create($categoriaPregunta);
        }
    }
}
