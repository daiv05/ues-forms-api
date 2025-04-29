<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\HasMany;

class TipoPregunta extends Model
{
    use HasFactory;
    use SoftDeletes;
    protected $table = 'qst_tipos_preguntas';
    protected $fillable = [
        'nombre',
    ];

    function clasesPreguntas(): HasMany
    {
        return $this->hasMany(ClasePregunta::class, 'id_tipo_pregunta');
    }
}
