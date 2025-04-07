<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use OwenIt\Auditing\Models\Audit;

class LogUserLoginEvent
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Login  $event
     * @return void
     */
    public function handle(Login $event)
    {
        $user = $event->user;
        $guard = $event->guard;
        $remember = $event->remember;
        Audit::create([
            'user_id' => $user->id,
            'event' => 'Inicio de sesión',
            'auditable_type' => 'App\Models\Seguridad\User',
            'auditable_id' => $user->id,
            'old_values' => [],
            'new_values' => [
                'guard' => $guard,
                'remember' => $remember,
                'email' => $user->email,
            ],
            'url' => request()->url(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);
    }
}
