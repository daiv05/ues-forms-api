<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Session;
use Illuminate\Validation\ValidationException;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ResponseTrait;
use Illuminate\Support\Facades\Auth;

class ValidateUser
{
    use ResponseTrait;

    public function handle(Request $request, Closure $next): Response
    {
        $user = $request->user('api');

        // Usuario SUPERADMIN por defecto
        if ($user->id === 1) {
            return $next($request);
        }

        // Usuario activo
        if ($user->activo === 0) {
            Auth::guard('api')->logout();
            return $this->error('Error de autenticación', 'Tu solicitud de registro sigue pendiente', Response::HTTP_UNAUTHORIZED);
        }

        // Usuario estado activo (1)
        if ($user->id_estado !== 1) {
            $message = 'El usuario se encuentra' . ($user->estado === 2 ? ' inactivo' : ' bloqueado') . ' dentro del sistema';
            Auth::guard('api')->logout();
            return $this->error('Error de autenticación', $message, Response::HTTP_UNAUTHORIZED);
        }

        // Posee al menos un rol activo
        $validRole = 0;
        $user->roles->each(function ($rol) use (&$validRole) {
            if ($rol->activo === true) {
                $validRole++;
            }
        });

        if (!$validRole) {
            $message = 'El usuario no posee un rol activo dentro del sistema';
            Auth::guard('api')->logout();
            return $this->error('Error de autenticación', $message, Response::HTTP_UNAUTHORIZED);
        }

        // Revisar si el email del usuario está verificado
        if ($user->email_verified_at === null) {
            Auth::guard('api')->logout();
            return $this->error('Error de autenticación', 'El email del usuario no ha sido verificado', Response::HTTP_UNAUTHORIZED);
        }

        return $next($request);
    }
}
