<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Logout;
use OwenIt\Auditing\Models\Audit;

class LogUserLogoutEvent
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Logout  $event
     * @return void
     */
    public function handle(Logout $event)
    {
        $user = $event->user;
        $guard = $event->guard;

        Audit::create([
            'user_id' => $user->id,
            'event' => 'Cerrar session',
            'auditable_type' => 'App\Models\Seguridad\User',
            'auditable_id' => $user->id,
            'old_values' => [],
            'new_values' => [
                'guard' => $guard,
                'email' => $user->email,
            ],
            'url' => request()->url(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'), 
        ]);
    }
}
