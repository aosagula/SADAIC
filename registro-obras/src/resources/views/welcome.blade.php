@extends('layouts.app')

@section('content')
<div class="container">
    <div class="row justify-content-center">
        <div class="col-md-8">
            @if (Auth::guard('members')->check() || Auth::guard('players')->check() || Auth::guard('web')->check())
            <a href="{{ route('logout') }}">Logout</a>
            @else
            <a href="{{ route('login') }}">Login</a>
            @endif
        </div>
    </div>
</div>
@endsection
