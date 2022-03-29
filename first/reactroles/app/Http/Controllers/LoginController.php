<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;
use Illuminate\Support\Facades\Auth;

class LoginController extends Controller
{
    public function login() {
        return view('auth.login');
    }

    public function auth(Request $request) {

        $credentials = $request->only('name', 'password');
        if (Auth::attempt($credentials)) {
            return redirect()
                ->route('index');
        }
    }

    public function logout() {
        Auth::logout();
        return redirect()
            ->route('login');
    }

}
