$('.address_country_id').on('change', function(event) {
    const $parentForm = $(event.target).parents('form');

    // Si es Argentina
    if ($('.address_country_id', $parentForm).val() == '32AR') {
        // Desactivo y escondo los campos de texto para ciudad y provincia
        $('.address_state_text', $parentForm).prop('disabled', true).hide();
        $('.address_city_text', $parentForm).prop('disabled', true).hide();

        // Activo y muestro los select para ciudad y provincia
        $('.address_state_id', $parentForm).prop('disabled', false).show();
        $('.address_city_id', $parentForm).prop('disabled', ($('.address_state_id', $parentForm).val() == '')).show();
    } else {
        // Desactivo y escondo los select para ciudad y provincia
        $('.address_state_id', $parentForm).prop('disabled', true).hide();
        $('.address_city_id', $parentForm).prop('disabled', true).hide();

        // Activo y muestro los campos de texto para ciudad y provincia
        $('.address_state_text', $parentForm).prop('disabled', false).show();
        $('.address_city_text', $parentForm).prop('disabled', false).show();
    }
});

$('.address_state_id').on('change', function(event) {
    const $parentForm = $(event.target).parents('form');

    // Si hay una provincia seleccionada
    if ($('.address_state_id', $parentForm).val() != '') {
        // Filtro las ciudades correspondientes a la provincia
        const cities = citiesOptions.filter(e => e.state_id == $('.address_state_id', $parentForm).val());

        // Cargo en el select las ciudades
        $('.address_city_id', $parentForm).empty();
        cities.forEach(city => {
            $('.address_city_id', $parentForm).append(`<option value="${city.id}">${city.city}</option>`);
        });

        // Activo el select de ciudades
        $('.address_city_id', $parentForm).prop('disabled', false);
    } else {
        // Desactivo el select de ciudades
        $('.address_city_id', $parentForm).val('').prop('disabled', true);
    }
});

$('.birth_country_id').on('change', function(event) {
    const $parentForm = $(event.target).parents('form');

    // Si es Argentina
    if ($('.birth_country_id', $parentForm).val() == '32AR') {
        // Desactivo y escondo los campos de texto para ciudad y provincia
        $('.birth_state_text', $parentForm).prop('disabled', true).hide();
        $('.birth_city_text', $parentForm).prop('disabled', true).hide();

        // Activo y muestro los select para ciudad y provincia
        $('.birth_state_id', $parentForm).prop('disabled', false).show();
        $('.birth_city_id', $parentForm).prop('disabled', ($('.birth_state_id', $parentForm).val() == '')).show();
    } else {
        // Desactivo y escondo los select para ciudad y provincia
        $('.birth_state_id', $parentForm).prop('disabled', true).hide();
        $('.birth_city_id', $parentForm).prop('disabled', true).hide();

        // Activo y muestro los campos de texto para ciudad y provincia
        $('.birth_state_text', $parentForm).prop('disabled', false).show();
        $('.birth_city_text', $parentForm).prop('disabled', false).show();
    }
});

$('.birth_state_id').on('change', function(event) {
    const $parentForm = $(event.target).parents('form');

    // Si hay una provincia seleccionada
    if ($('.birth_state_id', $parentForm).val() != '') {
        // Filtro las ciudades correspondientes a la provincia
        const cities = citiesOptions.filter(e => e.state_id == $('.birth_state_id', $parentForm).val());

        // Cargo en el select las ciudades
        $('.birth_city_id', $parentForm).empty();
        cities.forEach(city => {
            $('.birth_city_id', $parentForm).append(`<option value="${city.id}">${city.city}</option>`);
        });

        // Activo el select de ciudades
        $('.birth_city_id', $parentForm).prop('disabled', false);
    } else {
        // Desactivo el select de ciudades
        $('.birth_city_id', $parentForm).val('').prop('disabled', true);
    }
});