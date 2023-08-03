<!doctype html>
<html lang="{{ str_replace('_', '-', app()->getLocale()) }}">

<head>
    <meta charset="utf-8">
    <meta name="viewport" content="width=device-width, initial-scale=1">

    <meta name="csrf-token" content="{{ csrf_token() }}">

    <title>{{ config('app.name', 'SADAIC') }}</title>

    <link href="{{ asset('css/app.css') }}" rel="stylesheet">
    <style>
    #verify button[type="submit"] {
        transform: none;
    }

    .badge {
        top: 20vh !important;
    }
    </style>
</head>
<body style="overflow: hidden">
<div id="members" class="step2">
    <div class="left-side">
        <img src="{{ asset('/images/logo-white.png') }}" class="logo" />
        <p><strong>Ingresa tus datos</strong> para poder recuperar
        tu clave del sistema de administración.</p>
        <span class="goBack">&#8592; Volver al inicio</span>
    </div>
    <div class="content">
        <div class="badge">
            <img src="{{ asset('/images/icons/members.png') }}">
        </div>
        <div class="header">
            <strong>SOCIOS</strong><br>
            RECUPERAR CLAVE<br>
            <img src="{{ asset('images/line.png' )}}">
        </div>
        <div class="formWrapper">
            <form method="POST" action="{{ url('/password/email') }}">
                @csrf

                <div class="formInput">
                    <label for="member_id">
                        <img src="{{ asset('images/icons/members-input.png' )}}">
                    </label>

                    <input id="member_id" type="member_id"
                        @error('member_id') class=" is-invalid" @enderror
                        name="member_id" value="{{ old('member_id') }}" required
                        autocomplete="member_id" autofocus placeholder="Socio">
                </div>

                <div class="formInput">
                    <label for="heir">
                        <img src="{{ asset('images/icons/heir-input.png' )}}">
                    </label>

                    <input id="heir" type="heir"
                        @error('heir') class="is-invalid" @enderror name="heir"
                        required autocomplete="heir" autofocus placeholder="Heredero" value="0">
                </div>

                <div class="formInput">
                    <label for="password">
                        <img src="{{ asset('images/icons/members-input.png' )}}">
                    </label>

                    <input class="password" type="password"
                        @error('password') class="is-invalid" @enderror name="password"
                        required autocomplete="current-email" placeholder="Nueva clave">
                </div>

                <div class="form-group row">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-custom">RECUPERAR</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="players" class="step2">
    <div class="left-side">
        <img src="{{ asset('/images/logo-white.png') }}" class="logo" />
        <p><strong>Ingresa tus datos</strong> para poder recuperar
        tu clave del sistema de administración.</p>
        <span class="goBack">&#8592; Volver al inicio</span>
    </div>
    <div class="content">
        <div class="badge">
            <img src="{{ asset('/images/icons/players.png') }}">
        </div>
        <div class="header">
            <strong>INTÉRPRETES</strong><br>
            RECUPERAR CLAVE<br>
            <img src="{{ asset('images/line.png' )}}">
        </div>
        <div class="formWrapper">
            <form method="POST" action="{{ url('/password/email') }}">
                @csrf

                <div class="formInput">
                    <label for="player_id">
                        <img src="{{ asset('images/icons/members-input.png' )}}">
                    </label>

                    <input id="player_id" type="player_id"
                        @error('player_id') class=" is-invalid" @enderror
                        name="player_id" value="{{ old('player_id') }}" required
                        autocomplete="player_id" autofocus placeholder="Intérprete">
                </div>

                <div class="formInput">
                    <label for="email">
                        <img src="{{ asset('images/icons/members-input.png' )}}">
                    </label>

                    <input type="email"
                        @error('email') class="email is-invalid" @enderror
                        name="email" value="{{ old('email') }}" required
                        autocomplete="email" autofocus placeholder="Correo">
                </div>

                <div class="form-group row">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-custom">RECUPERAR</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>

