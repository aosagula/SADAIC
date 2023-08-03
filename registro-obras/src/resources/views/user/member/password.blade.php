@extends('user.layout.dashboard')

@section('content')
<div class="container">
    <div class="row d-flex flex-row align-items-center">
        <div class="col col-12">
            <h2>Cambio de Clave</h2>
        </div>
    </div>

    <div class="row">
        <div class="col col-12">
            <p>Complete los datos obligatorios (*) para poder generar una nueva clave de acceso.</p>
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
            <form action="/user/member/password" method="POST">
                @csrf
                <div class="form-group row">
                    <label for="name" class="col-12 col-sm-3 col-form-label">Clave Actual *</label>
                    <div class="col-12 col-sm-9">
                        <input type="password" class="form-control" name="old_password" id="old_password" required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="name" class="col-12 col-sm-3 col-form-label">Nueva Clave *</label>
                    <div class="col-12 col-sm-9">
                        <input type="password" class="form-control" name="password" id="password" minlength="8"  required>
                    </div>
                </div>

                <div class="form-group row">
                    <label for="name" class="col-12 col-sm-3 col-form-label">Repetir Clave *</label>
                    <div class="col-12 col-sm-9">
                        <input type="password" class="form-control" name="password_confirmation" id="password_confirmation" minlength="8"  required>
                    </div>
                </div>

                <div class="form-group row">
                    <div class="col-12 text-center">
                        <button type="submit" class="btn btn-primary">Cambiar Clave</button>
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
