@extends(Auth::user()->type . '.layout.dashboard')

@section('content')
<div class="container">
    <div class="row d-flex flex-row align-items-center">
        <div class="col col-9">
            <h2>Solicitudes de Registro de Obra</h2>
        </div>
        <div class="col col-3 text-right">
            <h3><a href="{{ url('/' . Auth::user()->type . '/work/list') }}">Volver</a></h3>
        </div>
    </div>

    @if($registration->status_id === 9)
    <div class="row mt-4 mb-4">
        <div class="col-sm-8 offset-sm-2">
            <div class="alert alert-danger" role="alert">
                <strong>Solicitud Rechazada:</strong>
                {{ $registration->rejection_reason }}
            </div>
        </div>
    </div>
    @endif

    @include('components.work-view')
</div>
@endsection
