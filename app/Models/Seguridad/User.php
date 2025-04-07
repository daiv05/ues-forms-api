<?php

namespace App\Models\Seguridad;

use App\Models\Registro\Persona;
use App\Models\Reportes\Reporte;
use App\Models\rhu\EmpleadoPuesto;
use App\Models\Mantenimientos\Escuela;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use OwenIt\Auditing\Contracts\Auditable;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\SendTwoFactorCode;
use Illuminate\Support\Facades\DB;
use IvanoMatteo\LaravelDeviceTracking\Facades\DeviceTracker;
use IvanoMatteo\LaravelDeviceTracking\Traits\UseDevices;

class User extends Authenticatable implements Auditable
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable, UseDevices, \OwenIt\Auditing\Auditable;


    protected $fillable = [
        'carnet',
        'email',
        'password',
        'id_persona',
        'activo',
        'id_escuela',
        'es_estudiante'
    ];

    protected $hidden = [
        'password',
        'remember_token',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
    ];

    public function sendPasswordResetNotification($token): void
    {
        $url = config('app.url') . '/reset-password' . '/' . $token . '?email=' . $this->email;
        $this->notify(new ResetPasswordNotification($url));
    }

    public function generateTwoFactorCode(): int
    {
        $verify =  DB::table('two_factor_tokens')->where([
            ['user_id', $this->id]
        ]);

        if ($verify->exists()) {
            $verify->delete();
        }

        $code = rand(100000, 999999);

        DB::table('two_factor_tokens')
            ->insert(
                [
                    'user_id' => $this->id,
                    'token' => $code,
                    'expires_at' => now()->addMinutes(10)
                ]
            );

        return $code;
    }

    public function sendTwoFactorCode($code): void
    {
        $this->notify(new SendTwoFactorCode($code));
    }

    public function markDeviceAsVerified(): void
    {
        DeviceTracker::flagCurrentAsVerified();
    }

    public function hasDeviceVerified(): bool
    {
        $device = DeviceTracker::detectFindAndUpdate();
        return $device->currentUserStatus->verified_at !== null;
    }

    public function setCarnetAttribute($value)
    {
        $this->attributes['carnet'] = strtoupper(strtr($value, 'áéíóú', 'ÁÉÍÓÚ'));
    }

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    public function empleadosPuestos(): HasMany
    {
        return $this->hasMany(EmpleadoPuesto::class, 'id_usuario');
    }

    public function reportes(): HasMany
    {
        return $this->hasMany(Reporte::class, 'id_usuario_reporta');
    }

    public function escuela(): BelongsTo
    {
        return $this->belongsTo(Escuela::class, 'id_escuela');
    }
}
