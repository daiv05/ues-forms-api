<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Catalogo\ClasePregunta;
use App\Models\Encuesta\Pregunta;

class CatalogoTipoPregunta extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'qst_catalogo_tipos_preguntas';
    protected $fillable = [
        'id_clase_pregunta',
        'nombre',
        'descripcion',
        'es_txt_largo',
        'es_escala_num',
        'max_seleccion',
        'permite_otros',
    ];

    public function clasePregunta(): BelongsTo
    {
        return $this->belongsTo(ClasePregunta::class, 'id_clase_pregunta');
    }

    public function preguntas(): HasMany
    {
        return $this->hasMany(Pregunta::class, 'id_catalogo_tipo_pregunta');
    }


}
