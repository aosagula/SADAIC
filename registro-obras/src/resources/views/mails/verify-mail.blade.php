@extends('mails.template')

@section('content')
<p>¡Hola {{ $nombre }}!</p>
<p>Por favor, haz click en el botón a continuación para verificar tu dirección de correo electrónico:</p>
<div class="cta">
    <a class="btn" href="{{ $url }}">Verificar Correo Electrónico</a>
</div>
@endsection