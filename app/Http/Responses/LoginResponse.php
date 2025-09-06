<?php

namespace App\Http\Responses;

use Illuminate\Http\Request;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        // وُجهة الدور
        $target = ($user->hasRole('Admin') || $user->hasRole('Seller'))
            ? route('admin.dashboard')
            : route('account.index');

        // احترم URL intended إن كان موجودًا
        if ($intended = redirect()->getIntendedUrl()) {
            $target = $intended;
        }

        // استجابة JSON عند الطلب (واجهات SPA)
        if ($request->wantsJson()) {
            return response()->json(['redirect' => $target]);
        }

        return redirect()->intended($target);
    }
}
