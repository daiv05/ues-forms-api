<?php

namespace App\Models\Respuesta;

use App\Models\Encuesta\Pregunta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class RespuestaPregunta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'answ_respuestas_preguntas';

    protected $fillable = [
        'id_encuesta_respuesta',
        'id_pregunta',
        'respuesta_texto',
        'respuesta_numero',
        'respuesta_fecha',
        'respuesta_booleano',
        'es_abierta',
    ];

    public function encuestaRespuesta()
    {
        return $this->belongsTo(EncuestaRespuesta::class, 'id_encuesta_respuesta');
    }

    public function opcionesSeleccionadas()
    {
        return $this->hasMany(OpcionSeleccionada::class, 'id_respuesta_pregunta');
    }

    public function pregunta()
    {
        return $this->belongsTo(Pregunta::class, 'id_pregunta');
    }
}
