<?php

namespace App\Models\Registro;

use App\Models\Seguridad\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;

class Persona extends Model
{
    use HasFactory;

    protected $table = 'personas';

    protected $fillable = [
        'nombre',
        'apellido',
        'identificacion'
    ];
    
    public function usuario() : HasOne
    {
        return $this->hasOne(User::class, 'id_persona');
    }
}
