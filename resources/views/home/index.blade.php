@extends('layouts.master')

@section('title', 'Home')

@section('content')
    <h1>Home page</h1>
    @foreach ($users as $user)
        <p>This is user {{ $user->name }}</p>
    @endforeach
@stop