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
            <h3>Bienvenido al sistema de registro digital para obras de música</h3>
            <p>En esta pantalla, está iniciando el trámite de ingreso de una obra musical en SADAIC.
            Complete los datos obligatorios (*) para poder generar el Boletín de Declaración que deberá presentar en la
            Institución.</p>
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
    <div id="generalSection">
        <div class="form-group">
            <label for="title">Título *</label>
            <input type="text" class="form-control" name="title" id="title" required value="{{ $request->title }}">
        </div>

        <div class="form-row">
            <div class="form-group col-md-9">
                <label for="genre_id">Género *</label>
                <select class="custom-select" id="genre_id" name="genre_id" required>
                @foreach($genres as $genre)
                    @if ($genre->id == $request->genre_id)
                        <option value="{{ $genre->id }}" selected>{{ $genre->name }}</option>
                    @else
                        <option value="{{ $genre->id }}">{{ $genre->name }}</option>
                    @endif
                @endforeach
                </select>
            </div>
            <div class="form-group col-md-3">
                <label for="duration">Duración Aproximada *</label>
                <input type="text" class="form-control" name="duration" id="duration" required value="{{ $request->duration }}">
            </div>
        </div>

        <div class="form-group">
            <label for="title">Títulos alternativos</label><br>
            @foreach(old('alternative_titles', $request->titles) as $title)
            <div class="input-group mb-3">
                <input type="text" class="form-control alternative_titles" placeholder="" name="alternative_titles[]" value="{{ $title->title }}">
                <div class="input-group-append">
                    <button class="btn btn-outline-secondary delete_alternative_title" type="button">Borrar</button>
                </div>
            </div>
            @endforeach
            <button type="button" class="btn btn-primary" id="addAltTitle">Agregar títtulo alternativo</button>
        </div>

        <div class="form-group">
            <label for="dnda_title">Título Álbum</label>
            <input type="text" class="form-control" name="dnda_title" id="dnda_title" value="{{ $request->dnda_title }}">
        </div>

        <div class="form-group">
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="is_jingle" value="1" {{ $request->is_jingle ? 'checked' : '' }}>
                <label class="form-check-label" for="is_jingle">Música en Publicidad</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="is_jingle" value="2" {{ $request->is_movie ? 'checked' : '' }}>
                <label class="form-check-label" for="is_movie">Música en Producción Audiovisual</label>
            </div>
            <div class="form-check form-check-inline">
                <input class="form-check-input" type="radio" name="is_jingle" value="3" {{ $request->is_regular ? 'checked' : '' }}>
                <label class="form-check-label" for="is_regular">Música Regular</label>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="lyric_dnda_in_file">N° Expediente DNDA Inédita (Letra)</label>
                <input type="text" class="form-control" name="lyric_dnda_in_file" id="lyric_dnda_in_file" maxlength="64" value="{{ $request->lyric_dnda_in_file }}">
            </div>
            <div class="form-group col-md-4">
                <label for="audio_dnda_in_file">N° Expediente DNDA Inédita (Musica)</label>
                <input type="text" class="form-control" name="audio_dnda_in_file" id="audio_dnda_in_file" maxlength="64" value="{{ $request->audio_dnda_in_file }}">
            </div>
            <div class="form-group col-md-4">
                <label for="dnda_in_date">Fecha Inédita</label>
                <input type="date" placeholder="__/__/____" class="form-control" name="dnda_in_date" id="dnda_in_date" value="{{ $request->dnda_in_date ? $request->dnda_in_date->format('Y-m-d') : '' }}" max="{{ now()->format('Y-m-d') }}">
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="lyric_dnda_ed_file">N° Expediente DNDA Editada (Letra)</label>
                <input type="text" class="form-control" name="lyric_dnda_ed_file" id="lyric_dnda_ed_file" maxlength="64" value="{{ $request->lyric_dnda_ed_file }}">
            </div>
            <div class="form-group col-md-4">
                <label for="audio_dnda_ed_file">N° Expediente DNDA Editada (Musica)</label>
                <input type="text" class="form-control" name="audio_dnda_ed_file" id="audio_dnda_ed_file" maxlength="64" value="{{ $request->audio_dnda_ed_file }}">
            </div>
            <div class="form-group col-md-4">
                <label for="dnda_ed_date">Fecha Editada</label>
                <input type="date" placeholder="__/__/____" class="form-control" name="dnda_ed_date" id="dnda_ed_date" value="{{ $request->dnda_ed_date ? $request->dnda_ed_date->format('Y-m-d') : '' }}" max="{{ now()->format('Y-m-d') }}">
            </div>
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

