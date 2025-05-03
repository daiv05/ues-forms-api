<?php

namespace App\Http\Controllers\Seguridad;

use App\Enums\EstadosEnum;
use App\Enums\GeneralEnum;
use App\Http\Controllers\Controller;
use App\Models\Registro\Persona;
use App\Models\Seguridad\User;
use Illuminate\Http\Request;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\PaginationTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;

class UsuarioController extends Controller
{
    use ResponseTrait, PaginationTrait;

    public function index(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre_usuario' => 'string|max:50|regex:/^[a-zA-Z0-9_]+$/',
                'id_estado' => 'integer|exists:ctl_estados,id',
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
            // Obtener los usuarios con su persona y estado (columnas especificadas en el modelo)
            $query = User::select('id', 'username', 'email', 'id_persona', 'id_estado')->with(['persona:id,nombre,apellido', 'estado:id,nombre']);
            // Aplicar filtros
            if (isset($validatedData['nombre_usuario'])) {
                // Buscar por username o nombre de persona
                $query->where(function ($q) use ($validatedData) {
                    $q->where('username', 'ilike', '%' . $validatedData['nombre_usuario'] . '%')
                        ->orWhereHas('persona', function ($q) use ($validatedData) {
                            $q->where('nombre', 'ilike', '%' . $validatedData['nombre_usuario'] . '%')
                                ->orWhere('apellido', 'ilike', '%' . $validatedData['nombre_usuario'] . '%');
                        });
                });
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
                // User
                'username' => 'required|string|max:50|unique:users,username|regex:/^[a-zA-Z0-9_]+$/',
                'email' => 'required|string|email|max:50|unique:users,email',
                'password' => 'required|string|min:8|max:50',
                'id_estado' => [
                    'required',
                    Rule::in(EstadosEnum::usuarios())
                ],
                // Persona
                'nombre' => 'string|max:50|regex:/^[a-zA-Z\s]+$/',
                'apellido' => 'string|max:50|regex:/^[a-zA-Z\s]+$/',
                'identificacion' => 'string|max:20|regex:/^[a-zA-Z0-9]+$/',
                'activo' => 'boolean',
                // Roles
                'roles' => 'array',
                'roles.*' => 'string|exists:roles,name',
            ], [
                'username.regex' => 'El nombre/usuario solo puede contener letras, números y guiones bajos',
                'username.max' => 'El nombre/usuario no puede exceder los 50 caracteres',
                'username.unique' => 'El nombre/usuario ya existe en la base de datos',
                'email.required' => 'El correo electrónico es obligatorio',
                'email.email' => 'El correo electrónico no es válido',
                'email.max' => 'El correo electrónico no puede exceder los 50 caracteres',
                'email.unique' => 'El correo electrónico ya existe en la base de datos',
                'password.required' => 'La contraseña es obligatoria',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres',
                'password.max' => 'La contraseña no puede exceder los 50 caracteres',
                'nombre.regex' => 'Los nombres solo pueden contener letras y espacios',
                'nombre.max' => 'Los nombres no pueden exceder los 50 caracteres',
                'apellido.regex' => 'Los apellidos solo pueden contener letras y espacios',
                'apellido.max' => 'Los apellidos no pueden exceder los 50 caracteres',
                'identificacion.regex' => 'La identificación solo puede contener letras y números',
                'identificacion.max' => 'La identificación no puede exceder los 20 caracteres',
                'activo.boolean' => 'El campo activo debe ser verdadero o falso',
                'roles.array' => 'Los roles deben ser un arreglo',
                'roles.*.string' => 'Los roles deben ser cadenas de texto',
                'roles.*.exists' => 'Uno o más de los roles seleccionados no son válidos',
                'id_estado.required' => 'Debe seleccionar los roles a asignar',
                'id_estado.in' => 'El estado de usuario seleccionado no es válido',
            ]);

