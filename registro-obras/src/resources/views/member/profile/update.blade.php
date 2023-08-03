@extends('member.layout.dashboard')

@section('content')
<div class="container">
    <div class="row d-flex flex-row align-items-center">
        <div class="col col-9">
            <h2>Solicitudes de Actualización de Datos</h2>
        </div>
        <div class="col col-3 text-right">
            <h3><a href="{{ url('/member/profile/list') }}">Volver</a></h3>
        </div>
    </div>
    @if(Session::has('message.type'))
    <div class="row">
        <div class="col-12 alert alert-{{ Session::get('message.type', 'secondary') }}" role="alert">
            {{ Session::get('message.data') }}
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
        <form class="col-12" action="/member/profile/update" method="POST">
            @csrf
            <div class="form-group row">
                <label for="member_id" class="col-sm-3 col-form-label">Socio N°</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" name="member_id" id="member_id" disabled value="{{ old('member_id', $member->member_id) }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="heir" class="col-sm-3 col-form-label">Heredero N°</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" name="heir" id="heir" disabled value="{{ old('heir', $member->heir) }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="email" class="col-sm-3 col-form-label">e-mail</label>
                <div class="col-sm-9">
                    <input type="email" class="form-control" name="email" id="email" value="{{ old('email', $member->email) }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="name" class="col-sm-3 col-form-label">Apellido y Nombre</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $profile->name) }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="address_type" class="col-sm-3 col-form-label">Carácter del Domicilio</label>
                <div class="col-sm-9">
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="address_type" id="address_type_main" value="Principal" @if(old('address_type', $profile->address_type) == 'Principal') checked @endif required>
                        <label class="form-check-label" for="address_type_main">Principal</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="address_type" id="address_type_legal" value="Legal" @if(old('address_type', $profile->address_type) == 'Legal') checked @endif>
                        <label class="form-check-label" for="address_type_legal">Legal</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="address_type" id="address_type_biz" value="Comercial" @if(old('address_type', $profile->address_type) == 'Comercial') checked @endif>
                        <label class="form-check-label" for="address_type_biz">Comercial</label>
                    </div>
                    <div class="form-check">
                        <input class="form-check-input" type="radio" name="address_type" id="address_type_work" value="Laboral" @if(old('address_type', $profile->address_type) == 'Laboral') checked @endif>
                        <label class="form-check-label" for="address_type_work">Laboral</label>
                    </div>
                </div>
            </div>
            <div class="form-group row">
                <label for="address" class="col-sm-3 col-form-label">Domicilio</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="address" id="address" value="{{ old('address', $profile->address) }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="address_zip" class="col-sm-3 col-form-label">Codigo Postal</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="address_zip" id="address_zip" value="{{ old('address_zip', $profile->address_zip) }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="address_city" class="col-sm-3 col-form-label">Localidad</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="address_city" id="address_city" value="{{ old('address_city', $profile->address_city) }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="address_state" class="col-sm-3 col-form-label">Provincia</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="address_state" id="address_state" value="{{ old('address_state', $profile->address_state) }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="address_country" class="col-sm-3 col-form-label">País</label>
                <div class="col-sm-9">
                    <input type="text" class="form-control" name="address_country" id="address_country" value="{{ old('address_country', $profile->address_country) }}">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Teléfono</label>
                <div class="form-group col-sm-3">
                    <label for="phone_country">Cod País</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">+</span>
                        </div>
                        <input type="number" class="form-control no-arrows" name="phone_country" id="phone_country" value="{{ old('phone_country', $profile->phone_country) }}">
                    </div>
                </div>
                <div class="form-group col-sm-3">
                    <label for="phone_area">Cod Área</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">(</span>
                        </div>
                        <input type="number" class="form-control no-arrows" name="phone_area" id="phone_area" value="{{ old('phone_area', $profile->phone_area) }}">
                        <div class="input-group-append">
                            <span class="input-group-text">)</span>
                        </div>
                    </div>
                </div>
                <div class="form-group col-sm-3">
                    <label for="phone_number">Número</label>
                    <input type="number" class="form-control no-arrows" name="phone_number" id="phone_number" value="{{ old('phone_number', $profile->phone_number) }}">
                </div>
            </div>
            <div class="form-group row">
                <label class="col-sm-3 col-form-label">Celular</label>
                <div class="form-group col-sm-3">
                    <label for="cell_country">Cod País</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">+</span>
                        </div>
                        <input type="number" class="form-control no-arrows" name="cell_country" id="cell_country" value="{{ old('cell_country', $profile->cell_country) }}">
                    </div>
                </div>
                <div class="form-group col-sm-3">
                    <label for="cell_area">Cod Área</label>
                    <div class="input-group">
                        <div class="input-group-prepend">
                            <span class="input-group-text">(</span>
                        </div>
                        <input type="number" class="form-control no-arrows" name="cell_area" id="cell_area" value="{{ old('cell_area', $profile->cell_area) }}">
                        <div class="input-group-append">
                            <span class="input-group-text">)</span>
                        </div>
                    </div>
                </div>
                <div class="form-group col-sm-3">
                    <label for="cell_number">Número</label>
                    <input type="number" class="form-control no-arrows" name="cell_number" id="cell_number" value="{{ old('cell_number', $profile->cell_number) }}">
                </div>
            </div>
            <div class="text-right">
                <button type="submit" class="btn btn-primary active ">Solicitar Actualización</button>
            </div>
        </form>
    </div>
</div>
@endsection

@push('scripts')
<script>
    $('form').on('submit', function(event) {
        $(event.originalEvent.submitter).attr('disabled', true);
    });
</script>
@endpush