h2 {
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

#attachmentsTable_wrapper thead {
    display: none;
}

#attachmentsTable_wrapper td {
    width: 100%;
}

@media (min-width: 576px) {
    #attachmentsTable_wrapper thead {
        display: table-header-group;
    }

    #attachmentsTable_wrapper td {
        width: 50%;
    }
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
    window.roleOptions = {!! json_encode($roles) !!}
    window.citiesOptions = {!! json_encode($cities) !!}
</script>
<script>
customStorage.setField('registration_id', {{ $request->id }});
@if (isset($request->genre_id))
    customStorage.setField('genre_id', {{ $request->genre_id }});
@else
    customStorage.setField('genre_id', parseInt($('#genre_id').val()));
@endif
@isset ($request->title)
    customStorage.setField('title', '{{ $request->title }}');
@endisset
@isset ($request->dnda_title)
    customStorage.setField('dnda_title', '{{ $request->dnda_title }}');
@endisset
@isset ($request->duration)
    customStorage.setField('duration', '{{ $request->duration }}');
@endisset
@isset ($request->dnda_ed_date)
    customStorage.setField('dnda_ed_date', '{{ $request->dnda_ed_date->format('Y-m-d') }}');
@endisset
@isset ($request->audio_dnda_ed_file)
    customStorage.setField('audio_dnda_ed_file', '{{ $request->audio_dnda_ed_file }}');
@endisset
@isset ($request->lyric_dnda_ed_file)
    customStorage.setField('lyric_dnda_ed_file', '{{ $request->lyric_dnda_ed_file }}');
@endisset
@isset ($request->dnda_in_date)
    customStorage.setField('dnda_in_date', '{{ $request->dnda_in_date->format('Y-m-d') }}');
@endisset
@isset ($request->audio_dnda_in_file)
    customStorage.setField('audio_dnda_in_file', '{{ $request->audio_dnda_in_file }}');
@endisset
@isset ($request->lyric_dnda_in_file)
    customStorage.setField('lyric_dnda_in_file', '{{ $request->lyric_dnda_in_file }}');
@endisset

