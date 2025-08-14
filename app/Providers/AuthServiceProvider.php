<?php

namespace App\Providers;

use App\Models\Book;
use App\Policies\BookPolicy;
use Illuminate\Foundation\Support\Providers\AuthServiceProvider as ServiceProvider;
use Illuminate\Support\Facades\Gate;

class AuthServiceProvider extends ServiceProvider
{
    protected $policies = [
        Book::class => BookPolicy::class,
        // أضف سياسات أخرى لاحقًا إن وجدت
    ];

    public function boot(): void
    {
        $this->registerPolicies();

        // Gate عام اختياري إن فضّلت استخدامه بدل role:
        Gate::define('access-admin', fn($user) => $user->hasAnyRole(['Admin','Seller']));
        // وقتها بإمكانك في الراوت استخدام 'can:access-admin' بدل 'role:Admin|Seller'
    }
}
