@extends('layout.app')
@section('title')ЛК@endsection
@section('content')
    @role('admin')
    <h1>admin</h1>
    @endrole
    @role('superadmin')
    <h1>superadmin</h1>
    @endrole
    @role('redactor')
    <h1>redactor</h1>
    @endrole
    @role('user')
    <h1>user</h1>
    @endrole
    <div id="root"></div>
@endsection
