<?php

namespace App\Http\Controllers\Seguridad\Auth;

use App\Enums\EstadosEnum;
use App\Http\Controllers\Controller;
use App\Models\Seguridad\FailedLoginAttempts;
use App\Models\Seguridad\User;
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

            // Limitar el número de intentos fallidos
            $failedLoginAttempts = FailedLoginAttempts::where('email', $request->input('email'))
                ->where('created_at', '>=', now()->subMinutes(5))
                ->count();
            if ($failedLoginAttempts >= 5) {
                // Si el número de intentos fallidos es mayor o igual a 5, bloquear el acceso
                $usuario = User::where('email', $request->input('email'))->first();
                if ($usuario) {
                    $usuario->id_estado = EstadosEnum::BLOQUEADO;
                    $usuario->save();
                }
                return $this->error('Tu usuario ha sido bloqueado por múltiples intentos fallidos de inicio de sesión', 'Error de autenticación', Response::HTTP_UNAUTHORIZED);
            }

            // Guardar el intento fallido
            $failedLoginAttempt = new FailedLoginAttempts();
            $failedLoginAttempt->email = $request->input('email');
            $failedLoginAttempt->ip_address = $request->ip();
            $failedLoginAttempt->user_agent = $request->header('User-Agent');
            $failedLoginAttempt->device = [
                'platform' => $request->header('Platform'),
                'version' => $request->header('Version'),
                'device' => $request->header('Device'),
            ];
            $failedLoginAttempt->save();
            // Limpiar los intentos fallidos después de 5 minutos
            FailedLoginAttempts::where('email', $request->input('email'))
                ->where('created_at', '<', now()->subMinutes(5))
                ->delete();
            return $this->error('Credenciales incorrectas', 'Error de autenticación', Response::HTTP_UNAUTHORIZED);
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
            'expires_in' => JWTAuth::factory()->getTTL(),
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
