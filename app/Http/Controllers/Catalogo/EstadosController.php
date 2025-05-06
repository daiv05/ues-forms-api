<?php

namespace App\Http\Controllers\Catalogo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Enums\EstadosEnum;
use App\Models\Catalogo\Estado;
use Illuminate\Support\Facades\Validator;
use App\Traits\ResponseTrait;

class EstadosController extends Controller
{
    use ResponseTrait;

    public function index(Request $request)
    {
        $validator = Validator::make($request->all(), [
            'scope' => 'required|string|in:users,requests,surveys,all',
        ], [
            'scope.required' => 'Debe especificar un scope.',
            'scope.in' => 'El valor de flag no es válido. Debe ser uno de los siguientes: users, requests, surveys, all.',
        ]);

        if ($validator->fails()) {
            $this->validationError('Error de validación', $validator->errors(), 422);
        }

        $estados = [];
        $scope = $request->scope;
        if ($scope === 'users') {
            $estados = Estado::whereIn('id', EstadosEnum::usuarios())->get();
        } elseif ($scope === 'requests') {
            $estados = Estado::whereIn('id', EstadosEnum::solicitudes())->get();
        } elseif ($scope === 'surveys') {
            $estados = Estado::whereIn('id', EstadosEnum::encuestas())->get();
        } elseif ($scope === 'all') {
            $estados = Estado::all();
        }

        return $this->success('Estados obtenidos correctamente', $estados, 200);
    }
}
