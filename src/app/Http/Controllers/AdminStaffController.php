<?php

namespace App\Http\Controllers;

use App\Models\User;

class AdminStaffController extends Controller
{
    public function index()
    {
        // 一般ユーザーのみ取得
        $users = User::where('is_admin', 0)->get();

        return view('admin.staff.index', compact('users'));
    }
}
