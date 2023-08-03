@extends('user.layout.dashboard')

@section('content')
<div class="container">
    <div class="row d-flex flex-row align-items-center">
        <div class="col col-9">
            <h2>Solicitud de Ingreso Representado Inscripto</h2>
        </div>
        <div class="col col-3 text-right">
            <h3><a href="{{ url('/user/member/list') }}">Volver</a></h3>
        </div>
    </div>
    <div class="row">
        <div class="col col-12">
            <p>Solicito a usted quiera someter a consideración del Directorio de su presidencia, mi solicitud de
            admisión a esa entidad - cuyos Estatutos, Reglamento Interno y disposiciones de la Ley 17648
            y su Decreto Reglamentario N º 5146/12-9-69, declaro conocer y aceptar-, en calidad de
            REPRESENTADO INSCRIPTO.</p>
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
        <form id="memberRegisterForm" action="/user/member/edit/{{ $request->id }}" method="POST">
                @csrf
                <div class="row"><div class="col text-center"><h3>Datos Personales</h3></div></div>
                <div class="form-group row">
                    <label for="name" class="col-12 col-sm-3 col-form-label">Apellido y Nombre</label>
                    <div class="col-12 col-sm-9">
                        <div class="form-control">{{ $request->name }}</div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="birth_date" class="col-12 col-sm-3 col-form-label">Fecha de Nacimiento</label>
                    <div class="col-12 col-sm-3">
                        <div class="form-control">{{ $request->birth_date->format('d/m/Y') }}</div>
                    </div>
                    <label for="birth_country_id" class="col-12 col-sm-3 col-form-label">País</label>
                    <div class="col-12 col-sm-3">
                        <div class="form-control">{{ $request->birth_country->name_ter  }}</div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="birth_state" class="col-12 col-sm-3 col-form-label">Provincia</label>
                    <div class="col-12 col-sm-3">
                        <div class="form-control">{{ $request->birth_state_id ? $request->birth_state->state : $request->birth_state_text }}</div>
                    </div>
                    <label for="birth_city" class="col-12 col-sm-3 col-form-label">Ciudad</label>
                    <div class="col-12 col-sm-3">
                        <div class="form-control">{{ $request->birth_city_id ? $request->birth_city->city : $request->birth_city_text }}</div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="doc_number" class="col-12 col-sm-3 col-form-label">DNI / Pasaporte N°</label>
                    <div class="col-12 col-sm-3">
                        <div class="form-control">{{ $request->doc_number }}</div>
                    </div>
                    <label for="doc_country" class="col-12 col-sm-3 col-form-label">Nacionalidad</label>
                    <div class="col-12 col-sm-3">
                        <div class="form-control">{{ $request->doc_country }}</div>
                    </div>
                    
                </div>
                <div class="form-group row">
                    <label for="work_code" class="col-12 col-sm-3 col-form-label">CUIT / CUIL / CDI N°</label>
                    <div class="col-12 col-sm-9">
                        <div class="form-control">{{ $request->work_code }}</div>
                    </div>
                </div>

                <div class="row"><div class="col text-center"><h3>Domicilio, Teléfonos y Correo Electrónico</h3></div></div>
                <div class="form-group row">
                    <label for="address_street" class="col-12 col-sm-3 col-form-label">Calle</label>
                    <div class="col-12 col-sm-5">
                        <div class="form-control">{{ $request->address_street }}</div>
                    </div>
                    <label for="address_number" class="col-12 col-sm-2 col-form-label">N°</label>
                    <div class="col-12 col-sm-2">
                        <div class="form-control">{{ $request->address_number }}</div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="address_floor" class="col-12 col-sm-3 col-form-label">Piso</label>
                    <div class="col-12 col-sm-3">
                        <div class="form-control">{{ $request->address_floor }}</div>
                    </div>
                    <label for="address_apt" class="col-12 col-sm-3 col-form-label">Depto</label>
                    <div class="col-12 col-sm-3">
                        <div class="form-control">{{ $request->address_apt }}</div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="address_country_id" class="col-12 col-sm-3 col-form-label">País</label>
                    <div class="col-12 col-sm-3">
                        <div class="form-control">{{ $request->address_country->name_ter }}</div>
                    </div>
                    <label for="address_state" class="col-12 col-sm-3 col-form-label">Provincia</label>
                    <div class="col-12 col-sm-3">
                        <div class="form-control">{{ $request->address_state_id ? $request->address_state->state : $request->address_state_text }}</div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="address_city" class="col-12 col-sm-3 col-form-label">Ciudad</label>
                    <div class="col-12 col-sm-3">
                        <div class="form-control">{{ $request->address_city_id ? $request->address_city->city : $request->address_city_text }}</div>
                    </div>
                    <label for="address_zip" class="col-12 col-sm-3 col-form-label">C.P.</label>
                    <div class="col-12 col-sm-3">
                        <div class="form-control">{{ $request->address_zip }}</div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="landline" class="col-12 col-sm-3 col-form-label">Teléfono***</label>
                    <div class="col-12 col-sm-3">
                        <div class="form-control">{{ $request->landline }}</div>
                    </div>
                    <label for="mobile" class="col-12 col-sm-3 col-form-label">Celular</label>
                    <div class="col-12 col-sm-3">
                        <div class="form-control">{{ $request->mobile }}</div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email" class="col-12 col-sm-3 col-form-label">Correo Electrónico</label>
                    <div class="col-12 col-sm-9">
                        <div class="form-control">{{ $request->email }}</div>
                    </div>
                </div>

                <div class="row"><div class="col text-center"><h3>Datos Complementarios</h3></div></div>
                <div class="form-group row">
                    <label for="pseudonym" class="col-12 col-sm-3 col-form-label">Seudónimo</label>
                    <div class="col-12 col-sm-3">
                        <div class="form-control">{{ $request->pseudonym }}</div>
                    </div>
                    <label for="band" class="col-12 col-sm-3 col-form-label">Grupo / Banda</label>
                    <div class="col-12 col-sm-3">
                        <div class="form-control">{{ $request->band }}</div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="entrance_work" class="col-12 col-sm-3 col-form-label">Obra de Ingreso</label>
                    <div class="col-12 col-sm-9">
                        <div class="form-control">{{ $request->entrance_work }}</div>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="genre_id" class="col-12 col-sm-3 col-form-label">Género Predominante en mis Obras</label>
                    <div class="col-12 col-sm-9">
                        <div class="form-control">{{ $request->genre->name }}</div>
                    </div>
                </div>
                <div class="row"><div class="col text-center">
                    <strong>Nota: la inclusión en el presente formulario de información sobre seudónimos o grupos
                    musicales no implica la aceptación por parte de SADAIC de los mismos</strong>
                </div></div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
.form-control {
    border: none;
    border-radius: 0;
    border-bottom: solid 1px #ccc;
}
</style>
@endpush