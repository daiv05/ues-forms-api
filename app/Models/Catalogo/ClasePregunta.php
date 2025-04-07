<?php

namespace App\Models\Catalogo;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use App\Models\Catalogo\TipoPregunta;
use App\Models\Catalogo\CatalogoTipoPregunta;

class ClasePregunta extends Model
{
    use HasFactory;
    use SoftDeletes;

    protected $table = 'qst_clases_preguntas';
    protected $fillable = [
        'id_tipo_pregunta',
        'nombre',
        'requiere_lista',
    ];

    function tipoPregunta(): BelongsTo
    {
        return $this->belongsTo(TipoPregunta::class, 'id_tipo_pregunta');
    }

    public function catalogoTiposPreguntas(): HasMany
    {
        return $this->hasMany(CatalogoTipoPregunta::class, 'id_clase_pregunta');
    }

}
