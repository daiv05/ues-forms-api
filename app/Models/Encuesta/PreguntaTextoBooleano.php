<?php

namespace App\Models\Encuesta;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Encuesta\Pregunta;

class PreguntaTextoBooleano extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'srvy_preguntas_texto_booleano';
    protected $fillable = [
        'id_pregunta',
        'false_txt',
        'true_txt',
    ];

    function pregunta(): BelongsTo
    {
        return $this->belongsTo(Pregunta::class, 'id_pregunta');
    }
}
