@php
$nombre = optional(Auth::user()->profile)->name;
if ($nombre == '') $nombre = Auth::user()->member_id . '/' . Auth::user()->heir;
@endphp
<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'Laravel') }}</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    @stack('styles')
</head>
<body>
    <div id="app">
        @include('member.layout.header') 
        @include('member.layout.sidebar') 
        <section id="main" class="dashboard">
            @yield('content')
        </section>
    </div>

    <script src="{{ asset('js/app.js') }}"></script>
    @stack('scripts')
</body>
</html>
