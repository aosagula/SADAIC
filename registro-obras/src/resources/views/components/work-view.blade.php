<div class="row">
    <div class="col col-12">
        <div class="form-group">
            <label for="title">Título</label>
            <div class="form-control">{{ $registration->title }}</div>
        </div>

        @if($registration->titles && $registration->titles->count() > 0)
        <div class="form-group">
            <label for="alternative_titles">Títulos alternativos</label>
            <div class="form-control">
                @foreach($registration->titles as $title)
                    {{ $title->title }}<br>
                @endforeach
            </div>
        </div>
        @endif

        <div class="form-group">
            <label for="dnda_title">Título Álbum **</label>
            <div class="form-control">{{ $registration->dnda_title }}</div>
        </div>

        <div class="form-group">
            <label for="dnda_title">Estado de la Solicitud</label>
            <div class="form-control">{{ optional($registration->status)->name }}</div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-9">
                <label for="genre_id">Género</label>
                <div class="form-control">{{ $registration->genre->name }}</div>
            </div>
            <div class="form-group col-md-3">
                <label for="duration">Duración</label>
                <div class="form-control">{{ $registration->duration }}</div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-12">
            {{ $registration->is_jingle ? '✔' : '✖' }} Música en Publicidad &nbsp; {{ $registration->is_movie ? '✔' : '✖' }} Música en Producción Audiovisual &nbsp; {{ $registration->is_regular ? '✔' : '✖' }} Música Regular
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="lyric_dnda_in_file">N° Expediente DNDA Inédita (Letra)</label>
                <div class="form-control">{{ $registration->lyric_dnda_in_file }}</div>
            </div>
            <div class="form-group col-md-4">
                <label for="audio_dnda_in_file">N° Expediente DNDA Inédita (Musica)</label>
                <div class="form-control">{{ $registration->audio_dnda_in_file }}</div>
            </div>
            <div class="form-group col-md-4">
                <label for="dnda_in_date">Fecha Inédita</label>
                <div class="form-control">{{ $registration->dnda_in_date ? $registration->dnda_in_date->format('Y-m-d') : '' }}</div>
            </div>
        </div>

        <div class="form-row">
            <div class="form-group col-md-4">
                <label for="lyric_dnda_ed_file">N° Expediente DNDA Editada (Letra)</label>
                <div class="form-control">{{ $registration->lyric_dnda_ed_file }}</div>
            </div>
            <div class="form-group col-md-4">
                <label for="audio_dnda_ed_file">N° Expediente DNDA Editada (Musica)</label>
                <div class="form-control">{{ $registration->audio_dnda_ed_file }}</div>
            </div>
            <div class="form-group col-md-4">
                <label for="dnda_ed_date">Fecha Editada</label>
                <div class="form-control">{{ $registration->dnda_ed_date ? $registration->dnda_ed_date->format('Y-m-d') : '' }}</div>
            </div>
        </div>
    </div>
</div>
<div class="row">
    <div class="col col-12">
        <table class="table table-striped nowrap" width="100%">
            <thead>
                <tr>
                    <th>Rol</th>
                    <th>Socio</th>
                    <th>Apellido y Nombre</th>
                    <th>DNI/CUIT</th>
                    <th>% Pública</th>
                    <th>% Mecánica</th>
                    <th>% Sinc.</th>
                    <th></th>
                </tr>
            </thead>
            <tbody>
            @foreach($registration->distribution as $distribution)
                @php
                $respuesta = 'No respondió';
                if ($distribution->response === true) {
                    $respuesta = 'Aceptada';
                } else if ($distribution->response === false) {
                    $respuesta = 'Rechazada';
                }
                @endphp
                <tr>
                    <td>{{ $distribution->role->description }}</td>
                    <td>{{ $distribution->member_id ?? '-' }}</td>
                    <td>{{ $distribution->type == 'member' ?  ucwords(strtolower($distribution->member->nombre)) : $distribution->meta->name }}</td>
                    <td>{{ $distribution->doc_number }}</td>
                    <td>{{ $distribution->public }}%</td>
                    <td>{{ $distribution->mechanic }}%</td>
                    <td>{{ $distribution->sync }}%</td>
                    <td>{{ $respuesta }}</td>
            @endforeach
            </tbody>
            <tfoot>
                <tr>
                    <th></th>
                    <th></th>
                    <th></th>
                    <th>Total</th>
                    <th>100%</th>
                    <th>100%</th>
                    <th>100%</th>
                    <th></th>
                </tr>
            </tfoot>
        </table>
    </div>
</div>
<div class="row">
    <div class="col col-12">
    <h3>Documentación</h3>
        <table id="attachmentsTable" class="table table-striped nowrap overflow-hidden" width="100%">
            <thead>
                <tr>
                    <th class="min-mobile">Descripción</th>
                    <th class="min-desktop">Archivo</th>
                </tr>
            </thead>
            <tbody>
            @foreach ($registration->files as $file)
                @php
                $desc = '';
                switch($file->name) {
                    case 'lyric_file': $desc = 'Archivo Partitura'; break;
                    case 'audio_file': $desc = 'Archivo de Audio'; break;
                    case 'script_file': $desc = 'Archivo Letra'; break;
                    case 'file_dnda_contract': $desc = 'Constancia DNDA'; break;
                    default:
                        $name = explode('_', $file->name);

                        if ($name[1] == 'editor' || $name[1] == 'subeditor' || $name[1] == 'dnda') {
                            if ($name[2] == 'contract') {
                                $desc = 'Contrato';
                            } elseif ($name[2] == 'triage') {
                                $desc = 'Contrato de tiraje';
                            }
                        } elseif ($name[1] == 'no-member') {
                            $desc = 'Documento';
                        }

                        $desc .= ' <strong>';

                        if ($name[1] != 'dnda') {
                            if ($file->distribution->type == 'member') {
                                $desc .= ucwords(strtolower($file->distribution->member->nombre));
                            } else if ($file->distribution->type == 'no-member') {
                                $desc .= $file->distribution->meta->name;
                            }
                        } else {
                            $desc .= 'DNDA';
                        }

                        $desc .= '</strong>';
                    }
                @endphp
                <tr>
                    <th>{!! $desc !!}</th>
                    <td><a href="/files/download/{{ $file->id }}">Descargar</a></td>
                </tr>
            @endforeach
            </tbody>
        </table>
    </div>
</div>

@push('styles')
<style>
.form-control {
    border: none;
    border-radius: 0;
    border-bottom: solid 1px #ccc;
}
</style>
@endpush
