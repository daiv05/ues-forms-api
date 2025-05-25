<?php

namespace App\Models\Respuesta;

use App\Models\Encuesta\Encuesta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EncuestaRespuesta extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'answ_encuesta_respuestas';

    protected $fillable = [
        'id_encuesta',
        'id_encuestado'
    ];

    public function encuestado()
    {
        return $this->belongsTo(Encuestado::class, 'id_encuestado');
    }

    public function encuesta()
    {
        return $this->belongsTo(Encuesta::class, 'id_encuesta');
    }

    public function respuestasPreguntas()
    {
        return $this->hasMany(RespuestaPregunta::class, 'id_encuesta_respuesta');
    }
}
