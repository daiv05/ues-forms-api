<?php

namespace App\Http\Controllers\Seguridad\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmail;
use App\Models\Seguridad\AuthVerifiedEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;

class AuthVerifiedEmailController extends Controller
{
    public function sendVerificationCode(Request $request)
    {
        // Validar la solicitud
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ]);

        if ($validator->fails()) {
            return response()->json($validator->errors(), 400);
        }

        $email = $request->email;

        // Verificar si ya existe un email un código previo
        $verifiedEmail = AuthVerifiedEmail::where('email', $email)->first();

        if ($verifiedEmail) {
            // Verificar si el email ya fue verificado previamente para no enviar un nuevo código
            if ($verifiedEmail->verified_at) {
                return response()->json([
                    'message' => 'Email ya verificado'
                ], 400);
            }

            // Eliminar el código previo
            $verifiedEmail->delete();
        }

        $code = rand(100000, 999999); // Código de 6 dígitos o código de verificación OTP
        $timeToExpire = env('VERIFICATION_CODE_TTL', 15); // Tiempo de expiración en minutos
        $fecha_expiracion = date('Y-m-d H:i:s', strtotime('+' . $timeToExpire . ' minutes'));

        try {
            // Enviar el código de verificación
            $this->enviarCorreo(VerifyEmail::class, [
                'destinatarios' => [
                    'propietario' => $email
                ],
                'emailReceptor' => $email,
                'verificationCode' => $code,
                'expirationCode' => $timeToExpire . ' minutos'
            ]);
        } catch (\Exception $e) {
            Log::error('Error al enviar el correo: ' . $e->getMessage());
            return response()->json([
                'message' => 'Error al enviar el código'
            ], 500);
        }

        // Guardar el código de verificación en la base de datos
        AuthVerifiedEmail::create([
            'email' => $email,
            'verification_code' => $code,
            'expiration_code' => $fecha_expiracion,
            'verified_at' => null
        ]);

        return response()->json([
            'message' => 'Código de verificación enviado'
        ], 200);
    }

    public static function enviarCorreo($mailable, $data)
    {
        Mail::to($data['emailReceptor'])->send(new $mailable($data));
    }
}
