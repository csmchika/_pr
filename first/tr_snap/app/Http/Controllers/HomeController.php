<?php

namespace App\Http\Controllers;

use Illuminate\Http\Request;

class HomeController extends Controller
{
    /**
     * Create a new controller instance.
     *
     * @return void
     */
    public function __construct()
    {
        $this->middleware('auth');
    }

    //    Просто возвращаем шаблон
    public function index():\Illuminate\Contracts\View\View
    {
        return view('home');
    }
}
