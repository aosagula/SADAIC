@extends(Auth::user()->type . '.layout.dashboard')

@section('content')
<div class="container">
    <div class="row d-flex flex-row align-items-center">
        <div class="col col-9">
            <h2>Solicitudes de Registro de Obra</h2>
        </div>
        <div class="col col-3 text-right">
            <h3><a href="{{ url('/' . Auth::user()->type . '/work/list') }}">Volver</a></h3>
        </div>
    </div>
    <div class="row">
        <div class="col col-12">
            <h3>Bienvenido al sistema de registro digital de obras</h3>
            <p>En esta pantalla, está iniciando el trámite de ingreso de una obra musical en SADAIC. Complete los datos obligatorios (*) para poder generar el Boletín de Declaración que deberá presentar en la Institución.</p>
        </div>
    </div>
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
        <div class="col col-12" id="generalSection">
            @include('wizard.section-general')
        </div>
    </div>
    <div class="row">
        <div class="col col-12">
            @include('wizard.section-people')
        </div>
    </div>
    <div class="row">
        <div class="col col-12">
            @include('wizard.section-attachments')
        </div>
    </div>

    @if (Auth::user()->type == 'user')
    <div class="row">
        <div class="col col-12 text-center">
            <div class="form-check form-check-inline mb-4">
                <input class="form-check-input" type="checkbox" value="1" name="do_ri" id="do_ri" {{ old('do_ri', '0') == 1 ? 'checked' : '' }}>
                <label class="form-check-label" for="do_ri">
                    Iniciar también Solicitud de Ingreso de Responsable Inscripto
                </label>
            </div>
        </div>
    </div>
    @endif

    <div class="row">
        <div class="col col-6 text-center">
            <button class="btn btn-secondary" id="saveRegister">Guardar solicitud</button>
        </div>
        <div class="col col-6 text-center">
            <button class="btn btn-primary" id="sendRegister">Enviar solicitud a SADAIC</button>
        </div>
    </div>
</div>
@endsection

@push('styles')
<style>
#memberSearchResults {
    padding-bottom: 1em;
}

#addAuthorModal .tab-content {
  overflow-y: auto;
}

#memberSearchResults ul {
    list-style-type: ' - ';
}

.capitalize {
    text-transform: capitalize;
}

#addAuthorModal .modal-content {
    min-height: 300px;
}

#memberSearchForm {
    height: 100px;
}

.tab-pane {
    padding-top: 1em;
    padding-bottom: 1em;
}

#distributionTable td {
    vertical-align: middle;
}

#distributionTable thead th {
    border-top: none;
}

input.amount {
    width: 5em;
}

.row + .row {
    margin-top: 2rem;
}

hr {
    margin-top: 0;
}

.dtr-details {
    width: 100%;
}

#distributionTable input[class$="Amount"] {
    width: auto;
    max-width: 100px;
}

#addPersonTabsContent {
    overflow-y: auto;
}
</style>
@endpush

@push('scripts')
<script>
window.maxFileUploadSize = {{ $max_size_b }};
window.maxFileUploadSizeFormatted = '{{ $max_size }}';
</script>
<script src="{{ asset('/js/work.register.js') }}"></script>
<script src="{{ asset('/js/file.uploader.js') }}"></script>
<script src="{{ asset('/js/city.selector.js') }}"></script>
<script>
    window.roleOptions = @json($roles);
    window.citiesOptions = @json($cities);

    $('#genre_id').change();
    @if (Auth::user()->type == 'member' && Auth::user()->sadaic)
    $distributionTable.row.add([
        selectFn('fn', true),
        '{{ Auth::user()->member_id }}',
        '<span class="capitalize">{{ optional(Auth::user()->profile)->name }}</span>',
        '',
        '<input type="number" class="form-control publicAmount" name="public" step="0.01" min="0" max="100" value="100">',
        '<input type="number" class="form-control mechanicAmount" name="mechanic" step="0.01" min="0" max="100" value="100">',
        '<input type="number" class="form-control syncAmount" name="sync" step="0.01" min="0" max="100" value="100">',
        '<button class="btn btn-link removePerson"><i class="far fa-trash-alt"></i></button>',
        ''
    ]).draw();

    customStorage.addPerson({
        type: 'member',
        member_id: '{{ Auth::user()->member_id }}',
        doc_number: '',
        public: 100,
        mechanic: 100,
        sync: 100
    });

    updateTotal();
    @endif
</script>
@endpush
