<?php

namespace App\Models\Respuesta;

use App\Models\Encuesta\PreguntaOpcion;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class OpcionSeleccionada extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'answ_opciones_seleccionadas';

    protected $fillable = [
        'id_pregunta_opcion',
        'id_respuesta_pregunta',
        'orden_final'
    ];

    public function respuestaPregunta()
    {
        return $this->belongsTo(RespuestaPregunta::class, 'id_respuesta_pregunta');
    }

    public function preguntaOpcion()
    {
        return $this->belongsTo(PreguntaOpcion::class, 'id_pregunta_opcion');
    }
}
