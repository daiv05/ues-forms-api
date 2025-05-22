<?php

namespace App\Http\Controllers\Seguridad\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Http\Response;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;
use App\Traits\ResponseTrait;
use Tymon\JWTAuth\Facades\JWTAuth;

class AuthController extends Controller
{
    use ResponseTrait;

    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        $token = Auth::guard('api')->attempt($credentials);
        if (!$token) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        $user = Auth::guard('api')->user();

        // Usuario activo
        if ($user->activo === false) {
            Auth::guard('api')->logout();
            return $this->error('Tu solicitud de registro sigue pendiente', 'Error de autenticación', Response::HTTP_UNAUTHORIZED);
        }

        // Usuario inactivo
        if ($user->id_estado === 2) {
            Auth::guard('api')->logout();
            return $this->error('Tu usuario se encuentra inactivo dentro del sistema', 'Error de autenticación', Response::HTTP_UNAUTHORIZED);
        }

        // Posee al menos un rol activo
        $validRole = 0;
        $user->roles->each(function ($rol) use (&$validRole) {
            if ($rol->activo === true) {
                $validRole++;
            }
        });

        if (!$validRole) {
            Auth::guard('api')->logout();
            return $this->error('Error de autenticación', 'El usuario no posee un rol activo dentro del sistema', Response::HTTP_UNAUTHORIZED);
        }

        // Revisar si el email del usuario está verificado
        if ($user->email_verified_at === null) {
            Auth::guard('api')->logout();
            return $this->error('El email del usuario no ha sido verificado', 'Error de autenticación', Response::HTTP_UNAUTHORIZED);
        }

        // Usuario bloqueado
        $isUnlocked = true;
        if ($user->id_estado === 3) {
            $isUnlocked = false;
        }

        return response()->json([
            'accessToken' => $token,
            'expires_in' => JWTAuth::factory()->getTTL() * 60,
            'isUnlocked' => $isUnlocked,
        ]);
    }

    public function logout(Request $request)
    {
        Auth::guard('api')->logout();

        return response()->json(['message' => 'Sesión cerrada con éxito']);
    }

    public function refresh(Request $request)
    {
        try {
            $newToken = JWTAuth::parseToken()->refresh();
            return response()->json([
                'accessToken' => $newToken,
                'expires_in' => JWTAuth::factory()->getTTL() * 60,
            ]);
        } catch (JWTException $e) {
            return response()->json(['message' => 'No se puede recuperar la sesión'], 401);
        }
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
