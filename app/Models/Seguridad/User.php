<?php

namespace App\Models\Seguridad;

use App\Models\Catalogo\Estado;
use App\Models\Encuesta\GrupoMeta;
use App\Models\Registro\Persona;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Notifications\ResetPasswordNotification;
use App\Notifications\SendTwoFactorCode;
use Illuminate\Support\Facades\DB;
use Tymon\JWTAuth\Contracts\JWTSubject;

class User extends Authenticatable implements JWTSubject
{
    use HasApiTokens, HasFactory, HasRoles, Notifiable;

    protected $guard_name = 'api';

    protected $fillable = [
        'username',
        'email',
        'password',
        'id_persona',
        'activo',
        'id_estado'
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

    public function getRoles()
    {
        return $this->roles();
    }

    //
    // Relationships
    //

    public function persona(): BelongsTo
    {
        return $this->belongsTo(Persona::class, 'id_persona');
    }

    public function gruposMetas(): HasMany
    {
        return $this->hasMany(GrupoMeta::class, 'id_usuario');
    }

    public function encuestas(): HasMany
    {
        return $this->hasMany(GrupoMeta::class, 'id_usuario');
    }

    public function estado(): BelongsTo
    {
        return $this->belongsTo(Estado::class, 'id_estado');
    }

    //
    // JWTSubject
    //

    public function getJWTIdentifier()
    {
        return $this->getKey(); // Devuelve el ID del usuario
    }

    public function getJWTCustomClaims()
    {
        return [
            'id' => $this->id,
            'username' => $this->username,
            'email' => $this->email,
            'roles' => $this->getRoles()->pluck('name'),
            'permissions' => $this->getAllPermissions()->pluck('name'),
        ];
    }

    // Validaciones
    public function checkPermissions($permission = []): bool
    {
        return $this->hasAllPermissions($permission);
    }
}
