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
    <div class="form-group row">
        <label for="member_id" class="col-sm-3 col-form-label">Socio N°</label>
        <div class="col-sm-9">
            <div class="form-control">{{ $update->member_id }}</div>
        </div>
    </div>
    <div class="form-group row">
        <label for="heir" class="col-sm-3 col-form-label">Heredero N°</label>
        <div class="col-sm-9">
            <div class="form-control">{{ $update->heir }}</div>
        </div>
    </div>
    <div class="form-group row">
        <label for="email" class="col-sm-3 col-form-label">e-mail</label>
        <div class="col-sm-9">
            <div class="form-control">{{ $update->email }}</div>
        </div>
    </div>
    <div class="form-group row">
        <label for="name" class="col-sm-3 col-form-label">Apellido y Nombre</label>
        <div class="col-sm-9">
            <div class="form-control">{{ $update->name }}</div>
        </div>
    </div>
    <div class="form-group row">
        <label for="address_type" class="col-sm-3 col-form-label">Carácter del Domicilio</label>
        <div class="col-sm-9">
            <div class="form-control">{{ $update->address_type }}</div>
        </div>
    </div>
    <div class="form-group row">
        <label for="address" class="col-sm-3 col-form-label">Domicilio</label>
        <div class="col-sm-9">
            <div class="form-control">{{ $update->address }}</div>
        </div>
    </div>
    <div class="form-group row">
        <label for="address_zip" class="col-sm-3 col-form-label">Codigo Postal</label>
        <div class="col-sm-9">
            <div class="form-control">{{ $update->address_zip }}</div>
        </div>
    </div>
    <div class="form-group row">
        <label for="address_city" class="col-sm-3 col-form-label">Localidad</label>
        <div class="col-sm-9">
            <div class="form-control">{{ $update->address_city }}</div>
        </div>
    </div>
    <div class="form-group row">
        <label for="address_state" class="col-sm-3 col-form-label">Provincia</label>
        <div class="col-sm-9">
            <div class="form-control">{{ $update->address_state }}</div>
        </div>
    </div>
    <div class="form-group row">
        <label for="address_country" class="col-sm-3 col-form-label">País</label>
        <div class="col-sm-9">
            <div class="form-control">{{ $update->address_country }}</div>
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
                <div class="form-control">{{ $update->phone_country }}</div>
            </div>
        </div>
        <div class="form-group col-sm-3">
            <label for="phone_area">Cod Área</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">(</span>
                </div>
                <div class="form-control">{{ $update->phone_area }}</div>
                <div class="input-group-append">
                    <span class="input-group-text">)</span>
                </div>
            </div>
        </div>
        <div class="form-group col-sm-3">
            <label for="phone_number">Número</label>
            <div class="form-control">{{ $update->phone_number }}</div>
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
                <div class="form-control">{{ $update->cell_country }}</div>
            </div>
        </div>
        <div class="form-group col-sm-3">
            <label for="cell_area">Cod Área</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">(</span>
                </div>
                <div class="form-control">{{ $update->cell_area }}</div>
                <div class="input-group-append">
                    <span class="input-group-text">)</span>
                </div>
            </div>
        </div>
        <div class="form-group col-sm-3">
            <label for="cell_number">Número</label>
            <div class="form-control">{{ $update->cell_number }}</div>
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