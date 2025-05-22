<?php

namespace App\Models\Respuesta;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class EncuestaRespuesta extends Model
{
    use HasFactory,SoftDeletes;

    protected $table = 'answ_encuesta_respuestas';

    protected $fillable = [
        'id_encuesta',
        'id_encuestado'
    ];

    public function encuestado()
    {
        return $this->belongsTo(Encuestado::class,'id_encuestado');
    }

    public function respuestaPregunta()
    {
        return $this->hasMany(RespuestaPregunta::class,'id_encuesta_respuesta');
    }
}
