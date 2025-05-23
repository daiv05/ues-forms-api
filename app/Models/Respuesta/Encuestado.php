<?php

namespace App\Models\Respuesta;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class Encuestado extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'answ_encuestados';

    protected $fillable = [
        'correo',
        'nombres',
        'apellidos',
        'fecha_nacimiento',
        'telefono',
        'edad',
        'activo'
    ];

    public function encuestasRespuestas()
    {
        return $this->hasMany(EncuestaRespuesta::class, 'id_encuestado');
    }
}
