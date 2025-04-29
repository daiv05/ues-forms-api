<?php

namespace App\Models\Encuesta;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Seguridad\User;

class Encuesta extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'srvy_encuestas';
    protected $fillable = [
        'id_usuario',
        'id_grupo_meta',
        'codigo',
        'titulo',
        'objetivo',
        'instrucciones',
        'fecha_publicacion',
    ];

    public function grupoMeta(): BelongsTo
    {
        return $this->belongsTo(GrupoMeta::class, 'id_grupo_meta');
    }

    public function user(): BelongsTo
    {
        return $this->belongsTo(User::class, 'id_usuario');
    }

    public function preguntas(): HasMany
    {
        return $this->hasMany(Pregunta::class, 'id_encuesta');
    }

}
