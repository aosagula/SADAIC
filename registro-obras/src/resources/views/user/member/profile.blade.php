@extends('user.layout.dashboard')

@section('content')
<div class="container">
    <div class="row d-flex flex-row align-items-center">
        <div class="col col-12">
            <h2>Actualización de Datos</h2>
        </div>
    </div>

    <div class="row">
        <div class="col col-12">
            <p>Complete los datos obligatorios (*) para poder actualizar los datos.</p>
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
            <form action="/user/member/profile" method="POST">
                @csrf

                <div class="form-group row">
                    <label for="name" class="col-12 col-sm-3 col-form-label">Apellido y Nombre *</label>
                    <div class="col-12 col-sm-9">
                        <input type="text" class="form-control" name="name" id="name" value="{{ old('name', $user->name) }}" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="phone" class="col-12 col-sm-3 col-form-label">Teléfono *</label>
                    <div class="col-12 col-sm-9">
                        <input type="tel" class="form-control" name="phone" id="phone" value="{{ old('phone', $user->phone) }}" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="email" class="col-12 col-sm-3 col-form-label">Correo Electrónico *</label>
                    <div class="col-12 col-sm-9">
                        <input type="text" class="form-control" name="email" id="email" value="{{ old('email', $user->email) }}" required>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary">Actualizar Datos</button>
                    </div>
                </div>
            </form>
        </div>
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
