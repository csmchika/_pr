@extends('layout.app')
@section('title')Вход@endsection
@section('content')
    <h1>Вход</h1>
    <form action="{{route('auth')}}" method="post">
        @csrf
        <input type="text" name="name" placeholder="Имя">
        <input type="text" name="password">
        <button type="submit">Вход</button>
    </form>
@endsection
