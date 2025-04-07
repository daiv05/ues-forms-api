<?php

namespace App\Http\Controllers\Seguridad;

use App\Enums\GeneralEnum;
use App\Http\Controllers\Controller;
use App\Models\Mantenimientos\Escuela;
use App\Models\Registro\Persona;
use App\Models\rhu\EmpleadoPuesto;
use App\Models\rhu\Puesto;
use App\Models\Seguridad\User;
use Illuminate\Http\Request;
use Illuminate\View\View;
use Spatie\Permission\Models\Role;
use Illuminate\Support\Facades\DB;
use Illuminate\Validation\Rule;
use OwenIt\Auditing\Models\Audit;


class UsuarioController extends Controller
{
    public function index(Request $request): View
    {
        $nombreFilter = $request->get('nombre-filter');
        $emailFilter = $request->get('email-filter');
        $rolFilter = $request->get('role-filter');

        $usuarios = User::with('roles', 'persona')
            ->when($nombreFilter, function ($query, $nombreFilter) {
                return $query->whereHas('persona', function ($query) use ($nombreFilter) {
                    $query->whereRaw("CONCAT(nombre, ' ', apellido) LIKE ?", ["%{$nombreFilter}%"]);
                });
            })
            ->when($emailFilter, function ($query, $emailFilter) {
                return $query->where('email', 'LIKE', "%{$emailFilter}%");
            })
            ->when($rolFilter, function ($query, $rolFilter) {
                return $query->whereHas('roles', function ($query) use ($rolFilter) {
                    $query->where('id', '=', $rolFilter);
                });
            })
            ->paginate(GeneralEnum::PAGINACION->value)->appends($request->query());

        $roles = Role::all();

        $roles = $roles->pluck('name', 'id');
        return view('seguridad.usuarios.index', compact('usuarios', 'roles'));
    }
    public function store(Request $request)
    {

        $rules = [
            'nombre' => 'required|string|max:255',
            'apellido' => 'required|string|max:255',
            'fecha_nacimiento' => 'required|date_format:d/m/Y',
            'telefono' => 'required|string|max:15',
            'email' => 'required|string|email|max:255|unique:users',
            'carnet' => 'required|string|max:20|unique:users',
            'tipo_user' => 'required|boolean', // Validar que el tipo de usuario sea 1 o 2
            'roles' => 'nullable|string', // Roles como cadena separada por comas
        ];

        $messages = [
            'tipo_user.boolean' => 'El tipo de usuario debe ser un valor booleano.',
            'fecha_nacimiento.date_format' => 'El campo fecha de nacimiento no tiene un formato válido.',
            'fecha_nacimiento.required' => 'El campo fecha de nacimiento es obligatorio.',
        ];
        $tipo = $request->input('tipo_user');

        if ($tipo == '1') {
            $rules['escuela'] = 'required|exists:escuelas,id';
            $rules['email'] = 'required|string|email|max:255|unique:users|regex:/^[a-zA-Z0-9._%+-]+@ues\.edu\.sv$/'; // Validar que el correo sea institucional
            $messages['email.regex'] = 'El correo electrónico debe ser institucional (@ues.edu.sv).';
        } else {
            $rules['puesto'] = 'required|exists:puestos,id'; // Validar que puesto existe en la tabla puestos
        }
        $request->validate($rules);

        $request->merge([
            'fecha_nacimiento' => \Carbon\Carbon::createFromFormat('d/m/Y', $request->input('fecha_nacimiento'))->format('Y-m-d')
        ]);

        try {
            DB::beginTransaction();

            $persona = Persona::create([
                'nombre' => $request->nombre,
                'apellido' => $request->apellido,
                'fecha_nacimiento' => $request->fecha_nacimiento,
                'telefono' => $request->telefono,
            ]);
            $usuario = User::create([
                'email' => $request->input('email'),
                'carnet' => $request->input('carnet'),
                'activo' => $request->has('activo'),
                'password' => bcrypt('password123'),
                'id_persona' => $persona->id,
            ]);
            if ($tipo == '1') {
                $usuario->id_escuela = $request->input('escuela');
                $usuario->es_estudiante = true;
                $usuario->save();

                $usuario->assignRole('USUARIO');
            } else {
                $usuario->es_estudiante = false;
                $usuario->save();
                EmpleadoPuesto::create([
                    'id_usuario' => $usuario->id,
                    'id_puesto' => $request->input('puesto'),
                ]);
            }
            // Si hay roles, convertir la cadena de IDs a nombres de roles
            if ($request->filled('roles')) {
                $roles = Role::whereIn('id', explode(',', $request->roles))->pluck('name')->toArray();
                $usuario->syncRoles($roles);
            } else {
                $usuario->assignRole('USUARIO');
            }
            DB::commit();
        } catch (\Exception $e) {
            DB::rollBack();
            return redirect()->back()->with('message', [
                'type' => 'danger',
                'content' => 'Ocurrió un error al crear el usuario. Por favor, inténtelo de nuevo.',
            ]);
        }

        // Redireccionar con mensaje de éxito
        return redirect()->route('usuarios.index')->with('message', [
            'type' => 'success',
            'content' => 'Usuario creado y roles asignados correctamente.',
        ]);
    }
    public function update(Request $request, $id)
    {
        $user = User::findOrFail($id);
        $rules = [
            'nombre' => 'required|string',
            'apellido' => 'required|string',
            'email' => ['required', 'email', 'max:255', Rule::unique('users', 'email')->ignore($user->id), ],
            'carnet' => 'required|string|max:20',
            'roles' => 'nullable|string', // Validar los roles (cadena separada por comas)
        ];
        if ($user->es_estudiante) {
            $rules['escuela'] = 'required|exists:escuelas,id';
        }
        $request->validate($rules);
        $user->update([
            'email' => $request->email,
            'carnet' => $request->carnet,
            'activo' => $request->has('activo'),
        ]);

        if ($user->es_estudiante) {
            $user->id_escuela = $request->escuela;
            $user->save();
        }
        $persona = Persona::findOrFail($user->id_persona);
        $persona->update([
            'nombre' => $request->nombre,
            'apellido' => $request->apellido
        ]);
        $currentRoles = $user->roles->pluck('name')->toArray();
        $newRoles = Role::whereIn('id', explode(',', $request->roles))->pluck('name')->toArray();
        $user->syncRoles($newRoles);
        Audit::create([
            'user_id' => auth()->id(),
            'event' => 'Actualizar_roles',
            'auditable_type' => 'App\Models\Seguridad\User',
            'auditable_id' => $user->id,  //
            'old_values' =>  $currentRoles,
            'new_values' => $newRoles,
            'url' => request()->url(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);
        return redirect()->route('usuarios.index')->with('message', [
            'type' => 'success',
            'content' => 'Usuario actualizado y roles asignados correctamente.',
        ]);
    }

    public function create(Request $request): View
    {

        $roles = Role::all();
        $escuelas = Escuela::all()->pluck('nombre', 'id');
        $out = new \Symfony\Component\Console\Output\ConsoleOutput();
        $idEntidad = $request->input('entidad');

        $entidades = [];
        $entidadesBackup = \App\Models\rhu\Entidades::all();
        foreach ($entidadesBackup as $entidad) {
            $entidades[$entidad->id] = $entidad->nombre;
        }
        $puestos = Puesto::all()->groupBy('id_entidad')->map(function ($puestos) {
            return $puestos->pluck('nombre', 'id');
        });

        return view('seguridad.usuarios.create', compact('roles', 'entidades', 'puestos', 'escuelas'));
    }
    public function show(string $id)
    {

        $user = User::with('empleadosPuestos.puesto.entidad', 'empleadosPuestos.empleadosAcciones.reporte')->findOrFail($id);
        return view('seguridad.usuarios.show', compact('user'));
    }
    public function edit(string $id)
    {
        $user = User::with('empleadosPuestos')->findOrFail($id);
        $escuelas = Escuela::all()->pluck('nombre', 'id');

        // Obtener todos los roles disponibles
        $roles = Role::all();

        return view('seguridad.usuarios.edit', compact('user', 'roles', 'escuelas'));
    }
}
