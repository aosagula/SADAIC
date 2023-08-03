@extends('member.layout.dashboard')

@section('content')
<div class="container">
    <div class="row d-flex flex-row align-items-center">
        <div class="col col-9">
            <h2>Registro de Obra: {{ $registration->title }}</h2>
        </div>
        <div class="col col-3 text-right">
            <h3><a href="{{ url('/member/jingles') }}">Volver</a></h3>
        </div>
    </div>
    @if(session('message.type'))
    <div class="row">
        <div class="alert alert-{{ session('message.type') }} col-md-12 col-lg-10 offset-lg-1">
            {{ session('message.data') }}
        </div>
    </div>
    @endif
    @include('components.jingle-view')
    @php
        $response = $registration->agreements->where('member_id', Auth::user()->member_id)->first()->response;
    @endphp
    @if (!$response)
        @if ($response !== null)
        <div class="row pt-4">
            <div class="col-12 text-center">
                <h3>Trámite ya rechazado</h3>
                <small>Aunque se realizó el rechazo del trámite, todavía tiene la posibilidad de cambiar la respuesta.</small>
            </div>
        </div>
        <div class="row d-flex flex-row justify-content-around pt-4">
            <button class="btn btn-primary" id="acceptDistribution">Aceptar</button>
        </div>
        @else
        <div class="row d-flex flex-row justify-content-around pt-4">
            <button class="btn btn-primary" id="acceptDistribution">Aceptar</button>
            <button class="btn btn-danger" id="rejectDistribution">Rechazar</button>
        </div>
        @endif
    @else
        <div class="row pt-4">
            <div class="col-12 text-center">
                <h3>Trámite ya aceptado</h3>
                <small>Una vez que se realizó la aceptación del trámite, la respuesta no se puede cambiar.</small>
            </div>
        </div>
    @endif
</div>
@endsection

@push('scripts')
<script>
window.onload = function() {
    $('#acceptDistribution').on('click', () => {
        axios.post('/member/jingles/{{ $registration->id }}/response', {
            response: 'accept'
        })
        .catch((err) => {
            toastr.error('Se encontró un problema mientras se realizaba la solicitud')
        })
        .then(({ data }) => {
            if (data.status == 'failed') {
                toastr.error('No se puedo registrar la aceptación del trámite.');

                data.errors.forEach(e => {
                    toastr.warning(e);
                });
            } else if (data.status == 'success') {
                toastr.success('Se registró correctamente la aceptación del trámite.');
                setTimeout(() => { location.reload() }, 1000);
            }
        });
    });

    $('#rejectDistribution').on('click', () => {
        axios.post('/member/jingles/{{ $registration->id }}/response', {
            response: 'reject'
        })
        .catch((err) => {
            toastr.error('Se encontró un problema mientras se realizaba la solicitud')
        })
        .then(({ data }) => {
            if (data.status == 'failed') {
                toastr.error('No se puedo registrar el rechazo del trámite.');

                data.errors.forEach(e => {
                    toastr.warning(e);
                });
            } else if (data.status == 'success') {
                toastr.success('Se registró correctamente el rechazo del trámite');
                setTimeout(() => { location.reload() }, 1000);
            }
        });
    });
}
</script>
@endpush