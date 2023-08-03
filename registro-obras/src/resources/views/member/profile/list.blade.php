@extends('member.layout.dashboard')

@section('content')
<div class="container">
    <div class="row d-flex flex-row align-items-center">
        <div class="col col-9">
            <h2>Solicitudes de Actualización de Datos</h2>
        </div>
        <div class="col col-3 text-right">
            @if (!$disableNew)
            <h3><a href="{{ url('/member/profile/update') }}">Nueva Solicitud</a></h3>
            @endif
        </div>
    </div>
    @if(Session::has('message.type'))
    <div class="row">
        <div class="col col-12">
            <div class="alert alert-{{ Session::get('message.type', 'secondary') }}" role="alert">
                {{ Session::get('message.data') }}
            </div>
        </div>
    </div>
    @endif
    <div class="row">
        <table class="table">
            <thead>
                <tr>
                    <th>N°</th>
                    <th>Fecha</th>
                    <th>Estado</th>
                    <th></th>
                </tr>
            </thead>
            @foreach ($requests as $req)
            <tr>
                <td>{{ $req->id }}</td>
                <td>{{date('d/m/Y H:i', strtotime($req->created_at)) }}</td>
                <td>Pendiente</td>
                <td><a href="/member/profile/update/{{ $req->id }}/view">Ver</a></td>
            </tr>
            @endforeach
        </table>
    </div>
</div>
@endsection
