<?php

namespace App\Listeners;

use Illuminate\Auth\Events\PasswordReset;
use OwenIt\Auditing\Models\Audit;

class LogPasswordResetEvent
{
    public function handle(PasswordReset $event)
    {
        $user = $event->user;
        Audit::create([
            'user_id' => $user->id,
            'event' => 'Resetear contra',
            'auditable_type' => 'App\Models\Seguridad\User',
            'auditable_id' => $user->id,
            'old_values' => [],
            'new_values' => [],
            'url' => request()->url(),
            'ip_address' => request()->ip(),
            'user_agent' => request()->header('User-Agent'),
        ]);
    }
}
