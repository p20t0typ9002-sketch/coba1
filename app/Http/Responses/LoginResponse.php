<?php

namespace App\Http\Responses;

use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request)
    {
        $user = $request->user();

        if ($user->level === 'admin' || $user->level === 'petugas') {
            return redirect('/admin');
        }

        return redirect()->route('home');
    }
}