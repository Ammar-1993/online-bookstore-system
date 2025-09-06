<?php

namespace App\Actions\Auth;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class CustomLoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        // حدّد الصفحة حسب الدور
        $target = ($user && ($user->hasRole('Admin') || $user->hasRole('Seller')))
            ? route('admin.dashboard')
            : route('orders.index'); // أو route('home') إن رغبت

        // REST/JSON (إن تم الاستدعاء عبر XHR)
        if ($request->wantsJson()) {
            return response()->json(['redirect' => $target]);
        }

        // احترام intended ثم التحويل للهدف الافتراضي
        return redirect()->intended($target);
    }
}
