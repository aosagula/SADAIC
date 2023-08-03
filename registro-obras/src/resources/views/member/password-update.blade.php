@extends('member.layout.dashboard')

@section('content')

<div class="container">
    <div class="row d-flex flex-row align-items-center">
        <div class="col col-12">
            <h2>Cambio de clave</h2>
        </div>
    </div>
    @if(Session::has('message.type'))
    <div class="alert alert-{{ Session::get('message.type', 'secondary') }}" role="alert">
        {{ Session::get('message.data') }}
    </div>
    @endif
    <div class="row">
        <form class="col-md-6" action="/member/password/update" method="POST">
            @csrf
            <div class="form-group row">
                <label for="member_id" class="col-sm-3 col-form-label">Socio N°</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" name="member_id" id="member_id" disabled value="{{ $member_id }}">
                </div>
            </div>

            <div class="form-group row">
                <label for="heir" class="col-sm-3 col-form-label">Heredero N°</label>
                <div class="col-sm-9">
                    <input type="number" class="form-control" name="heir" id="heir" disabled value="{{ $heir }}">
                </div>
            </div>

            <div class="form-group row">
                <label for="oldPassword" class="col-sm-3 col-form-label">Clave actual</label>
                <div class="col-sm-9">
                    <input type="password" class="form-control" name="oldPassword" id="oldPassword">
                </div>
            </div>

            <div class="form-group row">
                <label for="newPassword" class="col-sm-3 col-form-label">Nueva Clave</label>
                <div class="col-sm-9">
                    <input type="password" class="form-control" name="newPassword" id="newPassword">
                </div>
            </div>

            <div class="form-group row">
                <label for="newPassword_confirmation" class="col-sm-3 col-form-label">Retipee la clave</label>
                <div class="col-sm-9">
                    <input type="password" class="form-control" name="newPassword_confirmation" id="newPassword_confirmation">
                </div>
            </div>

            <div class="text-right">
                <button type="submit" class="btn btn-primary active">Cambiar Clave</button>
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
