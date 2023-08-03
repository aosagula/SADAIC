<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SADAIC') }}</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
    #verify button[type="submit"] {
        transform: none;
    }
    </style>
</head>
<body style="overflow: hidden">
<div id="verify" class="step2" style="display: flex !important;">
    <div class="left-side">
        <img src="{{ asset('/images/logo-white.png') }}" class="logo" />
        <p><strong>Verificá tu dirección de correo electrónico</strong> para
        poder ingresar en nuestro sistema de administración.</p>
        <span class="goBack">&#8592; Volver al inicio</span>
    </div>
    <div class="content">
        <div class="badge">
            <img src="{{ asset('/images/icons/users.png') }}">
        </div>
        <div class="header">
            <strong>USUARIOS</strong><br>
            VERIFICACIÓN<br>
            <img src="{{ asset('images/line.png' )}}">
        </div>
        <div class="formWrapper">
            @if (session('resent'))
                <div class="alert alert-success" role="alert">
                    Un nuevo enlace de verificación ha sido enviado a tu casilla de correo electrónico
                </div>
            @endif

            Antes de continuar, por favor, buscá un correo electrónico de verificación en
            tu bandeja de entrada y haz clic en el vínculo que se muestra en el mensaje.<br><br>
            Si no recibiste el mensaje de correo electrónico,
            <form class="d-inline" method="POST" action="{{ route('verification.resend') }}">
                @csrf
                <button type="submit" class="btn btn-link p-0 m-0 align-baseline">haz click aquí</button> para enviar uno nuevo.
            </form>
        </div>
    </div>
</div>
<script src="{{ asset('/js/app.js') }}"></script>
<script>
    $('.goBack').on('click', () => {
        window.location = '{{ url("logout") }}';
    });
</script>
</body>
</html>
