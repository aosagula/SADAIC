@extends('member.layout.dashboard')

@section('content')
<div class="container">
    <div class="row d-flex flex-row align-items-center">
        <div class="col col-9">
            <h2>Solicitudes de Registro de Obra</h2>
        </div>
        <div class="col col-3 text-right">
            <h3><a href="{{ url('/member/work/register') }}">Nueva Solicitud</a></h3>
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
                        <th>N°</th>
                        <th>Título</th>
                        <th>Género</th>
                        <th>Fecha de solicitud</th>
                        <th>Último movimiento</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($requests as $request)
                    <tr>
                        <td>{{ $request->id }}</td>
                        <td class="w-25">{{ $request->title }}</td>
                        <td>{{ optional($request->genre)->name }}</td>
                        <td>{{ $request->created_at->format('d/m/Y H:i') }}</td>
                        <td>{{ $request->updated_at->format('d/m/Y H:i') }}</td>
                        
                        {{-- Trámite finalizado --}}
                        @if ($request->status_id === 9)
                            @if ($request->approved)
                            <td class="text-center">Registro Aprobado</td>
                            @else
                            <td class="text-center">Registro Rechazado</td>
                            @endif
                            <td class="text-center"><a href="/member/work/view/{{ $request->id }}">Ver</a></td>
                        {{-- Si es el iniciador y todavía no se envió la solicitud, se puede editar --}}
                        @elseif ($request->member_id == Auth::user()->id && $request->status_id === null)
                            <td class="text-center">No enviada</td>
                            <td class="text-center">
                                <a href="/member/work/edit/{{ $request->id }}">Editar</a> &nbsp;
                                <a href="/member/work/delete/{{ $request->id }}">Eliminar</a>
                            </td>
                        {{-- Si es parte y está en proceso o en disputa, puede responder --}}
                        @elseif ($request->distribution->contains('member_id', Auth::user()->member_id) && ($request->status_id == 2 || $request->status_id == 3) && $request->distribution->firstWhere('member_id', Auth::user()->member_id)->response !== true)
                            <td class="text-center">{{ optional($request->status)->name }}</td>
                            <td><a href="/member/work/{{ $request->id }}/response/">Responder</a></td>
                        {{-- Si no, no --}}
                        @else
                            <td class="text-center">{{ optional($request->status)->name }}</td>
                            <td class="text-center"><a href="/member/work/view/{{ $request->id }}">Ver</a></td>
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
