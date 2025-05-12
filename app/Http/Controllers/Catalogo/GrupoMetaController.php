<?php

namespace App\Http\Controllers\Catalogo;

use Orion\Http\Controllers\Controller;
use Illuminate\Database\Eloquent\Builder;
use Orion\Http\Requests\Request;
use App\Models\Encuesta\GrupoMeta;
use App\Policies\Catalogo\GrupoMetaPolicy;

class GrupoMetaController extends Controller
{
    protected $model = GrupoMeta::class;
    protected $policy = GrupoMetaPolicy::class;
    protected $request = Request::class;

    public function filterableBy(): array
    {
        return [
            'nombre',
            'estado',
        ];
    }

    public function searchableBy(): array
    {
        return [
            'nombre',
            'descripcion',
        ];
    }

    public function sortableBy(): array
    {
        return [
            'nombre',
            'estado',
        ];
    }

    public function limit(): int
    {
        return 5;
    }

    protected function buildIndexFetchQuery(Request $request, array $requestedRelations): Builder
    {
        $query = parent::buildIndexFetchQuery($request, $requestedRelations);
        $query->where('id_usuario', auth()->user()->id);
        return $query;
    }


}
