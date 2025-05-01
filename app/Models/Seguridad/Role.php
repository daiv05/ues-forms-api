<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class Role extends Model
{
    use HasFactory;

    protected $fillable = ['name', 'description', 'activo'];

    protected $table = 'roles';

    public function setNameAttribute($value)
    {
        $this->attributes['name'] = strtoupper(strtr($value, 'áéíóú', 'ÁÉÍÓÚ'));
    }
}
