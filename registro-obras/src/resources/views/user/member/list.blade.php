@extends('user.layout.dashboard')

@section('content')
<div class="container">
    <div class="row d-flex flex-row align-items-center">
        <div class="col col-9">
            <h2>Solicitudes de Ingreso Representado Inscripto</h2>
        </div>
        <div class="col col-3 text-right">
            <h3><a href="{{ url('/user/member/register') }}">Nueva Solicitud</a></h3>
        </div>
    </div>
    @if(session('message.type'))
    <div class="row">
        <div class="alert alert-{{ session('message.type') }} col-md-12 col-lg-10 offset-lg-1">
            {{ session('message.data') }}
        </div>
    </div>
    @endif
    <div class="row">
        <div class="col col-12">
            <table class="table">
                <thead>
                    <tr>
                        <th>Nombre</th>
                        <th>Pseudónimo</th>
                        <th>Teléfono</th>
                        <th>Celular</th>
                        <th>Correo Electrónico</th>
                        <th>Fecha</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($requests as $request)
                    <tr>
                        <td>{{ $request->name }}</td>
                        <td>{{ $request->pseudonym }}</td>
                        <td>{{ $request->landline }}</td>
                        <td>{{ $request->mobile }}</td>
                        <td>{{ $request->email }}</td>
                        <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ optional($request->status)->name }}</td>
                        @if ($request->status_id === null)
                        <td><a href="/user/member/edit/{{ $request->id }}">Editar</a></td>
                        @else
                        <td><a href="/user/member/{{ $request->id }}">Ver</a></td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
