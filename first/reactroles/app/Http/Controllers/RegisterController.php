<?php

namespace App\Http\Controllers;

use App\Models\User;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;

class RegisterController extends Controller
{
    public function _construct() {
        $this -> middleware('guest');
    }

    public function register() {
        return view('auth.register');
    }


    public function create(Request $request)
    {
//        $this->validate($request, [
//            'name' => 'required|unique:products|max:255',
//            'email' => 'required|email|unique:users',
//            'password' => 'required|min:8|confirmed',
//
//        ]);
        User::create([
            'name' => $request->name,
            'email' => $request->email,
            'password' => Hash::make($request->password)
            ]);

        return redirect()
            -> route('login');
    }
}