            if ($validator->fails()) {
                return $this->validationError('Ocurrió un error de validación', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validatedData = $validator->validated();

            DB::beginTransaction();

            $persona = Persona::create([
                'nombre' => $validatedData['nombre'],
                'apellido' => $validatedData['apellido'],
                'identificacion' => $validatedData['identificacion'],
                'activo' => true,
            ]);

            $usuario = User::create([
                'username' => $validatedData['username'],
                'email' => $validatedData['email'],
                'password' => bcrypt($validatedData['password']),
                'id_persona' => $persona->id,
                'id_estado' => $validatedData['id_estado']
            ]);

            if (isset($validatedData['roles'])) {
                $usuario->assignRole($validatedData['roles']);
            }

            DB::commit();

            return $this->success('Usuario creado exitosamente', $usuario, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Error al crear el usuario', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $usuario = User::with(['persona:id,nombre,apellido', 'estado:id,nombre'])->find($id);
            if (!$usuario) {
                return $this->error('Usuario no encontrado', 'No se encontró el usuario con el ID proporcionado', Response::HTTP_NOT_FOUND);
            }
            $usuario->getRoleNames();
            return $this->success('Usuario obtenido exitosamente', $usuario, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->error('Error al obtener el usuario', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'string|max:50|unique:users,username,' . $id . '|regex:/^[a-zA-Z0-9_]+$/',
                'email' => 'string|email|max:50|unique:users,email,' . $id,
                'password' => 'string|min:8|max:50',
                'nombre' => 'string|max:50|regex:/^[a-zA-Z\s]+$/',
                'apellido' => 'string|max:50|regex:/^[a-zA-Z\s]+$/',
                'identificacion' => 'string|max:20|regex:/^[a-zA-Z0-9]+$/',
                'activo' => 'boolean',
                'id_estado' => Rule::in(EstadosEnum::usuarios()),
                'roles' => 'array',
                'roles.*' => 'string|exists:roles,name',
            ], [
                'username.regex' => 'El nombre/usuario solo puede contener letras, números y guiones bajos',
                'username.max' => 'El nombre/usuario no puede exceder los 50 caracteres',
                'username.unique' => 'El nombre/usuario ya existe en la base de datos',
                'email.required' => 'El correo electrónico es obligatorio',
                'email.email' => 'El correo electrónico no es válido',
                'email.max' => 'El correo electrónico no puede exceder los 50 caracteres',
                'email.unique' => 'El correo electrónico ya existe en la base de datos',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres',
                'password.max' => 'La contraseña no puede exceder los 50 caracteres',
                'nombre.regex' => 'Los nombres solo pueden contener letras y espacios',
                'nombre.max' => 'Los nombres no pueden exceder los 50 caracteres',
                'apellido.regex' => 'Los apellidos solo pueden contener letras y espacios',
                'apellido.max' => 'Los apellidos no pueden exceder los 50 caracteres',
                'identificacion.regex' => 'La identificación solo puede contener letras y números',
                'identificacion.max' => 'La identificación no puede exceder los 20 caracteres',
                'activo.boolean' => 'El campo activo debe ser verdadero o falso',
                'roles.array' => 'Los roles deben ser un arreglo',
                'roles.*.string' => 'Los roles deben ser cadenas de texto',
                'roles.*.exists' => 'Uno o más de los roles seleccionados no son válidos',
                'id_estado.in' => 'El estado de usuario seleccionado no es válido',
            ]);
            if ($validator->fails()) {
                return $this->validationError('Ocurrió un error de validación', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }
            $validatedData = $validator->validated();
            $usuario = User::find($id);
            if (!$usuario) {
                return $this->error('Usuario no encontrado', 'No se encontró el usuario con el ID proporcionado', Response::HTTP_NOT_FOUND);
            }
            DB::beginTransaction();
            if (isset($validatedData['nombre'])) {
                $usuario->persona->nombre = $validatedData['nombre'];
            }
            if (isset($validatedData['apellido'])) {
                $usuario->persona->apellido = $validatedData['apellido'];
            }
            if (isset($validatedData['identificacion'])) {
                $usuario->persona->identificacion = $validatedData['identificacion'];
            }
            if (isset($validatedData['username'])) {
                $usuario->username = $validatedData['username'];
            }
            if (isset($validatedData['email'])) {
                $usuario->email = $validatedData['email'];
            }
            if (isset($validatedData['password'])) {
                $usuario->password = bcrypt($validatedData['password']);
            }
            if (isset($validatedData['id_estado'])) {
                $usuario->id_estado = $validatedData['id_estado'];
            }
            if (isset($validatedData['activo'])) {
                $usuario->activo = $validatedData['activo'];
            }
            if (isset($validatedData['roles'])) {
                $usuario->syncRoles($validatedData['roles']);
            }
            $usuario->persona->save();
            $usuario->save();
            DB::commit();
            return $this->success('Usuario actualizado exitosamente', $usuario, Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Error al actualizar el usuario', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $usuario = User::find($id);
            if (!$usuario) {
                return $this->error('Usuario no encontrado', 'No se encontró el usuario con el ID proporcionado', Response::HTTP_NOT_FOUND);
            }
            $usuario->delete();
            return $this->success('Usuario eliminado exitosamente', null, Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Error al eliminar el usuario', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function updateByAdmin(Request $request, $id)
    {
        
    }
}
