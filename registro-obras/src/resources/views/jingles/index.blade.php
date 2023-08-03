@extends($user_type . '.layout.dashboard')

@section('content')
<div class="container">
    <div class="row d-flex flex-row align-items-center">
        <div class="col col-9">
            <h2>Solicitudes de Inclusión</h2>
        </div>
        <div class="col col-3 text-right">
            <h3><a href="{{ url('/' . $user_type . '/jingles/create') }}">Nueva Solicitud</a></h3>
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
                        <th>Anunciante</th>
                        <th>Tipo de Solicitud</th>
                        <th>Marca</th>
                        <th>Título Obra</th>
                        <th>Estado</th>
                        <th></th>
                    </tr>
                </thead>
                <tbody>
                @foreach($requests as $request)
                    <tr>
                        <td>{{ $request->id }}</td>
                        <td>{{ optional($request->applicant)->name ?? Auth::user()->full_name }}</td>
                        <td>{{ $request->is_especial ? 'Campaña Especial' : 'Regular' }}</td>
                        <td>{{ $request->product_brand }}</td>
                        <td>{{ $request->work_title }}</td>

                        @if (Auth::user()->type == 'member')
                            {{-- Trámite finalizado --}}
                            @if ($request->status_id === 9)
                                @if ($request->approved)
                                <td class="text-center">Registro Aprobado</td>
                                @else
                                <td class="text-center">Registro Rechazado</td>
                                @endif
                                <td class="text-center"><a href="/{{ $user_type }}/jingles/{{ $request->id }}">Ver</a></td>
                            {{-- Si es el iniciador y todavía no se envió la solicitud, se puede editar --}}
                            @elseif ($request->member_id == Auth::user()->id && $request->status_id === null)
                                <td class="text-center">No enviada</td>
                                <td class="text-center"><a href="/{{ $user_type }}/jingles/{{ $request->id }}/edit">Editar</a></td>
                            {{-- Si es parte y está en proceso o en disputa, puede responder --}}
                            @elseif (Auth::user()->sadaic && $request->agreements->contains('member_idx', Auth::user()->sadaic->idx) && ($request->status_id == 2 || $request->status_id == 3) && $request->agreements->firstWhere('member_idx', Auth::user()->sadaic->idx)->response !== true)
                                <td class="text-center">{{ optional($request->status)->name }}</td>
                                <td class="text-center"><a href="/{{ $user_type }}/jingles/{{ $request->id }}/response">Responder</a></td>
                            {{-- Si no, no --}}
                            @else
                                <td class="text-center">{{ optional($request->status)->name }}</td>
                                <td class="text-center"><a href="/{{ $user_type }}/jingles/{{ $request->id }}">Ver</a></td>
                            @endif
                        @elseif (Auth::user()->type == 'user')
                            {{-- Trámite finalizado --}}
                            @if ($request->status_id === 9)
                                @if ($request->approved)
                                <td class="text-center">Registro Aprobado</td>
                                @else
                                <td class="text-center">Registro Rechazado</td>
                                @endif
                                <td class="text-center"><a href="/{{ $user_type }}/jingles/{{ $request->id }}">Ver</a></td>
                            {{-- Si es el iniciador y todavía no se envió la solicitud, se puede editar --}}
                            @elseif ($request->user_id == Auth::user()->id && $request->status_id === null)
                                <td class="text-center">No enviada</td>
                                <td class="text-center"><a href="/{{ $user_type }}/jingles/{{ $request->id }}/edit">Editar</a></td>
                            {{-- Si no, no --}}
                            @else
                                <td class="text-center">{{ optional($request->status)->name }}</td>
                                <td class="text-center"><a href="/{{ $user_type }}/jingles/{{ $request->id }}">Ver</a></td>
                            @endif
                        @endif
                    </tr>
                @endforeach
                </tbody>
            </table>
        </div>
    </div>
</div>
@endsection
