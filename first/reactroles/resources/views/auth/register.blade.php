@extends('layout.app')
@section('title')Регистрация@endsection
@section('content')
    <h1>Регистрация</h1>
    <form action="{{route('register')}}" method="post">
        @csrf
        <input type="text" name="name" placeholder="Имя">
        <input type="email" name="email" placeholder="Почта">
        <input type="text" name="password">
        <input type="text" name="password2">
        <button type="submit">Регистрация</button>
    </form>
@endsection
