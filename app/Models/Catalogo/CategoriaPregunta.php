<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Catalogo\ClasePregunta;
use App\Models\Encuesta\Pregunta;

class CategoriaPregunta extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'qst_categorias_preguntas';
    protected $fillable = [
        'id_clase_pregunta',
        'nombre',
        'descripcion',
        'max_text_length',
        'max_seleccion_items',
        'es_escala_numerica',
        'es_booleano',
        'permite_otros',
        'activo'
    ];

    public function clasePregunta(): BelongsTo
    {
        return $this->belongsTo(ClasePregunta::class, 'id_clase_pregunta');
    }

    public function preguntas(): HasMany
    {
        return $this->hasMany(Pregunta::class, 'id_categoria_pregunta');
    }


}
