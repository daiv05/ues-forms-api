<?php

namespace App\Http\Controllers\Seguridad\Auth;

use App\Http\Controllers\Controller;
use App\Mail\VerifyEmail;
use App\Models\Seguridad\AuthVerifiedEmail;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Log;
use Illuminate\Support\Facades\Mail;
use Illuminate\Support\Facades\Validator;
use Symfony\Component\HttpFoundation\Response;
use App\Traits\ResponseTrait;

class AuthVerifiedEmailController extends Controller
{
    use ResponseTrait;

    public function sendVerificationCode(Request $request)
    {
        // Validar la solicitud
        $validator = Validator::make($request->all(), [
            'email' => 'required|email'
        ], [
            'email.required' => 'Debe ingresar un email',
            'email.email' => 'El email debe ser una dirección de correo electrónico válida'
        ]);

        if ($validator->fails()) {
            return $this->validationError('Error de validación', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $email = $request->email;

        // Verificar si ya existe un email un código previo
        $verifiedEmail = AuthVerifiedEmail::where('email', $email)->first();

        if ($verifiedEmail) {
            // Verificar si el email ya fue verificado previamente para no enviar un nuevo código
            if ($verifiedEmail->verified_at) {
                return $this->error('El email ya ha sido verificado', "", Response::HTTP_BAD_REQUEST);
            }

            // Eliminar el código previo
            $verifiedEmail->delete();
        }

        $code = rand(100000, 999999); // Código de 6 dígitos o código de verificación OTP
        $timeToExpire = config('auth.verification_code_ttl'); // Tiempo de expiración en minutos
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
            return $this->error('Error al enviar el correo', $e->getMessage(), Response::HTTP_INTERNAL_SERVER_ERROR);
        }

        // Guardar el código de verificación en la base de datos
        AuthVerifiedEmail::create([
            'email' => $email,
            'verification_code' => $code,
            'expiration_code' => $fecha_expiracion,
            'verified_at' => null
        ]);

        return $this->success('Código de verificación enviado con éxito', [
            'email' => $email,
            'expiration_date' => $fecha_expiracion
        ], Response::HTTP_OK);
    }

    public function verifyEmail(Request $request)
    {
        // Validar la solicitud
        $validator = Validator::make($request->all(), [
            'email' => 'required|email',
            'verification_code' => 'required|integer'
        ], [
            'email.required' => 'El campo email es obligatorio',
            'email.email' => 'El campo email debe ser una dirección de correo electrónico válida',
            'verification_code.required' => 'Debe ingresar un código de verificación',
            'verification_code.integer' => 'El código de verificación debe ser un número de 6 dígitos'
        ]);

        if ($validator->fails()) {
            return $this->validationError('Error de validación', $validator->errors(), Response::HTTP_UNPROCESSABLE_ENTITY);
        }

        $email = $request->email;
        $verificationCode = $request->verification_code;

        // Verificar el código de verificación
        $verifiedEmail = AuthVerifiedEmail::where('email', $email)
            ->where('verification_code', $verificationCode)
            ->whereNull('verified_at')
            ->first();

        if (!$verifiedEmail) {
            return $this->error('Código de verificación inválido o ya utilizado', "", Response::HTTP_BAD_REQUEST);
        }

        // Marcar el email como verificado
        $verifiedEmail->verified_at = now();
        $verifiedEmail->save();

        return $this->success('Email verificado con éxito', [
            'email' => $email,
            'verification_code' => $verificationCode
        ], Response::HTTP_OK);
    }

    public static function enviarCorreo($mailable, $data)
    {
        Mail::to($data['emailReceptor'])->send(new $mailable($data));
    }
}
