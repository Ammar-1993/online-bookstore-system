<?php

namespace App\Models;

use App\Models\Book;
use Illuminate\Contracts\Auth\MustVerifyEmail;
use Illuminate\Database\Eloquent\Factories\HasFactory;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Foundation\Auth\User as Authenticatable;
use Illuminate\Notifications\Notifiable;
use Laravel\Fortify\TwoFactorAuthenticatable;
use Laravel\Jetstream\HasProfilePhoto;
use Laravel\Jetstream\HasTeams;
use Laravel\Sanctum\HasApiTokens;
use Spatie\Permission\Traits\HasRoles;
use App\Models\Order;

class User extends Authenticatable implements MustVerifyEmail
{
    use HasApiTokens;
    use HasFactory;
    use Notifiable;
    use HasProfilePhoto;
    use TwoFactorAuthenticatable;
    use HasTeams;
    use HasRoles;

    /**
     * حارس الصلاحيات لحزمة Spatie.
     */
    protected string $guard_name = 'web';

    /**
     * الحقول القابلة للتعبئة.
     */
    protected $fillable = ['name', 'email', 'password'];

    /**
     * الحقول المخفية عند التحويل لمصفوفة/JSON.
     */
    protected $hidden = [
        'password',
        'remember_token',
        'two_factor_recovery_codes',
        'two_factor_secret',
    ];

    /**
     * التحويلات (casts).
     */
    protected $casts = [
        'email_verified_at' => 'datetime',
        'password'          => 'hashed',
    ];

    /**
     * ملاحق تلقائية لواجهة Jetstream.
     */
    protected $appends = ['profile_photo_url'];

    /* -----------------------------------------------------------------
     | العلاقات
     |-----------------------------------------------------------------*/

    /**
     * كتب هذا المستخدم كبائع (seller_id على جدول الكتب).
     */
    public function books(): HasMany
    {
        return $this->hasMany(Book::class, 'seller_id');
    }

    /* -----------------------------------------------------------------
     | مُساعدات للأدوار (اختياري لكنها مفيدة في الواجهات)
     |-----------------------------------------------------------------*/

    public function isAdmin(): bool
    {
        return $this->hasRole('Admin');
    }

    public function isSeller(): bool
    {
        return $this->hasRole('Seller');
    }

    public function orders(): HasMany
    {
        return $this->hasMany(Order::class);
    }
}
