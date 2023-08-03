<form id="{{ isset($edit) ? 'editNoMemberForm' : 'noMemberForm' }}" class="container">
    <div class="form-group row">
        <label for="name" class="col-12 col-sm-3 col-form-label">Apellido y Nombre *</label>
        <div class="col-12 col-sm-9">
            <input type="text" class="form-control name" name="name" required>
        </div>
    </div>

    <div class="form-group row">
        <label for="doc_type" class="col-12 col-sm-3 col-form-label">DNI / CUIT: *</label>
        <div class="col-12 col-sm-9">
            <div class="input-group">
                <div class="input-group-prepend">
                    <select class="custom-select doc_type" name="doc_type" required>
                        @foreach($types as $type)
                        <option value="{{ $type->code }}" @if ($type->code == 'DNI') selected
                            @endif>{{ $type->description }}</option>
                        @endforeach
                    </select>
                </div>
                <input type="text" class="form-control doc_number" name="doc_number" required>
            </div>
        </div>
    </div>

    <div class="form-group row">
        <label for="birth_country_id" class="col-12 col-sm-3 col-form-label">Nacionalidad *</label>
        <div class="col-12 col-sm-9">
            <select class="custom-select birth_country_id" name="birth_country_id" required>
                @foreach($countries as $country)
                <option value="{{ $country->idx }}" @if ($country->idx == '32AR') selected
                    @endif>{{ $country->name_ter }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group row">
        <label for="birth_date" class="col-12 col-sm-3 col-form-label" required>Fecha de Nacimiento *</label>
        <div class="col-12 col-sm-9">
            <input type="date" id="birth_date" placeholder="__/__/____" class="form-control birth_date" name="birth_date" required max="9999-12-31">
        </div>
    </div>

    <div class="form-group row">
        <label for="address_country_id" class="col-12 col-sm-3 col-form-label" required>País de residencia *</label>
        <div class="col-12 col-sm-9">
            <select class="custom-select address_country_id" name="address_country_id">
                @foreach($countries as $country)
                <option value="{{ $country->idx }}" @if ($country->idx == '32AR') selected
                    @endif>{{ $country->name_ter }}</option>
                @endforeach
            </select>
        </div>
    </div>

    <div class="form-group row">
        <label for="address_state_id" class="col-12 col-sm-3 col-form-label">Provincia *</label>
        <div class="col-12 col-sm-9">
            <select class="custom-select address_state_id" name="address_state_id" required>
                <option value="" selected></option>
                @foreach($states as $state)
                <option value="{{ $state->id }}">{{ $state->state }}</option>
                @endforeach
            </select>
            <input type="text" class="form-control address_state_text" name="address_state_text" maxlength="50" style="display: none" required />
        </div>
    </div>

    <div class="form-group row">
        <label for="-" class="col-12 col-sm-3 col-form-label">Localidad *</label>
        <div class="col-12 col-sm-9">
            <select class="custom-select address_city_id" name="address_city_id" required></select>
            <input type="text" class="form-control address_city_text" name="address_city_text" maxlength="50" style="display: none" required />
        </div>
    </div>

    <div class="form-group row">
        <label for="street_name" class="col-12 col-sm-3 col-form-label">Calle *</label>
        <div class="col-12 col-sm-3">
            <input type="text" class="form-control street_name" name="street_name" required>
        </div>
        <label for="street_number" class="col-12 col-sm-3 col-form-label">Número *</label>
        <div class="col-12 col-sm-3">
            <input type="text" class="form-control street_number" name="street_number" required>
        </div>
    </div>

    <div class="form-group row">
        <label for="floor" class="col-12 col-sm-3 col-form-label">Piso</label>
        <div class="col-12 col-sm-3">
            <input type="text" class="form-control floor" name="floor">
        </div>
        <label for="apartment" class="col-12 col-sm-3 col-form-label">Departamento</label>
        <div class="col-12 col-sm-3">
            <input type="text" class="form-control apartment" name="apartment">
        </div>
    </div>

    <div class="form-group row">
        <label for="address_zip" class="col-12 col-sm-3 col-form-label">Código Postal *</label>
        <div class="col-12 col-sm-9">
            <input type="text" class="form-control address_zip" name="address_zip" required>
        </div>
    </div>

    <div class="form-group row">
        <label for="email" class="col-12 col-sm-3 col-form-label">E-mail *</label>
        <div class="col-12 col-sm-9">
            <input type="email" class="form-control email" name="email" required>
        </div>
    </div>

    <div class="form-group row">
        <label class="col-12 col-sm-3 col-form-label">Teléfono</label>
        <div class="form-group col-12 col-sm-3">
            <label for="phone_country">Cod País *</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">+</span>
                </div>
                <input type="number" class="form-control no-arrows phone_country" name="phone_country" min="0" value="54" required>
            </div>
        </div>
        <div class="form-group col-12 col-sm-3">
            <label for="phone_area">Cod Área *</label>
            <div class="input-group">
                <div class="input-group-prepend">
                    <span class="input-group-text">(</span>
                </div>
                <input type="number" class="form-control no-arrows phone_area" name="phone_area" min="0" required>
                <div class="input-group-append">
                    <span class="input-group-text">)</span>
                </div>
            </div>
        </div>
        <div class="form-group col-12 col-sm-3">
            <label for="phone_number">Número *</label>
            <input type="number" class="form-control no-arrows phone_number" name="phone_number" min="0" required>
        </div>
    </div>

    <div class="form-row justify-content-center">
        @if (isset($edit))
        <button type="submit" class="btn btn-primary" id="editPerson">Guardar Cambios</button>
        @else
        <button type="submit" class="btn btn-primary" id="addNewPerson">Agregar Persona</button>
        @endif
    </div>
</form>
