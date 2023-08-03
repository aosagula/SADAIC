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
            REPRESENTADO INSCRIPTO. Complete los datos obligatorios (*) para poder generar el Boletín de Declaración que deberá presentar en la Institución.</p>
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
                    <label for="name" class="col-12 col-sm-3 col-form-label">Apellido y Nombre *</label>
                    <div class="col-12 col-sm-9">
                        <input type="text" class="form-control" name="name" id="name" maxlength="255" value="{{ old('name', $request->name) }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="birth_date" class="col-12 col-sm-3 col-form-label">Fecha de Nacimiento *</label>
                    <div class="col-12 col-sm-3">
                        <input type="date" placeholder="__/__/____" class="form-control" name="birth_date" id="birth_date" value="{{ old('birth_date', $request->birth_date) }}" max="9999-12-31">
                    </div>
                    <label for="birth_country_id" class="col-12 col-sm-3 col-form-label">País *</label>
                    <div class="col-12 col-sm-3">
                        <select class="custom-select birth_country_id" name="birth_country_id" required>
                        @foreach($countries as $country)
                            @if (old('birth_country_id', $request->birth_country_id) == $country->idx)
                            <option value="{{ $country->idx }}" selected>{{ $country->name_ter }}</option>
                            @else
                            <option value="{{ $country->idx }}">{{ $country->name_ter }}</option>
                            @endif
                        @endforeach
                        </select>
                    </div>
                </div>
                <div class="form-group row">
                    <label for="birth_state" class="col-12 col-sm-3 col-form-label">Provincia *</label>
                    <div class="col-12 col-sm-3">
                    <select class="custom-select birth_state_id" name="birth_state_id" required>
                            <option value=""></option>
                            @foreach($states as $state)
                            <option value="{{ $state->id }}">{{ $state->state }}</option>
                            @endforeach
                        </select>
                        <input type="text" class="form-control birth_state_text" name="birth_state_text" maxlength="50" value="{{ old('birth_state_text', $request->birth_state_text) }}" style="display: none" required />
                    </div>
                    <label for="birth_city" class="col-12 col-sm-3 col-form-label">Ciudad *</label>
                    <div class="col-12 col-sm-3">
                        <select class="custom-select birth_city_id" name="birth_city_id" required></select>
                        <input type="text" class="form-control birth_city_text" name="birth_city_text" maxlength="50" value="{{ old('birth_city_text', $request->birth_city_text) }}" style="display: none" required />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="doc_number" class="col-12 col-sm-3 col-form-label">DNI / Pasaporte N° *</label>
                    <div class="col-12 col-sm-3">
                        <input type="text" class="form-control" name="doc_number" id="doc_number" maxlength="50" value="{{ old('doc_number', $request->doc_number) }}">
                    </div>
                    <label for="doc_country" class="col-12 col-sm-3 col-form-label">Nacionalidad *</label>
                    <div class="col-12 col-sm-3">
                        <input type="text" class="form-control" name="doc_country" id="doc_country" maxlength="50" value="{{ old('doc_country', $request->doc_country) }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="work_code" class="col-12 col-sm-3 col-form-label">CUIT / CUIL / CDI N°</label>
                    <div class="col-12 col-sm-9">
                        <input type="text" class="form-control" name="work_code" id="work_code" maxlength="20" value="{{ old('work_code', $request->work_code) }}">
                    </div>
                </div>

                <div class="row"><div class="col text-center"><h3>Domicilio, Teléfonos y Correo Electrónico</h3></div></div>
                <div class="form-group row">
                    <label for="address_street" class="col-12 col-sm-3 col-form-label">Calle *</label>
                    <div class="col-12 col-sm-5">
                        <input type="text" class="form-control" name="address_street" id="address_street" maxlength="255" value="{{ old('address_street', $request->address_street) }}">
                    </div>
                    <label for="address_number" class="col-12 col-sm-2 col-form-label">N° *</label>
                    <div class="col-12 col-sm-2">
                        <input type="text" class="form-control" name="address_number" id="address_number" maxlength="20" value="{{ old('address_number', $request->address_number) }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="address_floor" class="col-12 col-sm-3 col-form-label">Piso</label>
                    <div class="col-12 col-sm-3">
                        <input type="text" class="form-control" name="address_floor" id="address_floor" maxlength="10" value="{{ old('address_floor', $request->address_floor) }}">
                    </div>
                    <label for="address_apt" class="col-12 col-sm-3 col-form-label">Depto</label>
                    <div class="col-12 col-sm-3">
                        <input type="text" class="form-control" name="address_apt" id="address_apt" maxlength="10" value="{{ old('address_apt', $request->address_apt) }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="address_country_id" class="col-12 col-sm-3 col-form-label">País *</label>
                    <div class="col-12 col-sm-3">
                        <select class="custom-select address_country_id" name="address_country_id" required>
                            @foreach($countries as $country)
                            <option value="{{ $country->idx }}">{{ $country->name_ter }}</option>
                            @endforeach
                        </select>
                    </div>
                    <label for="address_state" class="col-12 col-sm-3 col-form-label">Provincia *</label>
                    <div class="col-12 col-sm-3">
                        <select class="custom-select address_state_id" name="address_state_id" required>
                            <option value="" selected></option>
                            @foreach($states as $state)
                            <option value="{{ $state->id }}">{{ $state->state }}</option>
                            @endforeach
                        </select>
                        <input type="text" class="form-control address_state_text" name="address_state_text" maxlength="50" value="{{ old('address_state_text', $request->address_state_text) }}" style="display: none" required />
                    </div>
                </div>
                <div class="form-group row">
                    <label for="address_city" class="col-12 col-sm-3 col-form-label">Ciudad *</label>
                    <div class="col-12 col-sm-3">
                        <select class="custom-select address_city_id" name="address_city_id" required></select>
                        <input type="text"class="form-control address_city_text" name="address_city_text" maxlength="50" value="{{ old('address_city_text', $request->address_city_text) }}" style="display: none" required />
                    </div>
                    <label for="address_zip" class="col-12 col-sm-3 col-form-label">C.P.</label>
                    <div class="col-12 col-sm-3">
                        <input type="text" class="form-control" name="address_zip" id="address_zip" maxlength="10" value="{{ old('address_zip', $request->address_zip) }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="landline" class="col-12 col-sm-3 col-form-label">Teléfono *</label>
                    <div class="col-12 col-sm-3">
                        <input type="tel" class="form-control" name="landline" id="landline" maxlength="15" value="{{ old('landline', $request->landline) }}">
                    </div>
                    <label for="mobile" class="col-12 col-sm-3 col-form-label">Celular *</label>
                    <div class="col-12 col-sm-3">
                        <input type="tel" class="form-control" name="mobile" id="mobile" maxlength="15" value="{{ old('mobile', $request->mobile) }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="email" class="col-12 col-sm-3 col-form-label">Correo Electrónico *</label>
                    <div class="col-12 col-sm-9">
                        <input type="email" class="form-control" name="email" id="email" maxlength="254" value="{{ old('email', $request->email) }}">
                    </div>
                </div>

                <div class="row"><div class="col text-center"><h3>Datos Complementarios</h3></div></div>
                <div class="form-group row">
                    <label for="pseudonym" class="col-12 col-sm-3 col-form-label">Seudónimo *</label>
                    <div class="col-12 col-sm-3">
                        <input type="text" class="form-control" name="pseudonym" id="pseudonym" maxlength="255" value="{{ old('pseudonym', $request->pseudonym) }}">
                    </div>
                    <label for="band" class="col-12 col-sm-3 col-form-label">Grupo / Banda</label>
                    <div class="col-12 col-sm-3">
                        <input type="text" class="form-control" name="band" id="band" maxlength="255" value="{{ old('band', $request->band) }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="entrance_work" class="col-12 col-sm-3 col-form-label">Obra de Ingreso</label>
                    <div class="col-12 col-sm-9">
                        <input type="text" class="form-control" name="entrance_work" id="entrance_work" maxlength="255" value="{{ old('entrance_work', $request->entrance_work) }}">
                    </div>
                </div>
                <div class="form-group row">
                    <label for="genre_id" class="col-12 col-sm-3 col-form-label">Género Predominante en mis Obras</label>
                    <div class="col-12 col-sm-9">
                        <select class="custom-select" name="genre_id" id="genre_id">
                        @foreach($genres as $genre)
                            @if (old('genre_id', $request->genre_id) == $genre->id)
                            <option value="{{ $genre->id }}" selected>{{ $genre->name }}</option>
                            @else
                            <option value="{{ $genre->id }}">{{ $genre->name }}</option>
                            @endif
                        @endforeach
                        </select>
                    </div>
                </div>
                <div class="row"><div class="col text-center">
                    <strong>Nota: la inclusión en el presente formulario de información sobre seudónimos o grupos
                    musicales no implica la aceptación por parte de SADAIC de los mismos</strong>
                </div></div>
                <br>
                <div class="row"><div class="col text-right">
                    <button class="btn btn-primary" type="submit">Actualizar Solicitud</button>
                </div></div>
            </form>
        </div>
    </div>
