<?php

namespace App\Http\Controllers\Seguridad;

use App\Enums\GeneralEnum;
use App\Http\Controllers\Controller;
use App\Models\Seguridad\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\PaginationTrait;

class UsuarioController extends Controller
{
    use ResponseTrait, PaginationTrait;

    // API
    public function index(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre_usuario' => 'string|max:50|regex:/^[a-zA-Z0-9_]+$/',
                'id_estado' => 'integer|exists:estados,id',
            ], [
                'nombre_usuario.regex' => 'El nombre/usuario solo puede contener letras, números y guiones bajos',
                'nombre_usuario.max' => 'El nombre/usuario no puede exceder los 50 caracteres',
                'id_estado.exists' => 'El estado debe existir en la base de datos',
                'id_estado.integer' => 'El estado debe ser un número entero'
            ]);

            if ($validator->fails()) {
                return $this->validationError('Ocurrió un error de validación', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validatedData = $validator->validated();
            $query = User::with('persona');
            if (isset($validatedData['nombre_usuario'])) {
                $query->where('username', 'like', '%' . $validatedData['nombre_usuario'] . '%');
            }
            if (isset($validatedData['id_estado'])) {
                $query->where('id_estado', $validatedData['id_estado']);
            }
            $usuarios = $query->get();
            if ($request['paginate'] === "true") {
                $paginatedData = $this->paginate($usuarios->toArray(), $request['per_page'] ?? GeneralEnum::PAGINACION->value, $request['page'] ?? 1);
                return $this->successPaginated('Usuarios obtenidos exitosamente', $paginatedData, Response::HTTP_OK);
            } else {
                return $this->success('Usuarios obtenidos exitosamente', $usuarios, Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            return $this->error('Error al obtener los usuarios', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:50|unique:users,username|regex:/^[a-zA-Z0-9_]+$/',
                'email' => 'required|string|email|max:255|unique:users,email',
                'password' => 'required|string|min:8|max:255',
                'nombres' => 'string|max:50|regex:/^[a-zA-Z\s]+$/',
                'apellidos' => 'string|max:50|regex:/^[a-zA-Z\s]+$/',
                'identificacion' => 'string|max:20|regex:/^[a-zA-Z0-9]+$/',
                'activo' => 'boolean',
                'id_estado' => 'integer|exists:estados,id',
            ], [
                'username.regex' => 'El nombre/usuario solo puede contener letras, números y guiones bajos',
                'username.max' => 'El nombre/usuario no puede exceder los 50 caracteres',
                'username.unique' => 'El nombre/usuario ya existe en la base de datos',
                'email.required' => 'El correo electrónico es obligatorio',
                'email.email' => 'El correo electrónico no es válido',
                'email.max' => 'El correo electrónico no puede exceder los 255 caracteres',
                'email.unique' => 'El correo electrónico ya existe en la base de datos',
                'password.required' => 'La contraseña es obligatoria',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres',
                'password.max' => 'La contraseña no puede exceder los 255 caracteres',
                'id_persona.required' => 'La persona es obligatoria',
                'id_persona.integer' => 'La persona debe ser un número entero',
                'id_persona.exists' => 'La persona no existe en la base de datos',
            ]);

            if ($validator->fails()) {
                return $this->validationError('Ocurrió un error de validación', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validatedData = $validator->validated();

            $usuario = User::create([
                ...$validatedData,
            ]);

            return $this->success('Usuario creado exitosamente', $usuario, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->error('Error al crear el usuario', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
