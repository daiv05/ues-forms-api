<?php

namespace App\Models\Encuesta;

use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Seguridad\User;

class GrupoMeta extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'srvy_grupos_metas';
    protected $fillable = [
        'id_usuario',
        'nombre',
        'descripcion',
        'estado',
    ];
    protected $hidden = [
        'updated_at',
        'deleted_at',
    ];

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function encuestas(): HasMany
    {
        return $this->hasMany(Encuesta::class, 'id_grupo_meta');
    }
}
