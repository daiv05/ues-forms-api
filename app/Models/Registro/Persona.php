<?php

namespace App\Models\Registro;

use App\Models\Seguridad\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\HasOne;
use OwenIt\Auditing\Contracts\Auditable;

class Persona extends Model implements Auditable
{
    use HasFactory,\OwenIt\Auditing\Auditable;

    protected $table = 'personas';

    protected $fillable = [
        'nombre',
        'apellido',
        'fecha_nacimiento',
        'telefono'
    ];

    public function setNombreAttribute($value)
    {
        $this->attributes['nombre'] =strtoupper(strtr($value, 'áéíóú', 'ÁÉÍÓÚ'));
    }

    public function setApellidoAttribute($value)
    {
        $this->attributes['apellido'] =strtoupper(strtr($value, 'áéíóú', 'ÁÉÍÓÚ'));
    }
    public function usuario() : HasOne
    {
        return $this->hasOne(User::class, 'id_persona');
    }
}
