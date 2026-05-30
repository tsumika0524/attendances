<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;
use App\Http\Requests\AdminLoginRequest;

class AdminAuthController extends Controller
{
    public function login(AdminLoginRequest $request)
{
    if (Auth::attempt($request->only('email', 'password'))) {

        $user = Auth::user();

        if ($user->is_admin) {

            $request->session()->regenerate();

            return redirect('/admin/attendance/list');
        }

        Auth::logout();

        return back()->withErrors([
            'email' => '管理者アカウントではありません'
        ]);
    }

    return back()->withErrors([
        'email' => 'ログイン情報が登録されていません'
    ]);
}
}