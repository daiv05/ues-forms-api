<?php

namespace App\Http\Controllers\Seguridad\Auth;

use App\Enums\EstadosEnum;
use App\Http\Controllers\Controller;
use App\Models\Seguridad\SolicitudDesbloqueo;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ResponseTrait;
use App\Traits\PaginationTrait;
use Illuminate\Support\Facades\Auth;
use Illuminate\Support\Facades\DB;

class AuthUnlockingController extends Controller
{
    use ResponseTrait, PaginationTrait;

    public function index(Request $request)
    {
        // Implementación de la función index si es necesario
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
}