@isset ($distribution)
    @foreach ($distribution as $current)
    customStorage.addPerson({
        distribution_id: {{ $current->id }},
        type: '{{ $current->type }}',
        @if ($current->fn)
        fn: '{{ $current->fn }}',
        @endif
        @if ($current->type == 'member')
        member_id: '{{ $current->member_id }}',
        @endif
        doc_number: '{{ $current->doc_number }}',
        public: {{ $current->public }},
        mechanic: {{ $current->mechanic }},
        sync: {{ $current->sync }},
        @if ($current->type == 'no-member')
        @if ($current->meta->address_country_id)
        address_country_id: '{{ $current->meta->address_country_id }}',
        @endif
        @if ($current->meta->address_state_id)
        address_state_id: {{ $current->meta->address_state_id }},
        @endif
        @if ($current->meta->address_state_text)
        address_state_text: '{{ $current->meta->address_state_text }}',
        @endif
        @if ($current->meta->address_city_id)
        address_city_id: {{ $current->meta->address_city_id }},
        @endif
        @if ($current->meta->address_city_text)
        address_city_text: '{{ $current->meta->address_city_text }}',
        @endif
        address_zip: '{{ $current->meta->address_zip }}',
        apartment: '{{ $current->meta->apartment }}',
        birth_country_id: '{{ $current->meta->birth_country_id }}',
        birth_date: '{{ $current->meta->birth_date }}',
        doc_type: '{{ $current->meta->doc_type }}',
        email: '{{ $current->meta->email }}',
        floor: '{{ $current->meta->floor }}',
        name: '{{ $current->meta->name }}',
        phone_area: '{{ $current->meta->phone_area }}',
        phone_country: '{{ $current->meta->phone_country }}',
        phone_number: '{{ $current->meta->phone_number }}',
        street_name: '{{ $current->meta->street_name }}',
        street_number: '{{ $current->meta->street_number }}',
        @endif
    });

    $distributionTable.row.add([
        @if ($current->fn != -1)
        selectFn('fn', false, "{{ $current->fn }}"),
        @else
        selectFn('fn', true),
        @endif
        '{{ $current->type == 'member' ? $current->member_id : '' }}',
        '<span class="capitalize">{{ $current->type == 'member' ? ucwords(strtolower(optional($current->member)->nombre)) : optional($current->meta)->name }}</span>',
        '{{ $current->doc_number }}',
        '<input type="number" class="form-control publicAmount" name="public" step="0.01" min="0" max="100" value="{{ $current->public }}">',
        '<input type="number" class="form-control mechanicAmount" name="mechanic" step="0.01" min="0" max="100" value="{{ $current->mechanic }}">',
        '<input type="number" class="form-control syncAmount" name="sync" step="0.01" min="0" max="100" value="{{ $current->sync }}">',
        '<button class="btn btn-link removePerson"><i class="far fa-trash-alt"></i></button>',
        '{!! $current->type == 'no-member' ? '<button class="btn btn-link editPerson" data-toggle="modal" data-target="#editPersonModal"><i class="far fa-edit"></i></button>' : '' !!}'
    ]).draw();
    @endforeach

    updateTotal();

    // Generamos los pedidos de archivos relacionados con las funciones
    $('#distributionTable .fn').change();
    $('#lyric_dnda_in_file').change();

    @foreach ($distribution as $idx => $current)
    @if ($current->type == 'no-member')
        addFileRequest(
            'file_no-member_doc',
            'Documento de <strong>{{ $current->meta->name }}</strong>',
            'image/png, image/jpeg, application/pdf',
            { doc_number: '{{ $current->doc_number }}' }
        );

        @if ($file = $current->getFile('file_no-member_doc'))
        setFileUploaderFile(
            $('input[value="{{ $current->doc_number }}"]').parent().find('input[value="member_doc"]').parent(),
            {
                id: '{{ $file->id }}',
                name: '{{ $file->name }}',
                path: '{{ $file->path }}'
            }
        );
        @endif
    @endif
    @endforeach

    @foreach ($request->files as $file)
        @if ($file->distribution_id) // Archivos de la distribución
        setFileUploaderFile(
            $('input[value="{{ $file->distribution->doc_number }}"]').parent().find('input[value="{{ $file->name }}"]').parent(),
            {
                id: '{{ $file->id }}',
                name: '{{ $file->name }}',
                path: '{{ $file->path }}',
                distribution_id: {{ $file->distribution_id }}
            }
        );
        @elseif ($file->name == 'file_dnda_contract') // Archivos condicionales del registro
        setFileUploaderFile(
            $('input[value="file_dnda_contract"]').parent(),
            {
                id: '{{ $file->id }}',
                name: '{{ $file->name }}',
                path: '{{ $file->path }}'
            }
        );
        @elseif (in_array($file->name, ['lyric_file', 'audio_file', 'script_file'])) // Archivos del registro
        setFileUploaderFile(
            $('input[value="{{ $file->name }}"]').parent(),
            {
                id: '{{ $file->id }}',
                name: '{{ $file->name }}',
                path: '{{ $file->path }}'
            }
        );
        @endif
    @endforeach
@endisset

@php
$has_author = $distribution->first(function($value) {
    if ($value->fn == 'A' || $value->fn == 'CA') {
        return true;
    }
});
@endphp
    $('#scriptAttachment').show();
    $('#scriptAttachment input[type="file"]').prop('disabled', false);
@if ($has_author)
@else
    $('#scriptAttachment input[type="file"]').prop('disabled', true);
@endif
</script>
@endpush
