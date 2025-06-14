<?php

namespace App\Http\Controllers\Seguridad\Auth;

use App\Enums\EstadosEnum;
use App\Enums\GeneralEnum;
use App\Http\Controllers\Controller;
use App\Mail\RegistrationRequestResponseMail;
use App\Models\Registro\Persona;
use App\Models\Seguridad\AuthVerifiedEmail;
use App\Models\Seguridad\SolicitudRegistro;
use App\Models\Seguridad\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ResponseTrait;
use App\Traits\PaginationTrait;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;
use Illuminate\Validation\Rule;

class AuthRegistrationController extends Controller
{
    use ResponseTrait, PaginationTrait;

    public function index(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'nombre_usuario' => 'string|max:50|regex:/^[a-zA-Z\sáéíóúñÁÉÍÓÚÑ]+$/',
                'id_estado' => 'integer|exists:ctl_estados,id',
            ], [
                'nombre_usuario.regex' => 'Caracteres no válidos en el nombre/usuario',
                'nombre_usuario.max' => 'El nombre/usuario no puede exceder los 50 caracteres',
                'id_estado.exists' => 'El estado debe existir en la base de datos',
                'id_estado.integer' => 'El estado debe ser un número entero'
            ]);

            if ($validator->fails()) {
                return $this->validationError('Ocurrió un error de validación', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validatedData = $validator->validated();
            // Obtener las solicitudes de registro con sus usuarios y personas
            $query = SolicitudRegistro::with(['usuario', 'usuario.persona', 'estado']);
            // Aplicar filtros
            if (isset($validatedData['nombre_usuario'])) {
                // Buscar por username o nombre de persona
                $query->where(function ($q) use ($validatedData) {
                    $q->whereHas('usuario', function ($q) use ($validatedData) {
                        $q->where('username', 'ilike', '%' . $validatedData['nombre_usuario'] . '%');
                    })
                        ->orWhereHas('usuario.persona', function ($q) use ($validatedData) {
                            $q->where('nombre', 'ilike', '%' . $validatedData['nombre_usuario'] . '%')
                                ->orWhere('apellido', 'ilike', '%' . $validatedData['nombre_usuario'] . '%');
                        });
                });
            }
            if (isset($validatedData['id_estado'])) {
                $query->where('id_estado', $validatedData['id_estado']);
            }
            $solicitudes = $query->get();

            $solicitudes->each(function ($solicitud) {
                $solicitud->usuario->persona->makeHidden(['updated_at']);
                $solicitud->usuario->makeHidden(['password', 'remember_token', 'updated_at']);
            });

            if ($request['paginate'] === "true") {
                $paginatedData = $this->paginate($solicitudes->toArray(), $request['per_page'] ?? GeneralEnum::PAGINACION->value, $request['page'] ?? 1);
                return $this->successPaginated('Solicitudes obtenidas exitosamente', $paginatedData, Response::HTTP_OK);
            } else {
                return $this->success('Solicitudes obtenidas exitosamente', $solicitudes, Response::HTTP_OK);
            }
        } catch (\Exception $e) {
            return $this->error('Error al obtener las solicitudes de registro', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function requestRegistration(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'username' => 'required|string|max:50|unique:users,username|regex:/^[a-zA-Z0-9_\-]+$/',
                'email' => 'required|string|email|max:50|unique:users,email',
                'password' => 'required|string|min:8|max:50',
                'nombre' => 'string|max:50|regex:/^[a-zA-Z\sáéíóúñÁÉÍÓÚÑ]+$/',
                'apellido' => 'string|max:50|regex:/^[a-zA-Z\sáéíóúñÁÉÍÓÚÑ]+$/',
                'identificacion' => 'string|max:20|regex:/^[a-zA-Z0-9\-]+$/',
                'justificacion_solicitud' => 'string|max:255|regex:/^[a-zA-Z0-9\sáéíóúñÁÉÍÓÚÑ.,;:()\-]+$/',
            ], [
                'username.regex' => 'El usuario solo puede contener letras, números y guiones',
                'username.max' => 'El usuario no puede exceder los 50 caracteres',
                'username.unique' => 'Ya existe un usuario registrado con este /usuario',
                'email.required' => 'El correo electrónico es obligatorio',
                'email.email' => 'El correo electrónico no es válido',
                'email.max' => 'El correo electrónico no puede exceder los 50 caracteres',
                'email.unique' => 'Ya existe un usuario registrado con este correo electrónico',
                'password.required' => 'La contraseña es obligatoria',
                'password.min' => 'La contraseña debe tener al menos 8 caracteres',
                'password.max' => 'La contraseña no puede exceder los 50 caracteres',
                'nombre.regex' => 'Caracteres no válidos en el nombre',
                'nombre.max' => 'Los nombres no pueden exceder los 50 caracteres',
                'apellido.regex' => 'Caracteres no válidos en el apellido',
                'apellido.max' => 'Los apellidos no pueden exceder los 50 caracteres',
                'identificacion.regex' => 'La identificación solo puede contener letras, números y guiones',
                'identificacion.max' => 'La identificación no puede exceder los 20 caracteres',
                'justificacion_solicitud.max' => 'La justificación de la solicitud no puede exceder los 255 caracteres',
                'justificacion_solicitud.string' => 'La justificación de la solicitud debe ser una cadena de texto',
                'justificacion_solicitud.regex' => 'Caracteres no válidos en la justificación de la solicitud',
            ]);

            if ($validator->fails()) {
                return $this->validationError('Ocurrió un error de validación', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validatedData = $validator->validated();

            // Verificar si el correo electrónico ya está verificado
            $verifiedEmail = AuthVerifiedEmail::where('email', $validatedData['email'])->first();

            if (!$verifiedEmail) {
                return $this->error('Correo electrónico no verificado', 'El correo electrónico no está verificado', Response::HTTP_BAD_REQUEST);
            }

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
                'id_estado' => EstadosEnum::INACTIVO->value,
                'activo' => false
            ]);

            $usuario->markEmailAsVerified();

            $solicitud = SolicitudRegistro::create([
                'justificacion_solicitud' => $validatedData['justificacion_solicitud'],
                'id_usuario' => $usuario->id,
                'id_estado' => EstadosEnum::PENDIENTE->value,
            ]);

            DB::commit();

            return $this->success('Solicitud registrada', $solicitud, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Error al solicitar registro', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $solicitud = SolicitudRegistro::with(['usuario', 'usuario.persona', 'estado'])->findOrFail($id);
            $solicitud->usuario->persona->makeHidden(['updated_at']);
            $solicitud->usuario->makeHidden(['password', 'remember_token', 'updated_at']);

            return $this->success('Solicitud obtenida exitosamente', $solicitud, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->error('Error al obtener la solicitud de registro', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update($id, Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_estado' => [
                    'required',
                    'integer',
                    Rule::in(EstadosEnum::solicitudes()),
                ],
                'justificacion_rechazo' => 'string|max:255|regex:/^[a-zA-Z0-9\sáéíóúñÁÉÍÓÚÑ.,;:()\-]+$/',
            ], [
                'id_estado.required' => 'El estado es obligatorio',
                'id_estado.integer' => 'El estado debe ser un número entero',
                'id_estado.in' => 'El estado no es válido',
                'justificacion_rechazo.regex' => 'Caracteres no válidos en la justificación de rechazo',
                'justificacion_rechazo.max' => 'La justificación de rechazo no puede exceder los 255 caracteres',
            ]);

            if ($request['id_estado'] === EstadosEnum::RECHAZADO->value) {
                if (empty($request['justificacion_rechazo'])) {
                    $this->validationError('La justificación de rechazo es obligatoria', [], Response::HTTP_UNPROCESSABLE_ENTITY);
                }
            }

            if ($validator->fails()) {
                return $this->validationError('Ocurrió un error de validación', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validatedData = $validator->validated();

            DB::beginTransaction();

            $solicitud = SolicitudRegistro::findOrFail($id);

            // Verificar si la solicitud ya fue aprobada o rechazada
            if ($solicitud->id_estado !== EstadosEnum::PENDIENTE->value) {
                return $this->error('Solicitud ya procesada', 'La solicitud ya fue aprobada o rechazada', Response::HTTP_BAD_REQUEST);
            }

            $aprobado = $validatedData['id_estado'] === EstadosEnum::APROBADO->value;

            $usuario = User::findOrFail($solicitud->id_usuario);

            if ($aprobado) {
                // Aprobar solicitud
                $solicitud->update([
                    'id_estado' => $validatedData['id_estado'],
                ]);
                // Activar usuario
                $usuario->update(['activo' => true, 'id_estado' => EstadosEnum::ACTIVO->value]);
                // Asignar rol por defecto
                $usuario->assignRole('ENCUESTADOR');
                // Enviar correo de aprobación
                Mail::to($usuario->email)->send(new RegistrationRequestResponseMail([
                    'approved' => true,
                    'reason' => null,
                ]));
            } else {
                // Rechazar solicitud
                $solicitud->update([
                    'id_estado' => $validatedData['id_estado'],
                    'justificacion_rechazo' => $validatedData['justificacion_rechazo'] ?? null,
                ]);
                // Enviar correo de rechazo
                Mail::to($usuario->email)->send(new RegistrationRequestResponseMail([
                    'approved' => false,
                    'reason' => $validatedData['justificacion_rechazo'] ?? null,
                ]));
            }

            DB::commit();

            return $this->success('Solicitud actualizada exitosamente', $solicitud, Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Error al actualizar la solicitud de registro', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
