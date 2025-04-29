<?php

namespace App\Models\Encuesta;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Encuesta\Pregunta;

class PreguntaEscalaNumerica extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'srvy_preguntas_escalas_numericas';
    protected $fillable = [
        'id_pregunta',
        'min_val',
        'max_val',
    ];

    function pregunta(): BelongsTo
    {
        return $this->belongsTo(Pregunta::class, 'id_pregunta');
    }
}
