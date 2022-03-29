@extends('layout.app')
@section('title')Главная@endsection
@section('content')
    <div class="enter">
        <ul>
            @guest
                <li>
                    <a href="{{ 'login' }}">Войти</a>
                </li>
                <li>
                    <a href="{{ 'register' }}">Регистрация</a>
                </li>
            @else
                <li>
                    <a href="{{ 'index'}}">ЛК</a>
                </li>
                <li>
                    <a href="{{'logout'}}">Выход</a>
                </li>
            @endif
        </ul>
    </div>
@endsection