<div id="users" class="step2">
    <div class="left-side">
        <img src="{{ asset('/images/logo-white.png') }}" class="logo" />
        <p><strong>Ingresa tus datos</strong> para poder recuperar
        tu clave del sistema de administración.</p>
        <span class="goBack">&#8592; Volver al inicio</span>
    </div>
    <div class="content">
        <div class="badge">
            <img src="{{ asset('/images/icons/users.png') }}">
        </div>
        <div class="header">
            <strong>USUARIOS</strong><br>
            RECUPERAR CLAVE<br>
            <img src="{{ asset('images/line.png' )}}">
        </div>
        <div class="formWrapper">
            <form method="POST" action="{{ route('password.update') }}">
                @csrf

                <input type="hidden" name="token" value="{{ $token }}">

                <div class="formInput">
                    <label for="email">
                        <img src="{{ asset('images/icons/members-input.png' )}}">
                    </label>

                    <input id="email" type="email"
                        @error('email') class=" is-invalid" @enderror
                        name="email" value="{{ $email ?? old('email') }}" required
                        autocomplete="email" autofocus placeholder="Correo">
                </div>

                <div class="formInput">
                    <label for="password">
                        <img src="{{ asset('images/icons/members-input.png' )}}">
                    </label>

                    <input type="password"
                        @error('password') class="password is-invalid" @enderror
                        name="password" value="{{ old('password') }}" required
                        autocomplete="password" autofocus placeholder="Nueva clave">
                </div>

                <div class="formInput">
                    <label for="password">
                        <img src="{{ asset('images/icons/members-input.png' )}}">
                    </label>

                    <input type="password"
                        @error('password') class="password is-invalid" @enderror
                        name="password_confirmation" value="{{ old('password') }}" required
                        autocomplete="password" autofocus placeholder="Ingrese nuevamente la clave">
                </div>

                <div class="form-group row">
                    <div class="col-md-12 text-center">
                        <button type="submit" class="btn btn-custom">RESETEAR</button>
                    </div>
                </div>
            </form>
        </div>
    </div>
</div>
<script src="{{ asset('/js/app.js') }}"></script>
<script>
    $('.goBack').on('click', () => {
        window.location = '/login' + window.location.hash;
    });

    $('#members form').on('submit', (event) => {
        axios.post('{{ url('/password/email') }}', $('#members form').serialize())
        .then((response) => {
            if (response.data.status == 'success') {
                toastr.info('Se envió un mail a la casilla de correo electrónico con instrucciones para continuar el proceso.');
            } else {
                for (const attr in response.data.errors) {
                    toastr.warning(response.data.errors[attr]);
                }
            }
        })
        .catch(({ response }) => {
            if (response.status == 422) {
                for (const attr in response.data.errors) {
                    toastr.warning(response.data.errors[attr]);
                }
            }
        });

        event.preventDefault();
    });

    $('#players form').on('submit', (event) => {
        axios.post('{{ url('/password/email') }}', $('#players form').serialize())
        .then((response) => {
            if (response.data.status == 'success') {
                toastr.info('Se envió un mail a la casilla de correo electrónico con instrucciones para continuar el proceso.');
            } else {
                for (const attr in response.data.errors) {
                    toastr.warning(response.data.errors[attr]);
                }
            }
        })
        .catch(({ response }) => {
            if (response.status == 422) {
                for (const attr in response.data.errors) {
                    toastr.warning(response.data.errors[attr]);
                }
            }
        });

        event.preventDefault();
    });

    $('#users form').on('submit', (event) => {
        axios.post('{{ route('password.update') }}', $('#users form').serialize())
        .then((response) => {
            if (response.data.status == 'success') {
                toastr.info('Se cambió correctamente la clave.');
                setTimeout(() => { window.location = '/user' }, 1000);
            } else {
                toastr.warning('Se produjo un error cuando se estaba cambiando la clave. Por favor, intente nuevamente más tarde.');
            }
        })
        .catch(({ response }) => {
            if (response.status == 422) {
                for (const attr in response.data.errors) {
                    toastr.warning(response.data.errors[attr]);
                }
            }
        });

        event.preventDefault();
    });

    if (window.location.hash) {
        if($(window).width() < 767) {
            $(window.location.hash).toggle();
        } else {
            $(window.location.hash).css('display', 'flex');
        }
        $('html, body').animate({
            scrollTop: $(window.location.hash).offset().top
        }, 500);
    }
</script>
</body>
</html>
