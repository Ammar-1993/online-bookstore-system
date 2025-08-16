<?php

namespace App\Providers;

use Illuminate\Support\ServiceProvider;
use App\Support\Cart;
use Illuminate\Support\Facades\View;

class AppServiceProvider extends ServiceProvider
{
    /**
     * Register any application services.
     */
    public function register(): void
    {
        //
    }

    /**
     * Bootstrap any application services.
     */
    public function boot(): void
    {
        // داخل boot():
View::composer('*', function ($view) {
    $cart = new Cart();
    $view->with('cartCount', $cart->count());
});
    }
}
