<?php

namespace App\Models\Encuesta;

use App\Models\Catalogo\CatalogoTipoPregunta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Pregunta extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'srvy_preguntas';
    protected $fillable = [
        'id_catalogo_tipo_pregunta',
        'id_encuesta',
        'descripcion',
        'es_abierta',
    ];

    function encuesta(): BelongsTo
    {
        return $this->belongsTo(Encuesta::class, 'id_encuesta');
    }

    function catalogoTipoPregunta(): BelongsTo
    {
        return $this->belongsTo(CatalogoTipoPregunta::class, 'id_catalogo_tipo_pregunta');
    }

    function preguntasTextosBooleanos(): HasMany
    {
        return $this->hasMany(PreguntaTextoBooleano::class, 'id_pregunta');
    }

    function preguntasEscalasNumericas(): HasMany
    {
        return $this->hasMany(PreguntaEscalaNumerica::class, 'id_pregunta');
    }

    function preguntasOpciones(): HasMany
    {
        return $this->hasMany(PreguntaOpcion::class, 'id_pregunta');
    }

}
