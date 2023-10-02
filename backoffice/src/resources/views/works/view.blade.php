@extends('dashboard.layout')

@section('content')
<div class="container">
    <section class="content-header">
        <div class="row mb-2">
            <div class="col-sm-6">
                <h1 class="m-0 text-dark">
                    Solicitud N° {{ $registration->id }}
                </h1>
            </div>
            <div class="col-sm-6">
                <ol class="breadcrumb float-sm-right">
                    <li class="breadcrumb-item"><a href="/">Inicio</a></li>
                    <li class="breadcrumb-item"><a href="/works">Registro de Obras</a></li>
                    <li class="breadcrumb-item active">Solicitud #{{ $registration->id }}</li>
                </ol>
            </div>
        </div>
    </section>
    <section class="content">
        <table class="table">
            <tr>
                <th colspan="2" class="table-inner-title">Datos generales</th>
            </tr>
            <tr>
                <th>Título</th>
                <td>{{ $registration->title }}</td>
            </tr>
            @if($registration->titles && $registration->titles->count() > 0)
            <tr>
                <th>Títulos alternativos</th>
                <td>
                    @foreach($registration->titles as $title)
                        {{ $title->title }}<br>
                    @endforeach
                </td>
            </tr>
            @endif
            <tr>
                <th>Género</th>
                <td>{{ $registration->genre->name }}</td>
            </tr>
            <tr>
                <th>Duración</th>
                <td>{{ $registration->duration }}</td>
            </tr>
            <tr>
                <th>Fecha</th>
                <td>{{ optional($registration->created_at)->format('d/m/Y H:i') }}</td>
            </tr>
            <tr>
                <th>Estado</th>
                <td>{{ optional($registration->status)->name }}</td>
            </tr>
            <tr>
                <th>Archivo Exportado</th>
                <td><a href="{{ route('work.re_export', ['id' => $registration->id ]) }}">
                    {{ $registration->export_filename }}</a></td>
            </tr>
            <tr>
                <th>Iniciador</th>
                @if ($registration->member_id)
                <td>{{ $registration->initiator->full_name }} (Socio n° {{ $registration->initiator->member_id }}/{{ $registration->initiator->heir }})</td>
                @else
                <td>{{ $registration->initiator->name }}</td>
                @endif
            </tr>
            <tr>
                <th>Correo electrónico</th>
                <td>{{ $registration->initiator->email }}</td>
            </tr>
            <tr>
                <th></th>
                <td>
                    {{ $registration->is_jingle ? '✔' : '✖' }} Música en Publicidad
                    <br>
                    {{ $registration->is_movie ? '✔' : '✖' }} Música en Producción Audiovisual
                    <br>
                    {{ $registration->is_regular ? '✔' : '✖' }} Música Regular
                </td>
            </tr>
            <tr>
                <th colspan="2" class="table-inner-title">DNDA</th>
            </tr>
            <tr>
                <th>Título</th>
                <td>{{ $registration->dnda_title }}</td>
            </tr>
            @if ($registration->dnda_in_date)
            <tr>
                <th colspan="2" class="table-inner-subtitle">Inédito</th>
            </tr>
            <tr>
                <th>N° Expediente (Música)</th>
                <td>{{ $registration->audio_dnda_in_file }}</td>
            </tr>
            <tr>
                <th>N° Expediente (Letra)</th>
                <td>{{ $registration->lyric_dnda_in_file }}</td>
            </tr>
            <tr>
                <th>Fecha</th>
                <td>{{ optional($registration->dnda_in_date)->format('d/m/Y') }}</td>
            </tr>
            @endif
            @if ($registration->dnda_ed_date)
            <tr>
                <th colspan="2" class="table-inner-subtitle">Editado</th>
            </tr>
            <tr>
                <th>N° Expediente (Música)</th>
                <td>{{ $registration->audio_dnda_ed_file }}</td>
            </tr>
            <tr>
                <th>N° Expediente (Letra)</th>
                <td>{{ $registration->lyric_dnda_ed_file }}</td>
            </tr>
            <tr>
                <th>Fecha</th>
                <td>{{ $registration->dnda_ed_date->format('d/m/Y') }}</td>
            </tr>
            @endif
            <tr>
                <th colspan="2" class="table-inner-title">Distribución</th>
            </tr>
            @foreach ($registration->distribution as $distribution)
            <tr>
                @if ($distribution->type == 'member')
                <th>{{ optional($distribution->member)->nombre }}<br><small>Socio n° {{ $distribution->member_id }}</small></th>
                @else
                <th>{{ $distribution->meta->name }}<br><small>DNI n° {{ $distribution->doc_number }}</small></th>
                @endif
                <td>
                    <strong>Rol:</strong> {{ $distribution->role->description }}<br>
                    @if ($distribution->type == 'no-member')
                    <strong>{{ optional($distribution->meta->type)->description }}:</strong> {{ $distribution->doc_number }}<br>
                    <strong>Nacimiento:</strong> {{ $distribution->meta->birth_date->format('d/m/Y') }}, {{ $distribution->meta->birth_country->name_ter }}<br>
                    <strong>Dirección:</strong> {{ $distribution->meta->full_address }}<br>
                    <strong>Correo electrónico:</strong> <a href="mailto:{{ $distribution->meta->email }}">{{ $distribution->meta->email }}</a><br>
                    <strong>Teléfono:</strong> {{ $distribution->meta->full_phone }}<br>
                    @endif
                    <strong>Ejecución Pública:</strong> {{ $distribution->public }}%<br>
                    <strong>Reproducción Mecánica:</strong> {{ $distribution->mechanic }}%<br>
                    <strong>Sincronización:</strong> {{ $distribution->sync }}%<br>
                    <div class="d-flex flex-row align-items-center">
                    <strong>Respuesta:</strong>&nbsp;
                    @if ($distribution->response === null)
                        Sin respuesta
                        @if (in_array($registration->status_id, [2, 3]) && Auth::user()->can('nb_obras', 'carga'))
                        &nbsp;<button class="btn btn-link text-success acceptDistribution" data-did="{{ $distribution->id }}">Aceptar</button>
                        &nbsp;<button class="btn btn-link text-danger rejectDistribution" data-did="{{ $distribution->id }}">Rechazar</button>
                        @endif
                    @elseif ($distribution->response === 0)
                        Rechazado por
                        {{ $distribution->liable_id ?? 'el socio' }}
                        ({{ $distribution->updated_at->format('d/m/Y H:i') }})
                        @if (in_array($registration->status_id, [2, 3]) && Auth::user()->can('nb_obras', 'carga'))
                        &nbsp;<button class="btn btn-link text-success acceptDistribution" data-did="{{ $distribution->id }}">Cambiar Respuesta</button>
                        @endif
                    @elseif ($distribution->response === 1)
                        Aceptado por
                        {{ $distribution->liable_id ?? 'el socio' }}
                        ({{ $distribution->updated_at->format('d/m/Y H:i') }})
                    @endif
                    </div>
                </td>
            </tr>
            @endforeach
            <tr>
                <th colspan="2" class="table-inner-title">Archivos</th>
            </tr>
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
                            if ($file->distribution->member_id) {
                                $desc .= optional($file->distribution->member)->nombre;
                            } else {
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
                    <td><a href="/works/files?file={{ $file->path }}">Descargar</a></td>
                </tr>
            @endforeach
            <tr>
                <th colspan="2" class="table-inner-title">Registro</th>
            </tr>
            @foreach ($registration->logs as $log)
            <tr>
                <th>{{ $log->time->format('d/m/Y H:i') }}</th>
                @switch($log->action->name)
                    @case('REGISTRATION_ACCEPTED')
                        <td>{{ $log->action->description }} {{ isset($log->action_data['forced']) ? '(Forzado)' : '' }}</td>
                        @break
                    @case('DISTRIBUTION_CONFIRMED')
                    @case('DISTRIBUTION_REJECTED')
                        <td>{{ $log->action->description }} ({{
                            $log->distribution->member_id
                            ? optional($log->distribution->member)->nombre
                            : $log->distribution->meta->name
                            }}{{ isset($log->action_data['operator_id']) ? ' por ' . $log->action_data['operator_id'] : '' }})</td>
                        @break
                    @case('NOT_NOTIFIED')
                        <td>{{ $log->action->description }} ({{
                            $log->distribution->member_id
                            ? optional($log->distribution->member)->nombre
                            : $log->distribution->meta->name
                            }})</td>
                        @break
                    @default
                        <td>{{ $log->action->description }}</td>
                @endswitch
            </tr>
            @endforeach
            <tr>
                <th colspan="2" class="table-inner-title">Observaciones</th>
            </tr>
            <tr>
                <td colspan="2" id="observationsWrapper">
                    @if (Auth::user()->can('nb_obras', 'carga'))
                    <textarea id="observations">{{ $registration->observations }}</textarea>
                    <button class="btn btn-secondary float-right" id="saveObservations">Guardar Observaciones</button>
                    @else
                    <div id="observations">{!! nl2br(e($registration->observations)) !!}</div>
                    @endif
                </td>
            </tr>
        </table>
        <br /><br />
        @if (Auth::user()->can('nb_obras', 'homologa'))
        {{-- Trámite Nuevo --}}
        @if ($registration->status_id == 1)
        <div class="row justify-content-center">
            <div>
                <button class="btn btn-success" id="beginAction">Iniciar Proceso</button>
                <button class="btn btn-danger" data-toggle="modal" data-target="#modalRejection">Rechazar Solicitud</button>
            </div>
        </div>
        {{-- Aprobado por todos los propietarios --}}
        @elseif ($registration->status_id == 5)
        <div class="row justify-content-center">
            <div>
                <button class="btn btn-primary" id="sendToInternal">Pase a Procesamiento Interno</button>
            </div>
        </div>
        {{-- Aprobada/Rechazada --}}
        @elseif ($registration->status_id == 8 || $registration->status_id == 9)
        <div class="row justify-content-center">
            <div>
                <button class="btn btn-primary" id="finishRequest">Finalizar</button>
            </div>
        </div>
        @endif
        @endif
    </section>
</div>

@if ($registration->status_id == 1)
<div class="modal" tabindex="-1" id="modalRejection">
    <div class="modal-dialog modal-lg modal-dialog-centered">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title">Ingrese el motivo del rechazo</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <textarea class="form-control" rows="5"></textarea>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Cancelar</button>
                <button type="button" class="btn btn-primary" id="rejectAction">Rechazar</button>
            </div>
        </div>
    </div>
</div>
@endif
@endsection

@push('scripts')
<script>const workId = {{ $registration->id }}</script>
<script src="{{ asset('/js/works.view.js') }}"></script>
@endpush

@push('styles')
<style>
.btn-link:hover {
    text-decoration: underline;
}
</style>
@endpush
