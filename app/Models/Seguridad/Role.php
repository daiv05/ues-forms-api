<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use OwenIt\Auditing\Contracts\Auditable;

class Role extends Model  implements Auditable
{
    use HasFactory,\OwenIt\Auditing\Auditable;

    protected $fillable = ['name', 'activo'];
    protected $table = 'roles';


    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper(strtr($value, 'áéíóú', 'ÁÉÍÓÚ'));
    }
}
