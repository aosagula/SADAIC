<form id="jingleRegistrationForm" action="#">
    @if ($registration->exists)
    <input type="hidden" name="id" value="{{ $registration->id }}">
    @endif
    @if (Auth::user()->type != 'member' || !Auth::user()->sadaic)
    <div class="form-group row">
        <label class="col-12 col-form-label font-weight-bold">Datos del Solicitante</label>

        @include('components.agency-input', ['type' => 'applicant'])
    </div>
    @endif

    <div class="form-group row">
        <label class="col-12 col-form-label font-weight-bold">Datos del Anunciante</label>

        @include('components.agency-input', ['type' => 'advertiser'])
    </div>

    <div class="form-group row">
        <label for="name" class="col-12 col-form-label font-weight-bold">Tipo de Solicitud</label>
        <div class="col-12">
            <div class="row">
                <div class="col-12">
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" type="radio" name="is_special" id="requestTypeRegular" value="0" {{ old('is_special', $registration->is_special) ? '' : 'checked'}}>
                        <label class="form-check-label" for="requestTypeRegular">Regular</label>
                    </div>
                    <div class="form-check form-check-inline col-2">
                        <input class="form-check-input" type="radio" name="is_special" id="requestTypeSpecial" value="1" {{ old('is_special', $registration->is_special) ? 'checked' : ''}}>
                        <label class="form-check-label" for="requestTypeSpecial">Campaña Especial</label>
                    </div>
                </div>
                <br><br>
            </div>
            <div class="row">
                <div class="col-12">
                    <select class="custom-select" name="request_action_id" id="request_action_id"></select>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <div class="col-12">
            <div class="form-group row">
                <label for="validity" class="col-12 col-sm-3 col-form-label">Vigencia en meses</label>
                <div class="col-12 col-sm-3">
                    <input type="number" class="form-control" name="validity" id="validity" value="{{ old('validity', $registration->validity) }}" min="1" max="12" required>
                </div>
            </div>

            <div class="form-group row">
                <label for="air_date" class="col-12 col-sm-3 col-form-label">Fecha de salida al aire *</label>
                <div class="col-12 col-sm-3">
                    <input type="date" placeholder="__/__/____" class="form-control" name="air_date" id="air_date" value="{{ old('air_date', optional($registration->air_date)->format('Y-m-d')) }}" max="9999-12-31" required>
                </div>
            </div>

            <div class="form-group row regular">
                <label for="ads_duration" class="col-12 col-sm-3 col-form-label">Duración en segundos</label>
                <div class="col-12 col-sm-3">
                    <input type="number" class="form-control ads_duration" name="ads_duration[]" min="1" value="{{ old('ads_duration.0', $registration->ads_duration[0]) }}">
                </div>
            </div>
        </div>
    </div>

    <div class="form-group row special">
        <label class="col-12 col-form-label font-weight-bold">
            Campaña Especial
            <small id="specialHelp" class="form-text text-muted">
                <ul>
                    <li>3 o más avisos del mismo producto</li>
                    <li>Dentro de la vigencia de la autorización</li>
                    <li>Abonados en un solo pago</li>
                    <li>Abona la suma de aranceles menos el 25%</li>
                </ul>
            </small>
        </label>
        <div class="col-12">
            <div class="form-group row">
                <label for="ad_number" class="col-12 col-sm-3 col-form-label">Cantidad de Avisos</label>
                <div class="col-12 col-sm-9">
                    <input type="number" class="form-control" name="ad_number" id="ad_number" min="3" max="6" value="{{ old('ad_number', 3) }}">
                </div>
            </div>
            <div class="form-group row">
                <label for="ad_duration" class="col-12 col-sm-3 col-form-label">Duración en segundos</label>
                <div class="col-12 col-sm-9">
                    @for ($i = 0; $i < old('ad_number', 3); $i++)
                    <div class="form-group row ads_durationWrapper">
                        <label for="ads_duration" class="col-12 col-sm-3 col-form-label">Aviso {{ $i + 1 }}</label>
                        <div class="col-12 col-sm-9">
                            <input type="number" class="form-control ads_duration" name="ads_duration[]" min="1" value="{{ old('ads_duration.' . $i, Arr::get($registration->ads_duration, $i, '')) }}">
                        </div>
                    </div>
                    @endfor
                </div>
            </div>
        </div>
    </div>


    <div class="form-group row">
        <label class="col-12 col-form-label font-weight-bold">
            Características del Aviso Publicitario
            <small class="form-text text-muted">
                Se debe señalar solo 1. Si fuera en caso, por ejemplo de mas de dos paises, Paraguay y Chile se debe seleccionar el item “América Latina”
            </small>
        </label>
        <div class="col-12">
            <div class="form-group row local">
                <label for="ad_details_territory" class="col-12 col-sm-3 col-form-label">Territorio de Difusión</label>
                <div class="col-12 col-sm-9">
                <select class="custom-select" name="broadcast_territory_id" id="broadcast_territory_id" required>
                        @foreach($broadcast_territories as $broadcast_territory)
                        @php if($broadcast_territory['id'] == '3') continue; @endphp
                        <option value="{{ $broadcast_territory['id'] }}" {{
                            old('broadcast_territory_id', $registration->broadcast_territory_id) == $broadcast_territory['id'] ? 'selected' : ''
                        }}>{{ $broadcast_territory['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group row provincial">
                <label for="ad_details_territory_states" class="col-12 col-sm-3 col-form-label">Detalle de Provincias</label>
                <div class="col-12 col-sm-9">
                    <select class="custom-select" name="territory_id[]" id="ad_details_territory_states" multiple required>
                        @foreach($states as $state)
                        <option value="{{ $state->id }}" {{
                            in_array($state->id, old('territory_id', $registration->territory_id) ?? []) ? 'selected' : ''
                        }}>{{ $state->state }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group row foreign">
                <label for="also_national" class="col-12 col-sm-3 col-form-label">
                    Difusión Nacional
                </label>
                <div class="col-12 col-md-9">
                    <div class="form-check">
                        <input class="form-check-input" type="checkbox" value="1" id="also_national" name="also_national" {{ old('also_national', $registration->also_national) ? 'checked' : ''}}>
                        <label class="form-check-label" for="also_national">También tuvo difusión nacional</label>
                    </div>
                </div>
            </div>

            <div class="form-group row foreign">
                <label for="ad_details_territory_countries" class="col-12 col-sm-3 col-form-label">Detalle de Paises</label>
                <div class="col-12 col-sm-9">
                    <select class="custom-select" name="territory_id[]" id="ad_details_territory_countries" required>
                        @foreach($countries as $country)
                        <option value="{{ $country->idx }}">{{ $country->name_ter }}</option>
                        @endforeach
                    </select>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-12 col-form-label font-weight-bold">
            Medios de Comunicación
        </label>
        <div class="col-12">
            @foreach($media as $current)
                <div class="form-check form-check-inline col-12">
                    <label class="form-check-label col-3" for="mediaType{{ $current->id }}">{{ $current->name }}</label>
                    <input class="form-check-input" type="radio" name="media_id"id="mediaType{{ $current->id }}" value="{{ $current->id }}" {{ old('media_id', $registration->media_id) == $current->id ? 'checked' : ''}}>
                    <label class="form-check-label" for="mediaType{{ $current->id }}">{{ $current->description }}</label>
                </div>
            @endforeach
        </div>
    </div>

    <div class="form-group row special">
        <label class="col-12 col-form-label font-weight-bold">
            Inciso i)
        </label>
        <div class="col-12">
            <blockquote>Toda Campaña Especial que tenga una continuidad mínima de 3 (tres) meses en la que el anunciante modifique sus avisos en forma diaria o semanal, utilizando en ellos la misma obra musical, se abonará un importe no inferior al doble del arancel determinado en la tabla arancelaria que como ANEXO I forma parte del presente Régimen para cada plazo y modalidad de utilización.</blockquote>
            <div class="form-check text-center">
                <input class="form-check-input" type="checkbox" value="1" id="subsection_i" name="subsection_i" {{ old('subsection_i', $registration->subsection_i) ? 'checked' : ''}}>
                <label class="form-check-label" for="subsection_i">
                    Aplica Inciso i)
                </label>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-12 col-form-label font-weight-bold">
            Datos de la Agencia / Productora
            <small id="agencyHelp" class="form-text text-muted">
                En caso de no participar una Agencia de Publicidad, consignar los datos de la Productora.
            </small>
        </label>

        <div class="col-12">
            <div class="form-group row">
                <label for="agency_type_id" class="col-12 col-sm-3 col-form-label">Tipo</label>
                <div class="col-12 col-sm-4">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="agency_type_id" id="agencyTypeAgenct" value="1" {{ old('agency_type_id', $registration->agency_type_id) == '1' ? 'checked' : ''}}>
                        <label class="form-check-label" for="agencyTypeAgenct">Agencia</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="agency_type_id" id="agencyTypeProducer" value="2" {{ old('agency_type_id', $registration->agency_type_id) == '2' ? 'checked' : ''}}>
                        <label class="form-check-label" for="agencyTypeProducer">Productora</label>
                    </div>
                </div>
            </div>
        </div>

        @include('components.agency-input', ['type' => 'agency'])
    </div>

    <div class="form-group row">
        <label class="col-12 col-form-label font-weight-bold">
            Datos del Producto / Servicio
        </label>
        <div class="col-12">
            <div class="form-group row">
                <label for="product_brand" class="col-12 col-sm-3 col-form-label">Marca *</label>
                <div class="col-12 col-sm-9">
                    <input type="text" class="form-control" name="product_brand" id="product_brand" value="{{ old('product_brand', $registration->product_brand) }}" required>
                </div>
            </div>

            <div class="form-group row">
                <label for="product_type" class="col-12 col-sm-3 col-form-label">Característica y Tipo *</label>
                <div class="col-12 col-sm-9">
                    <input type="text" class="form-control" name="product_type" id="product_type" value="{{ old('product_type', $registration->product_type) }}" required>
                </div>
            </div>

            <div class="form-group row">
                <label for="product_name" class="col-12 col-sm-3 col-form-label">Identicación y Nombre *</label>
                <div class="col-12 col-sm-9">
                    <input type="text" class="form-control" name="product_name" id="product_name" value="{{ old('product_name', $registration->product_name) }}">
                </div>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-12 col-form-label font-weight-bold">
            Datos de la Obra
        </label>
        <div class="col-12">
            <div class="form-group row">
                <label for="work_name" class="col-12 col-sm-3 col-form-label">Título *</label>
                <div class="col-12 col-sm-9">
                    <input type="text" class="form-control" name="work_title" id="work_title" value="{{ old('work_title', $registration->work_title) }}">
                </div>
            </div>

            <div class="form-group row">
                <div class="col-12 col-sm-5 form-control-plaintext text-center">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="work_original" id="workTypeOriginal" value="1" {{ old('work_original', $registration->work_original) ? 'checked' : '' }}>
                        <label class="form-check-label" for="workTypeOriginal">Original</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="work_original" id="workTypePre" value="0" {{ old('work_original', $registration->work_original) ? '' : 'checked' }}>
                        <label class="form-check-label" for="workTypePre">Preexistente</label>
                    </div>
                </div>

                <label for="work_dnda" class="col-12 col-sm-3 col-form-label">N° Expediente DNDA</label>
                <div class="col-12 col-sm-4">
                    <input type="text" class="form-control" name="work_dnda" id="work_dnda" value="{{ old('work_dnda', $registration->work_dnda) }}">
                </div>
            </div>

            <div class="form-group row">
                <label for="work_dnda" class="col-12 col-sm-3 col-form-label">Autores *</label>
                <div class="col-12 col-sm-9">
                    <textarea class="form-control" name="work_authors" id="work_authors">{{ old('work_authors', $registration->work_authors) }}</textarea>
                </div>
            </div>

            <div class="form-group row">
                <label for="work_composers" class="col-12 col-sm-3 col-form-label">Compositores</label>
                <div class="col-12 col-sm-9">
                    <textarea class="form-control" name="work_composers" id="work_composers">{{ old('work_composers', $registration->work_composers) }}</textarea>
                </div>
            </div>

            <div class="form-group row">
                <label for="work_editors" class="col-12 col-sm-3 col-form-label">Editores</label>
                <div class="col-12 col-sm-9">
                    <textarea class="form-control" name="work_editors" id="work_editors">{{ old('work_editors', $registration->work_editors) }}</textarea>
                </div>
            </div>

            <div class="form-group row align-items-center">
                <label for="work_modded" class="col-12 col-sm-3 col-form-label">Modificación de la Obra</label>
                <div class="col-12 col-sm-9">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" value="1" name="work_script_mod" id="work_lyric_modded" value="1" {{ old('work_script_mod', $registration->work_script_mod) ? 'checked' : '' }}>
                        <label class="form-check-label" for="work_lyric_modded">
                            Letra
                        </label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="checkbox" value="1" name="work_music_mod" id="work_audio_modded" value="1" {{ old('work_music_mod', $registration->work_music_mod) ? 'checked' : '' }}>
                        <label class="form-check-label" for="work_audio_modded">
                            Música (arreglos, adaptaciones, versiones)
                        </label>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-12 col-form-label font-weight-bold">
            Conformidad de los autores
        </label>
        <div class="col-12">
            <div class="form-group row align-items-center">
                <label for="authors_agreement" class="col-12 col-sm-6 col-form-label">¿Cuenta con la conformidad de los autores?</label>
                <div class="col-12 col-sm-6">
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="authors_agreement" id="work_authors_agreed_yes" value="1" {{ old('authors_agreement', $registration->authors_agreement) == '1' ? 'checked' : ''}}>
                        <label class="form-check-label" for="work_authors_agreed_yes">Si</label>
                    </div>
                    <div class="form-check form-check-inline">
                        <input class="form-check-input" type="radio" name="authors_agreement" id="work_authors_agreed_no" value="0" {{ old('authors_agreement', $registration->authors_agreement) == '0' ? 'checked' : ''}}>
                        <label class="form-check-label" for="work_authors_agreed_no">No</label>
                    </div>
                </div>
            </div>

            <div class="form-group row agreed">
                <div class="col-12">
                    <table id="peopleTable" class="table table-striped nowrap" width="100%">
                        <thead>
                            <tr>
                                <th class="w-75">Apellido y Nombre</th>
                                <th>DNI/CUIT</th>
                                <th>N° Socio</th>
                                <th></th>
                                <th></th>
                            </tr>
                        </thead>
                        <tbody></tbody>
                    </table>
                </div>
            </div>

            <div class="form-group row agreed justify-content-center">
                <button type="button" class="btn btn-primary" data-toggle="modal" data-target="#addPersonModal">Agregar Persona</button>
            </div>

            <div class="form-group row agreed">
                <label class="col-12 col-form-label font-weight-bold">
                    Arancel *
                </label>
                <div class="col-12">
                    <div class="input-group mb-3">
                        <div class="input-group-prepend">
                            <span class="input-group-text" id="tariffAddon">$</span>
                        </div>
                        <input type="number" class="form-control" aria-describedby="tariffAddon" name="authors_tariff" id="tariff" value="{{ old('authors_tariff', $registration->authors_tariff) }}" step="0.01" min="1" required>
                    </div>
                </div>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-12 col-form-label font-weight-bold">
            Persona física o jurídica que abona el derecho de autor
        </label>
        <div class="col-12">
            <div class="form-group row">
                <div class="col-12">
                    <select class="custom-select" name="tariff_payer_id" id="payer">
                        @foreach($tariff_payers as $tariff_payer)
                        <option value="{{ $tariff_payer['id'] }}" {{
                            old('tariff_payer_id', $registration->tariff_payer_id) == $tariff_payer['id'] ? 'selected' : ''
                        }}>{{ $tariff_payer['name'] }}</option>
                        @endforeach
                    </select>
                </div>
            </div>

            <div class="form-group row represent">
                <label for="represent" class="col-12 col-sm-6 col-form-label">A cuenta y orden de (anunciante o agencia)</label>
                <div class="col-12 col-sm-6">
                    <input type="text" class="form-control" name="tariff_representation" id="represent" value="{{ old('tariff_representation', $registration->tariff_representation) }}">
                </div>
            </div>
        </div>
    </div>
</form>
@include('jingles.people')

@push('styles')
<style>
input[name="media_type"] {
    height: 2rem;
    margin: 0;
}

.form-check-label.col-3 {
    text-align: right;
    padding-right: 1.5rem;
}

input[name="media_type"] + label {
    padding-left: 1.5rem;
}

small ul {
    padding-inline-start: 20px;
}

:not(.tab-pane) > .container {
    margin-bottom: 5rem;
}

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

#addPersonTabsContent {
    overflow-y: auto;
}

label.font-weight-bold {
    font-size: 1.25rem;
    padding: 1rem;
}
</style>
@endpush

@push('scripts')
<script src="{{ asset('/js/jingle.register.js') }}"></script>
<script src="{{ asset('/js/city.selector.js') }}"></script>
<script src="{{ asset('/js/agency.input.js') }}"></script>
<script>
    window.citiesOptions = @json($cities);
</script>
@endpush
