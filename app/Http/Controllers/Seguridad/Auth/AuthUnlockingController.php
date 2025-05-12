<?php

namespace App\Http\Controllers\Seguridad\Auth;

use App\Enums\EstadosEnum;
use App\Enums\GeneralEnum;
use App\Http\Controllers\Controller;
use App\Mail\UnlockRequestResponseMail;
use App\Models\Seguridad\SolicitudDesbloqueo;
use App\Models\Seguridad\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ResponseTrait;
use App\Traits\PaginationTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;
use Illuminate\Support\Facades\Mail;

class AuthUnlockingController extends Controller
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
            // Obtener las solicitudes de desbloqueo con sus usuarios y personas
            $query = SolicitudDesbloqueo::with(['usuario', 'usuario.persona', 'estado']);
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
            return $this->error('Error al obtener las solicitudes de desbloqueo', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function requestUnlocking(Request $request)
    {
        try {
            $validator = Validator::make($request->all(), [
                'justificacion_solicitud' => 'string|max:255',
            ], [
                'justificacion_solicitud.max' => 'La justificación de la solicitud no puede exceder los 255 caracteres',
                'justificacion_solicitud.string' => 'La justificación de la solicitud debe ser una cadena de texto',
            ]);

            if ($validator->fails()) {
                return $this->validationError('Ocurrió un error de validación', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
            }

            $validatedData = $validator->validated();

            DB::beginTransaction();

            $usuario = Auth::user();

            if (!$usuario) {
                return $this->error('Usuario no encontrado', 'No se encontró el usuario', Response::HTTP_NOT_FOUND);
            }

            if ($usuario->id_estado !== EstadosEnum::BLOQUEADO->value) {
                return $this->error('Estado de usuario no válido', 'El usuario no está bloqueado', Response::HTTP_BAD_REQUEST);
            }

            // Verificar si ya existe una solicitud de desbloqueo pendiente
            $existingRequest = SolicitudDesbloqueo::where('id_usuario', $usuario->id)
                ->where('id_estado', EstadosEnum::PENDIENTE->value)
                ->first();
            if ($existingRequest) {
                return $this->error('Solicitud ya existente', 'Ya tienes una solicitud de desbloqueo pendiente', Response::HTTP_BAD_REQUEST);
            }

            $solicitud = SolicitudDesbloqueo::create([
                'justificacion_solicitud' => $validatedData['justificacion_solicitud'],
                'id_usuario' => $usuario->id,
                'id_estado' => EstadosEnum::PENDIENTE->value,
            ]);

            DB::commit();

            return $this->success('Solicitud generada', $solicitud, Response::HTTP_CREATED);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Error al solicitar desbloqueo', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function show($id)
    {
        try {
            $solicitud = SolicitudDesbloqueo::with(['usuario', 'usuario.persona', 'estado'])->find($id);

            if (!$solicitud) {
                return $this->error('Solicitud no encontrada', 'No se encontró la solicitud de desbloqueo', Response::HTTP_NOT_FOUND);
            }

            $solicitud->usuario->persona->makeHidden(['updated_at']);
            $solicitud->usuario->makeHidden(['password', 'remember_token', 'updated_at']);

            return $this->success('Solicitud obtenida exitosamente', $solicitud, Response::HTTP_OK);
        } catch (\Exception $e) {
            return $this->error('Error al obtener la solicitud de desbloqueo', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }

    public function update(Request $request, $id)
    {
        try {
            $validator = Validator::make($request->all(), [
                'id_estado' => [
                    'required',
                    'integer',
                    'exists:ctl_estados,id',
                    'in:' . EstadosEnum::APROBADO->value . ',' . EstadosEnum::RECHAZADO->value,
                ],
                'justificacion_rechazo' => 'string|max:255|regex:/^[a-zA-Z0-9\sáéíóúñÁÉÍÓÚÑ.,;:()\-]+$/',
            ], [
                'id_estado.required' => 'El estado es requerido',
                'id_estado.exists' => 'El estado debe existir en la base de datos',
                'id_estado.integer' => 'El estado debe ser un número entero',
                'id_estado.in' => 'El estado seleccionado no es válido',
                'justificacion_rechazo.regex' => 'Caracteres no válidos en la justificación de la respuesta',
                'justificacion_rechazo.max' => 'La justificación de la respuesta no puede exceder los 255 caracteres',
                'justificacion_rechazo.string' => 'La justificación de la respuesta debe ser una cadena de texto',
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

            // Obtener la solicitud de desbloqueo
            $solicitud = SolicitudDesbloqueo::with(['usuario'])->find($id);

            if (!$solicitud) {
                return $this->error('Solicitud no encontrada', 'No se encontró la solicitud de desbloqueo', Response::HTTP_NOT_FOUND);
            }

            // Verificar si el estado es válido para la actualización
            if ($solicitud->id_estado !== EstadosEnum::PENDIENTE->value) {
                return $this->error('Estado no válido', 'La solicitud ya ha sido procesada', Response::HTTP_BAD_REQUEST);
            }

            $usuario = User::find($solicitud->id_usuario);
            if (!$usuario) {
                return $this->error('Usuario no encontrado', 'No se encontró el usuario asociado a la solicitud', Response::HTTP_NOT_FOUND);
            }

            // Verificar si el usuario está bloqueado
            if ($usuario->id_estado !== EstadosEnum::BLOQUEADO->value) {
                return $this->error('Estado de usuario no válido', 'El usuario no está bloqueado', Response::HTTP_BAD_REQUEST);
            }

            $aprobado = $validatedData['id_estado'] === EstadosEnum::APROBADO->value;

            if ($aprobado) {
                $solicitud->update([
                    'id_estado' => $validatedData['id_estado'],
                ]);
                $usuario->update(['id_estado' => EstadosEnum::ACTIVO->value]);
                Mail::to($usuario->email)->send(new UnlockRequestResponseMail([
                    'approved' => true,
                    'reason' => null,
                ]));
            } else {
                $solicitud->update([
                    'id_estado' => $validatedData['id_estado'],
                    'justificacion_rechazo' => $validatedData['justificacion_rechazo'] ?? null,
                ]);
                Mail::to($usuario->email)->send(new UnlockRequestResponseMail([
                    'approved' => false,
                    'reason' => $validatedData['justificacion_rechazo'] ?? null,
                ]));
            }

            DB::commit();

            return $this->success('Solicitud actualizada exitosamente', $solicitud, Response::HTTP_OK);
        } catch (\Exception $e) {
            DB::rollBack();
            return $this->error('Error al actualizar la solicitud de desbloqueo', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }
    }
}
