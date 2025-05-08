<?php

namespace App\Models\Seguridad;

use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\SoftDeletes;

class AuthVerifiedEmail extends Model
{
    use HasFactory, SoftDeletes;

    protected $table = 'auth_verified_email';

    protected $fillable = [
        'email',
        'verification_code',
        'expiration_code',
        'verified_at'
    ];

    protected $casts = [
        'expiration_code' => 'datetime',
        'verified_at' => 'datetime',
    ];


}
