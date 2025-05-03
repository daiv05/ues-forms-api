<?php

namespace App\Models\Catalogo;

use App\Models\Seguridad\User;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

class Estado extends Model
{
    use HasFactory;

    protected $table = 'ctl_estados';
    protected $fillable = [
        'nombre',
        'activo',
    ];

    public function usuarios(): HasMany
    {
        return $this->hasMany(User::class, 'id_estado');
    }
}
