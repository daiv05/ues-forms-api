<?php

namespace App\Http\Controllers\Seguridad\Auth;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use Tymon\JWTAuth\Exceptions\JWTException;

class AuthController extends Controller
{
    public function login(Request $request)
    {
        $credentials = $request->only('email', 'password');

        if(!$token = Auth::guard('api')->attempt($credentials)) {
            return response()->json(['message' => 'Credenciales incorrectas'], 401);
        }

        return response()->json([
            'accessToken' => $token,
            'expires_in' => Auth::guard('api')->factory()->getTTL()
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
            $newToken = Auth::guard('api')->refresh();
            return response()->json([
                'token' => $newToken,
                'expires_in' => Auth::guard('api')->factory()->getTTL(),
            ]);
        } catch (JWTException $e) {
            return response()->json(['message' => 'No se puedo recuperar la sesión'], 401);
        }
    }

    public function me(Request $request)
    {
        return response()->json($request->user());
    }
}
