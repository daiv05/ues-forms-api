<?php

namespace App\Http\Controllers\Catalogo;

use Orion\Http\Controllers\Controller;
use Orion\Http\Requests\Request;
use App\Models\Catalogo\TipoPregunta;
use App\Policies\Catalogo\TipoPreguntaPolicy;

class TiposPreguntasController extends Controller
{
    protected $model = TipoPregunta::class;
    protected $policy = TipoPreguntaPolicy::class;
    protected $request = Request::class;
}
