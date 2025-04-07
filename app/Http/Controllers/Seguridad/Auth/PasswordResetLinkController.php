<?php

namespace App\Http\Controllers\Seguridad\Auth;

use App\Http\Controllers\Controller;
use App\Models\Seguridad\User;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Password;
use Illuminate\View\View;
use OwenIt\Auditing\Models\Audit;

class PasswordResetLinkController extends Controller
{

    public function create(): View
    {
        return view('seguridad.auth.forgot-password');
    }

    public function store(Request $request): RedirectResponse
    {
        // Validar el correo electrónico
        $request->validate([
            'email' => ['required', 'email'],
        ]);

        // Encontrar el usuario por correo electrónico
        $user = User::where('email', $request->email)->first();

        // Enviar el enlace de restablecimiento de contraseña
        $status = Password::sendResetLink(
            $request->only('email')
        );

        // Si el usuario existe, auditamos la solicitud
        if ($user) {
            Audit::create([
                'user_id' => $user->id,  // ID del usuario que realizó la acción (si corresponde)
                'event' => 'password_reset_request',
                'auditable_type' => 'App\Models\Seguridad\User',  // Tipo de entidad que está siendo auditada (User)
                'auditable_id' => $user->id,  // ID del usuario
                'old_values' => [],
                'new_values' => ['email' => $request->email],
                'url' => request()->url(),
                'ip_address' => request()->ip(),
                'user_agent' => request()->header('User-Agent'),
            ]);
        }

        // Responder con un mensaje según el estado de la solicitud
        return $status == Password::RESET_LINK_SENT
            ? back()->with('status', __($status))
            : back()->withInput($request->only('email'))
                ->withErrors(['email' => __($status)]);
    }
}
