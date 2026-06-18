<?php

namespace App\Http\Responses;

use Illuminate\Http\JsonResponse;
use Laravel\Fortify\Contracts\LoginResponse as LoginResponseContract;
use Symfony\Component\HttpFoundation\Response;

class LoginResponse implements LoginResponseContract
{
    public function toResponse($request): Response
    {
        if ($request->wantsJson()) {
            return new JsonResponse(['two_factor' => false], 200);
        }

        $user = auth()->user();
        if ($user) {
            if ($user->role === 'admin') {
                $path = route('admin.dashboard');
            } elseif ($user->role === 'kasir') {
                $path = route('kasir.cashier');
            } else {
                $path = route('member.dashboard');
            }

            return redirect()->intended($path);
        }

        return redirect()->intended('/');
    }
}