</div>
@endsection

@push('scripts')
<script src="{{ asset('/js/city.selector.js') }}"></script>
<script>
    const citiesOptions = @json($cities);
    const statesOptions = @json($states);
</script>
<script>
    $('.birth_country_id').val('{{ old('birth_country_id', $request->birth_country_id) }}');
    $('.birth_country_id').change();

    $('.birth_state_id').val({{ old('birth_state_id', $request->birth_state_id) }});
    $('.birth_state_id').change();

    $('.birth_city_id').val({{ old('birth_city_id', $request->birth_city_id) }});

    $('.address_country_id').val('{{ old('address_country_id', $request->address_country_id) }}');
    $('.address_country_id').change();

    $('.address_state_id').val({{ old('address_state_id', $request->address_state_id) }});
    $('.address_state_id').change();

    $('.address_city_id').val({{ old('address_city_id', $request->address_city_id) }});

    // Solo IE11
    if (navigator.userAgent.indexOf('Trident/7.0') !== -1) {
        // Corregimos las fechas
        $('#birth_date').each(function(i, e) {
            $(e).val( $(e).val().split('-').reverse().join('/') );
        });

        // Enmascaramos los datepicker
        $('#birth_date').mask('00/00/0000', {
            clearIfNotMatch: true,
            selectOnFocus: true
        });
    }

    $('#memberRegisterForm').on('submit', function() {
        $('#birth_date').val( $('#birth_date').val().split('/').reverse().join('-') );
    });
</script>
@endpush
