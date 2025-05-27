<?php

namespace App\Http\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\Catalogo\CategoriaPregunta;
use Illuminate\Support\Facades\Validator;
use App\Traits\ResponseTrait;

class CategoriasPreguntasController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        $categorias = CategoriaPregunta::with(['clasePregunta.tipoPregunta'])
            ->where('activo', true)
            ->get();

        $categoriasMap = $categorias->map(function ($categoria) {
            return [
                'id' => $categoria->id,
                'codigo' => $categoria->codigo,
                'nombre' => $categoria->nombre,
                'descripcion' => $categoria->descripcion,
                'max_text_length' => $categoria->max_text_length,
                'max_seleccion_items' => $categoria->max_seleccion_items,
                'es_escala_numerica' => $categoria->es_escala_numerica,
                'es_booleano' => $categoria->es_booleano,
                'permite_otros' => $categoria->permite_otros,
                "id_clase_pregunta" => $categoria->clasePregunta->id,
                'clase_pregunta' => $categoria->clasePregunta->nombre,
                'requiere_lista' => $categoria->clasePregunta->requiere_lista,
                'id_tipo_pregunta' => $categoria->clasePregunta->tipoPregunta->id,
                'tipo_pregunta' => $categoria->clasePregunta->tipoPregunta->nombre
            ];
        });
        return $this->success('Categorías obtenidas correctamente', $categoriasMap, 200);
    }
}
