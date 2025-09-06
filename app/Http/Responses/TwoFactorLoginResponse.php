<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\TwoFactorLoginResponse as TwoFactorLoginResponseContract;

class TwoFactorLoginResponse implements TwoFactorLoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        $target = ($user->hasRole('Admin') || $user->hasRole('Seller'))
            ? route('admin.dashboard')
            : route('account.index');

        if ($request->wantsJson()) {
            return response()->json(['redirect' => $target]);
        }

        return redirect()->intended($target);
    }
}
