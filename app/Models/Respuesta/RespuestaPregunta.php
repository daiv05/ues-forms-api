<?php

namespace App\Models\Respuesta;

use App\Models\Encuesta\Encuesta;
use App\Models\Encuesta\Pregunta;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use PhpParser\Node\Expr\PreDec;

class RespuestaPregunta extends Model
{
    use HasFactory,SoftDeletes;

    protected $table='answ_respuestas_preguntas';

    protected $fillable =[];

    public function encuestaRespuesta()
    {
        return $this->belongsTo(EncuestaRespuesta::class,'id_encuesta_respuesta');
    }

    public function opcionSeleccionada()
    {
        return $this->hasMany(OpcionSeleccionada::class,'id_respuesta_pregunta');
    }

    public function pregunta()
    {
        return $this->belongsTo(Pregunta::class, 'id_pregunta');
    }
}
