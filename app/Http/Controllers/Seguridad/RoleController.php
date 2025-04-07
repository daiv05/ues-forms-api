<?php

namespace App\Http\Controllers\Seguridad;

use App\Enums\GeneralEnum;
use App\Http\Controllers\Controller;
use BladeUI\Icons\Factory;
use Illuminate\Http\Request;
use Spatie\Permission\Models\Permission;
use Spatie\Permission\Models\Role;
use Illuminate\View\View;
use Illuminate\Http\RedirectResponse;

class RoleController extends Controller
{

    public function index(): View
    {

        $roles = Role::paginate(GeneralEnum::PAGINACION->value);
        // Retornar la vista y pasar los datos
        return view('seguridad.roles.index', compact('roles'));
    }
    public function create():Factory|View
    {
        $permissions = Permission::all();

        return view('seguridad.roles.create', compact('permissions'));
    }
    public function store(Request $request): RedirectResponse
    {
        $request->validate([
            'name' => 'required|unique:roles,name',
            'permissions' => 'nullable|string',
        ], [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.unique' => 'Ya existe un rol con ese nombre.',
            'permissions.string' => 'Los permisos deben ser una lista de IDs separados por comas.'
        ]);
              $role = Role::create([
            'name' => $request->name,
        ]);
        $permissions = is_string($request->permissions)
            ? explode(',', $request->permissions)
            : $request->permissions;
        if (empty($permissions)) {
            $validPermissions = [];
        } else {
            $validPermissions = Permission::whereIn('id', $permissions)->pluck('id')->toArray();
        }
        $role->syncPermissions($validPermissions);
        return redirect()->route('roles.index')->with('message', [
            'type' => 'success',
            'content' => 'Rol creado correctamente.',
        ]);
    }
    public function edit(string $id):Factory|View
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = Permission::all();
        return view('seguridad.roles.edit', compact('role', 'permissions'));
    }
    public function update(Request $request, string $id): RedirectResponse
    {
        $request->validate([
            'name' => 'required|unique:roles,name,' . $id,
            'permissions' => 'nullable|string',
        ], [
            'name.required' => 'El nombre del rol es obligatorio.',
            'name.unique' => 'Ya existe un rol con ese nombre.',
            'permissions.string' => 'Los permisos deben ser una lista de IDs separados por comas.'
        ]);
        $role = Role::findOrFail($id);
        $permissions = is_string($request->permissions)
            ? explode(',', $request->permissions)
            : $request->permissions;

        if (empty($permissions)) {
            $validPermissions = [];
        } else {
            $validPermissions = Permission::whereIn('id', $permissions)->pluck('id')->toArray();
        }
        $role->syncPermissions($validPermissions);
        return redirect()->route('roles.index')->with('message', [
            'type' => 'success',
            'content' => 'Rol actualizado correctamente.',
        ]);
    }

    public function show(string $id)
    {
        $role = Role::with('permissions')->findOrFail($id);
        $permissions = $role->permissions()->paginate(GeneralEnum::PAGINACION->value/2);
        return view('seguridad.roles.show', compact('role', 'permissions'));
    }

}
