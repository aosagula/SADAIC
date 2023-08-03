<div class="row">
    <div class="col col-12">
        <label class="col-12 col-form-label font-weight-bold">Datos del Solicitante</label>
        @include('components.agency-view', ['type' => 'applicant'])
    </div>

    <div class="col col-12">
        <label class="col-12 col-form-label font-weight-bold">Datos del Anunciante</label>
        @include('components.agency-view', ['type' => 'advertiser'])
    </div>

    <div class="col col-12">
        <label class="col-12 col-form-label font-weight-bold">Tipo de solicitud</label>

        <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label">Tipo</label>
            <div class="col-12 col-sm-9">
                <div class="form-control">{{ $registration->is_special ? 'Campaña Especial' : 'Regular' }} - {{ $registration->request_action }}</div>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label">Vigencia en Meses</label>
            <div class="col-12 col-sm-9">
                <div class="form-control">{{ $registration->validity }}</div>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label">Fecha de Salida al Aire</label>
            <div class="col-12 col-sm-9">
                <div class="form-control">{{ $registration->air_date->format('d/m/Y') }}</div>
            </div>
        </div>
        @if ($registration->is_special)
        <label class="col-12 col-form-label font-weight-bold">Campaña Especial</label>

        <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label">Cantidad de Avisos</label>
            <div class="col-12 col-sm-9">
                <div class="form-control">{{ count($registration->ads_duration) }}</div>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label">Duración de los Avisos</label>
            <div class="col-12 col-sm-9">
                <div class="form-control">@foreach($registration->ads_duration as $duration)
                    Aviso {{ $loop->iteration }}: {{ $duration }}s
                    @if(!$loop->last) , @endif
                @endforeach</div>
            </div>
        </div>
        @else
        <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label">Duración en Segundos</label>
            <div class="col-12 col-sm-9">
                <div class="form-control">{{ $registration->ads_duration[0] }}s</div>
            </div>
        </div>
        @endif
    </div>

    <div class="col col-12">
        <label class="col-12 col-form-label font-weight-bold">Características del Aviso Publicitario</label>

        <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label">Territorio de Difusión</label>
            <div class="col-12 col-sm-9">
                <div class="form-control">{{ $registration->broadcast_territory }}</div>
            </div>
        </div>

        @if ($registration->broadcast_territory_id == 2)
        <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label">Detalle Provincias</label>
            <div class="col-12 col-sm-9">
                <div class="form-control">{{ $registration->territories->implode('state', ', ') }}</div>
            </div>
        </div>
        @elseif ($registration->broadcast_territory_id == 3)
        <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label">Detalle de Paises</label>
            <div class="col-12 col-sm-9">
                <div class="form-control">{{ $registration->territories->implode('name_ter', ', ') }}</div>
            </div>
        </div>
        @endif
    </div>

    <div class="col col-12">
        <label class="col-12 col-form-label font-weight-bold">Medios de Comunicación</label>

        <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label">{{ $registration->media->name }}</label>
            <div class="col-12 col-sm-9">
                <div class="form-control">{{ $registration->media->description }}</div>
            </div>
        </div>
    </div>

    @if ($registration->agency->cuit && $registration->agency->name)
    <div class="col col-12">
        <label class="col-12 col-form-label font-weight-bold">Datos de la Agencia</label>
        @include('components.agency-view', ['type' => 'agency'])
    </div>
    @endif

    <div class="col col-12">
        <label class="col-12 col-form-label font-weight-bold">Datos del Producto</label>

        <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label">Marca</label>
            <div class="col-12 col-sm-9">
                <div class="form-control">{{ $registration->product_brand }}</div>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label">Característica y Tipo</label>
            <div class="col-12 col-sm-9">
                <div class="form-control">{{ $registration->product_type }}</div>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label">Identicación y Nombre</label>
            <div class="col-12 col-sm-9">
                <div class="form-control">{{ $registration->product_name }}</div>
            </div>
        </div>
    </div>

    <div class="col col-12">
        <label class="col-12 col-form-label font-weight-bold">Datos de la Obra</label>

        <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label">Título</label>
            <div class="col-12 col-sm-9">
                <div class="form-control">{{ $registration->work_title }}</div>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label">Original</label>
            <div class="col-12 col-sm-9">
                <div class="form-control">{{ $registration->work_original ? 'Si' : 'No' }}</div>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label">DNDA</label>
            <div class="col-12 col-sm-9">
                <div class="form-control">{{ $registration->work_dnda }}</div>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label">Autores</label>
            <div class="col-12 col-sm-9">
                <div class="form-control">{{ $registration->work_authors }}</div>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label">Compositores</label>
            <div class="col-12 col-sm-9">
                <div class="form-control">{{ $registration->work_composers }}</div>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label">Editores</label>
            <div class="col-12 col-sm-9">
                <div class="form-control">{{ $registration->work_editors }}</div>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label">Letra Modificada</label>
            <div class="col-12 col-sm-9">
                <div class="form-control">{{ $registration->work_script_mod ? 'Si' : 'No' }}</div>
            </div>
        </div>

        <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label">Música Modificada</label>
            <div class="col-12 col-sm-9">
                <div class="form-control">{{ $registration->work_music_mod ? 'Si' : 'No' }}</div>
            </div>
        </div>
    </div>

    <div class="col col-12">
        <label class="col-12 col-form-label font-weight-bold">Conformidad de los Autores</label>

        <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label">Conformidad de los Autores</label>
            <div class="col-12 col-sm-9">
                <div class="form-control">{{ $registration->authors_agreement ? 'Si' : 'No' }}</div>
            </div>
        </div>

        @if ($registration->authors_agreement)
            @foreach($registration->agreements as $person)
            <div class="form-group row">
                @if ($person->type_id == 1)
                <label class="col-12 col-sm-3 col-form-label">{{ $person->member->nombre }}</label>
                <div class="col-12 col-sm-9">
                    <div class="form-control">
                        <strong>N° de Documento:</strong> {{ $person->doc_number }}<br>
                        <strong>Correo electrónico:</strong> {{ $person->member->email }}<br>
                    </div>
                </div>
                @else
                <label class="col-12 col-sm-3 col-form-label">{{ $person->meta->name }}</label>
                <div class="col-12 col-sm-9">
                    <div class="form-control">
                        <strong>Nacimiento:</strong> {{ $person->meta->birth_date->format('d/m/Y') }}, {{ $person->meta->birth_country->name_ter }}<br>
                        <strong>Dirección:</strong> {{ $person->meta->full_address }}<br>
                        <strong>Correo electrónico:</strong> <a href="mailto:{{ $person->meta->email }}">{{ $person->meta->email }}</a><br>
                        <strong>Teléfono:</strong> {{ $person->meta->full_phone }}<br>
                        <strong>Respuesta:</strong>
                        @if ($person->response === null)
                            Sin Respuesta
                        @elseif ($person->response === 0)
                            Rechazado
                        @elseif ($distribution->response === 1)
                            Aceptado
                        @endif
                    </div>
                </div>
                @endif
            </div>
            @endforeach
        @endif
    </div>

    <div class="col col-12">
        <label class="col-12 col-form-label font-weight-bold">Arancel</label>

        <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label">Monto</label>
            <div class="col-12 col-sm-9">
                <div class="form-control">$ {{ number_format($registration->authors_tariff, 2) }}</div>
            </div>
        </div>
    </div>

    <div class="col col-12">
        <label class="col-12 col-form-label font-weight-bold">Persona física o jurídica que abona el derecho de autor</label>

        <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label">Persona</label>
            <div class="col-12 col-sm-9">
                <div class="form-control">{{ $registration->tariff_payer }}</div>
            </div>
        </div>

        @if ($registration->tariff_payer_id == 3)
        <div class="form-group row">
            <label class="col-12 col-sm-3 col-form-label">A cuenta y orden de</label>
            <div class="col-12 col-sm-9">
                <div class="form-control">{{ $registration->tariff_representation }}</div>
            </div>
        </div>
        @endif
    </div>
</div>

@push('styles')
<style>
.form-control {
    border: none;
    border-radius: 0;
    border-bottom: solid 1px #ccc;
}

label.font-weight-bold {
    font-size: 1.25rem;
    padding: 1rem 0;
}
</style>
@endpush