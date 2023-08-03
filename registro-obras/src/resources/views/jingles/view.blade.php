@extends(Auth::user()->type . '.layout.dashboard')

@section('content')
<div class="container">
    <div class="row d-flex flex-row align-items-center">
        <div class="col col-9">
            <h2>Solicitudes de Inclusión de Obra</h2>
        </div>
        <div class="col col-3 text-right">
            <h3><a href="{{ url('/' . Auth::user()->type . '/jingles') }}">Volver</a></h3>
        </div>
    </div>

    @include('components.jingle-view')
</div>
@endsection