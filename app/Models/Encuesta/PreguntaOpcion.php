<?php

namespace App\Models\Encuesta;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use App\Models\Encuesta\Pregunta;

class PreguntaOpcion extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'srvy_preguntas_opciones';
    protected $fillable = [
        'id_pregunta',
        'opcion',
        'orden_inicial',
    ];

    function pregunta(): BelongsTo
    {
        return $this->belongsTo(Pregunta::class, 'id_pregunta');
    }
}
