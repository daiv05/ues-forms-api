<?php

namespace App\Http\Controllers\Seguridad;

use App\Enums\GeneralEnum;
use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use App\Traits\ResponseTrait;
use Symfony\Component\HttpFoundation\Response;
use Illuminate\Auth\Events\Validated;
use Illuminate\Support\Facades\Validator;
use App\Traits\PaginationTrait;

class RoleController extends Controller
{
    use ResponseTrait, PaginationTrait;

    public function index(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'string|max:50|regex:/^[a-zA-Z0-9_]+$/',
                'estado' => 'integer|in:0,1',
            ], [
                'name.regex' => 'El nombre solo puede contener letras, números y guiones bajos',
                'name.max' => 'El nombre no puede exceder los 50 caracteres',
                'estado.in' => 'El estado debe ser 0 o 1'
            ]);

            if ($validator->fails()) {
                return $this->validationError('Ocurrió un error de validación', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validatedData = $validator->validated();

            $roles = Role::with('permissions')->when(isset($validatedData['name']), function ($query) use ($validatedData) {
                $query->where('name', 'like', '%' . $validatedData['name'] . '%');
            })->when(isset($validatedData['estado']), function ($query) use ($validatedData) {
                $query->where('activo', $validatedData['estado']);
            })->get();

            if ($request['paginate'] === "true") {
                $paginatedData = $this->paginate($roles->toArray(), $request['per_page'] ?? GeneralEnum::PAGINACION->value, $request['page'] ?? 1);
                return $this->successPaginated('Roles obtenidos exitosamente', $paginatedData, Response::HTTP_OK);
            } else {
                return $this->success('Roles obtenidos exitosamente', $roles, Response::HTTP_OK);
            }

            return $this->success('Roles obtenidos exitosamente', $roles, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->error('Error al obtener los roles', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function store(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50|unique:roles,name|regex:/^[a-zA-Z0-9_]+$/',
                'description' => 'string|max:255',
                'activo' => 'boolean',
                'permissions' => 'array',
                'permissions.*' => 'string|exists:permissions,name',
            ], [
                'name.regex' => 'El nombre solo puede contener letras, números y guiones bajos',
                'name.unique' => 'El nombre del rol ya existe',
                'description.max' => 'La descripción no puede exceder los 255 caracteres',
                'activo.boolean' => 'El campo activo debe ser verdadero o falso',
                'permissions.array' => 'Los permisos deben ser un arreglo',
                'permissions.*.string' => 'Los permisos deben ser cadenas de texto',
                'permissions.*.exists' => 'Uno o más de los permisos seleccionados no son válidos',
            ]);

            if ($validator->fails()) {
                return $this->validationError('Ocurrió un error de validación', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validatedData = $validator->validated();

            $role = Role::create(['name' => $validatedData['name']]);

            if (isset($validatedData['permissions'])) {
                $role->syncPermissions($validatedData['permissions']);
            }

            return $this->success('Rol creado exitosamente', $role, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            return $this->error('Error al crear el rol', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'name' => 'required|string|max:50|unique:roles,name,' . $id . '|regex:/^[a-zA-Z0-9_]+$/',
                'description' => 'string|max:255',
                'activo' => 'integer|in:0,1',
                'permissions' => 'array',
                'permissions.*' => 'string|exists:permissions,name',
            ], [
                'name.regex' => 'El nombre solo puede contener letras, números y guiones bajos',
                'name.unique' => 'El nombre del rol ya existe',
                'description.max' => 'La descripción no puede exceder los 255 caracteres',
                'activo.integer' => 'El campo activo debe ser un número entero',
                'activo.in' => 'El campo activo debe ser 0 o 1',
                'permissions.array' => 'Los permisos deben ser un arreglo',
                'permissions.*.string' => 'Los permisos deben ser cadenas de texto',
                'permissions.*.exists' => 'Uno o más de los permisos seleccionados no son válidos',
            ]);

            if ($validator->fails()) {
                return $this->validationError('Ocurrió un error de validación', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validatedData = $validator->validated();

            $role = Role::findOrFail($id);
            $role->name = $validatedData['name'];
            $role->save();

            if (isset($validatedData['permissions'])) {
                $role->syncPermissions($validatedData['permissions']);
            }

            return $this->success('Rol actualizado exitosamente', $role, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->error('Error al actualizar el rol', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $role = Role::with('permissions')->findOrFail($id);
            return $this->success('Rol obtenido exitosamente', $role, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->error('Error al obtener el rol', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function destroy($id)
    {
        try {
            $role = Role::findOrFail($id);
            $role->delete();

            return $this->success('Rol eliminado exitosamente', null, Response::HTTP_NO_CONTENT);
        } catch (\Exception $e) {
            return $this->error('Error al eliminar el rol', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
