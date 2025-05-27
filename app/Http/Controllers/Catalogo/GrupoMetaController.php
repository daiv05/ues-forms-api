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
            'activo',
        ];
    }

    public function limit(): int
    {
        return 10;
    }

    protected function runIndexFetchQuery(Request $request, Builder $query, int $paginationLimit)
    {
        if ($request->pagination == 'false') return $query->get();
        return $query->paginate($paginationLimit);
    }

    protected function buildIndexFetchQuery(Request $request, array $requestedRelations): Builder
    {
        $query = parent::buildIndexFetchQuery($request, $requestedRelations);
        $query->where('id_usuario', auth()->user()->id)
              ->orderBy('nombre', 'asc');
        return $query;
    }

    protected function beforeStore(Request $request, $entity): void
    {
        $entity->id_usuario = auth()->user()->id;
    }


}
