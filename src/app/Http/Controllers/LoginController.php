<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use App\Http\Requests\LoginRequest;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login(LoginRequest $request)
{
    $credentials = $request->only('email', 'password');

    // 認証チェック
    if (!Auth::attempt($credentials)) {
        return back()->withErrors([
            'email' => 'ログイン情報が登録されていません',
        ]);
    }

    return redirect('/attendance'); // ログイン後
}
    //
}
