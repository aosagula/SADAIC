@extends($user_type . '.layout.dashboard')

@section('content')
<div class="container">
    <div class="row d-flex flex-row align-items-center">
        <div class="col col-9">
            <h2>Solicitud de Inclusión</h2>
        </div>
        <div class="col col-3 text-right">
            <h3><a href="{{ url('/' . $user_type . '/jingles') }}">Volver</a></h3>
        </div>
    </div>
    <div class="row">
        <div class="col col-12">
            <p>Solicito a la Sociedad Argentina de Autores y Compositores de Música (SADAIC), de acuerdo a las condiciones establecidas en el Régimen
            Autoral para el uso de Obras Musicales en Actos de Naturaleza Publicitaria, para la inclusión de la obra cuyo título, autor/es,
            compositor/es y/o editor/es se detalla a continuación y afirma que los datos consignados en la presente son correctos y completos,
            y que esta solicitud y declaración se ha confeccionado sin omitir dato alguno que deba contener, siendo la expresión de la verdad. Complete los datos obligatorios (*) para poder generar la solicitud de inclusión.</p>
        </div>
    </div>
    @if(session('message.type'))
    <div class="row">
        <div class="alert alert-{{ session('message.type') }} col-md-12 col-lg-10 offset-lg-1">
            {{ session('message.data') }}
        </div>
    </div>
    @endif
    @if ($errors->any())
    <div class="row">
        <div class="alert alert-danger col-md-12 col-lg-10 offset-lg-1">
            <ul>
                @foreach ($errors->all() as $error)
                    <li>{{ $error }}</li>
                @endforeach
            </ul>
        </div>
    </div>
    @endif
    <div class="row">
        <div class="col col-12">
            @include('jingles.form')
        </div>
    </div>
    <div class="row">
        <div class="col col-6 text-center">
            <button type="button" class="btn btn-secondary" id="saveRegister">Guardar solicitud</button>
        </div>
        <div class="col col-6 text-center">
            <button type="button" class="btn btn-primary" id="sendRegister">Enviar solicitud a SADAIC</button>
        </div>
    </div>
</div>
@endsection
