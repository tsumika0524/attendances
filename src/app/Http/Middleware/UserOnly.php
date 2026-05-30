<?php

namespace App\Http\Middleware;

use Closure;
use Illuminate\Http\Request;

class UserOnly
{
    public function handle(Request $request, Closure $next)
    {
        if (!auth()->check() || auth()->user()->is_admin) {

            auth()->logout();

            return redirect('/login')->withErrors([
                'email' => 'ユーザーのみログイン可能です'
            ]);
        }

        return $next($request);
    }
}
