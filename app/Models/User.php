<?php

namespace App\Models;

use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;          // ← مهم
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasProfilePhoto;
    use TwoFactorAuthenticatable;
    use HasTeams;                         // ← مهم
    use HasRoles;

    protected string $guard_name = 'web';

    protected $fillable = ['name','email','password'];

    protected $hidden = [
        'password','remember_token',
        'two_factor_recovery_codes','two_factor_secret',
    ];

    protected $casts = [
        'email_verified_at' => 'datetime',
        'password' => 'hashed',
    ];

    protected $appends = ['profile_photo_url'];
}
