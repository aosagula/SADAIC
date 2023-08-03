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

@push('scripts')
<script>
@foreach($registration->agreements as $agreement)
@if ($agreement->type_id == 1)
    $peopleTable.row.add([
        '<span class="capitalize">{{ ucwords(strtolower($agreement->member->nombre)) }}</span>',
        '{{ $agreement->doc_number }}',
        '{{ $agreement->member->codanita }}',
        '<button type="button" class="btn btn-link removePerson"><i class="far fa-trash-alt"></i></button>',
        ''
    ]).node().id = '{{ $agreement->idx }}';

    customStorage.addPerson({
        type: 'member',
        member_id: '{{ $agreement->member->codanita }}',
        doc_number: '{{ $agreement->doc_number }}',
        idx: '{{ $agreement->member->idx }}'
    });
@else
    $peopleTable.row.add([
        '<span class="capitalize">{{ ucwords(strtolower($agreement->meta->name)) }}</span>',
        '{{ $agreement->doc_number }}',
        '',
        '<button type="button" class="btn btn-link removePerson"><i class="far fa-trash-alt"></i></button>',
        '<button type="button" class="btn btn-link editPerson" data-toggle="modal" data-target="#editPersonModal"><i class="far fa-edit"></i></button>'
    ]).node().id = '{{ $agreement->doc_number }}';

    customStorage.addPerson({
        type: 'no-member',
        doc_number: '{{ $agreement->doc_number }}',
        @if ($agreement->meta->address_country_id)
        address_country_id: '{{ $agreement->meta->address_country_id }}',
        @endif
        @if ($agreement->meta->address_state_id)
        address_state_id: {{ $agreement->meta->address_state_id }},
        @endif
        @if ($agreement->meta->address_state_text)
        address_state_text: '{{ $agreement->meta->address_state_text }}',
        @endif
        @if ($agreement->meta->address_city_id)
        address_city_id: {{ $agreement->meta->address_city_id }},
        @endif
        @if ($agreement->meta->address_city_text)
        address_city_text: '{{ $agreement->meta->address_city_text }}',
        @endif
        address_zip: '{{ $agreement->meta->address_zip }}',
        apartment: '{{ $agreement->meta->apartment }}',
        birth_country_id: '{{ $agreement->meta->birth_country_id }}',
        birth_date: '{{ optional($agreement->meta->birth_date)->format('Y-m-d') }}',
        doc_type: '{{ $agreement->meta->doc_type_id }}',
        email: '{{ $agreement->meta->email }}',
        floor: '{{ $agreement->meta->floor }}',
        name: '{{ $agreement->meta->name }}',
        phone_area: '{{ $agreement->meta->phone_area }}',
        phone_country: '{{ $agreement->meta->phone_country }}',
        phone_number: '{{ $agreement->meta->phone_number }}',
        street_name: '{{ $agreement->meta->street_name }}',
        street_number: '{{ $agreement->meta->street_number }}',
    });
@endif
@endforeach
$peopleTable.draw();
</script>
@endpush