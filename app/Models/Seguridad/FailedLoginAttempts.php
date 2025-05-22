<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;

class FailedLoginAttempts extends Model
{
    use HasFactory;

    protected $table = 'failed_login_attempts';

    protected $fillable = [
        'email',
        'ip_address',
        'user_agent',
        'device',
    ];

    protected $casts = [
        'created_at' => 'datetime',
        'updated_at' => 'datetime',
    ];
    public function getDeviceAttribute($value)
    {
        return json_decode($value, true);
    }
    public function setDeviceAttribute($value)
    {
        $this->attributes['device'] = json_encode($value);
    }
    public function getUserAgentAttribute($value)
    {
        return json_decode($value, true);
    }
    public function setUserAgentAttribute($value)
    {
        $this->attributes['user_agent'] = json_encode($value);
    }
}
