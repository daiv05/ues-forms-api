<?php

namespace App\Listeners;

use Illuminate\Auth\Events\Login;
use OwenIt\Auditing\Models\Audit;
use Illuminate\Support\Facades\Auth;

class LogLoginEvent
{
    public function handle(Login $event)
    {
        $user = $event->user;
        Audit::create([
            'user_id' => $user->id,
            'event' => 'login',
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
