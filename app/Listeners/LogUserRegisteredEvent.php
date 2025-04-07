<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Registered;
use OwenIt\Auditing\Models\Audit;

class LogUserRegisteredEvent
{
    /**
     * Handle the event.
     *
     * @param  \Illuminate\Auth\Events\Registered  $event
     * @return void
     */
    public function handle(Registered $event)
    {
        $user = $event->user;
        Audit::create([
            'user_id' => $user->id,
            'event' => 'Registro',
            'auditable_type' => 'App\Models\Seguridad\User',
            'auditable_id' => $user->id,
            'old_values' => [],
            'new_values' => [
                'email' => $user->email,
                'name' => $user->name,
            ],
            'url' => request()->url(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'), 
        ]);
    }
}
