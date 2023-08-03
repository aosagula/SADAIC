require('formdata-polyfill')

const CustomStorage = require('./storage').PeopleStorage;
const currency = require('currency.js');

window.customStorage = new CustomStorage('jingle.registration');

const userType = window.location.pathname.split('/')[1];

const requestOptions = [
    [
        { id: 1, name: 'Aviso Original' },
        { id: 2, name: 'Reducción' },
        { id: 3, name: 'Renovación' },
        { id: 4, name: 'Exportación' }
    ],
    [
        { id: 1, name: 'Aviso Original' },
        { id: 3, name: 'Renovación' },
        { id: 4, name: 'Exportación' }
    ]
];

let personSearchResults = [];

let personSelected = null;

// Campos excluidos del submit
const excludedFields = ['ad_number'];

// Función auxiliar para convertir la
// información de un form en un objeto
const serializeForm = function (form) {
    let obj = {};
    let formData = new FormData(form);
    for (let key of formData.keys()) {
        obj[key] = formData.get(key);
    }
    return obj;
};

$('#ad_details_territory_states').select2();

// Selección de trámite regular / especial
$('input[name="is_special"]').on('change', function() {
    const options = requestOptions[$(this).val()];

    $('#request_action_id').empty();
    options.forEach(option => {
        $('#request_action_id').append(`<option value="${ option.id }">${ option.name }</option>`);
    });

    if ($(this).val() == 0) {
        $('#validity').attr('min', 1);

        $('.special').hide();
        $('.special :input').attr('disabled', true);
        $('.regular :input').attr('disabled', false).change();
        $('.regular').show();
    } else {
        $('#validity').attr('min', 3);
        if (parseInt($('#validity').val()) < 3) $('#validity').val(3);

        $('.regular').hide();
        $('.regular :input').attr('disabled', true);
        $('.special :input').attr('disabled', false).change();
        $('.special').show();
    }
});
$('input[name="is_special"]:checked').change();

$('select[name="request_action_id"]').on('change', function() {
    if ($(this).val() == 4) {
        $('.local').hide();
        $('.local :input').attr('disabled', true);
        $('.foreign :input').attr('disabled', false)
        $('select[name="broadcast_territory_id"]').change();
        $('.foreign').show();
    } else {
        $('.foreign').hide();
        $('.foreign :input').attr('disabled', true);
        $('.local :input').attr('disabled', false).change();
        $('.local').show();
    }
});
$('select[name="request_action_id"]').change();

// Selección de cantidad de avisos en campañas especiales
$('#ad_number').on('change', (event) => {
    let newAmmount = $(event.target).val();
    const currentAmmount = $('.ads_duration:enabled').length;

    // Validación
    if (newAmmount < 3) {
        $(event.target).val(3);
        newAmmount = 3;
    }

    // Menos avisos que antes
    if (newAmmount < currentAmmount) {
        const deleteAmmount = newAmmount - currentAmmount;

        $('.ads_durationWrapper').slice(deleteAmmount).remove();

    // Más avisos que antes
    } else if (newAmmount > currentAmmount) {
        for (let i = currentAmmount; i < newAmmount; i++) {
            const $newWrapper = $('.ads_durationWrapper').first().clone();
            $newWrapper.val(1);
            $('label', $newWrapper).text(`Aviso ${i + 1}`);
            $newWrapper.appendTo($('.ads_durationWrapper').parent());
        }
    }
});

// Direcciones de correo electrónico
const mailMaskOptions = {
    translation: {
        "A": { pattern: /[\w@\-.+]/, recursive: true }
    }
};

// https://emailregex.com
const mailRegex = /^(([^<>()\[\]\\.,;:\s@"]+(\.[^<>()\[\]\\.,;:\s@"]+)*)|(".+"))@((\[[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}\.[0-9]{1,3}])|(([a-zA-Z\-0-9]+\.)+[a-zA-Z]{2,}))$/;

$('#advertiser_email, #agency_email, #applicant_email')
.mask("A", mailMaskOptions)
.on('blur', function(event) {
    if (!mailRegex.test($(event.target).val())) {
        $(event.target).addClass('is-invalid');
    }
})
.on('change keypress', function(event) {
    $(event.target).removeClass('is-invalid');
});

// Territorio de difusión
$('#broadcast_territory_id').on('change', function() {
    if ($(this).val() == 1) {
        $('.provincial').hide();
        $('.provincial :input').attr('disabled', true);
        customStorage.removeField('territory_id');
    } else if ($(this).val() == 2) {
        $('.provincial :input').attr('disabled', false).change();
        $('.provincial').show();
    }
});
$('#broadcast_territory_id').change();

// Acuerdo de autores
$('input[name="authors_agreement"]').on('change', function() {
    if ($(this).val() == '1') {
        $('.agreed :input').attr('disabled', false);
        $('.agreed').show();
    } else if ($(this).val() == '0') {
        $('.agreed').hide();
        $('.agreed :input').attr('disabled', true);
    }
});
$('input[name="authors_agreement"]:checked').change();

// Tarifa
$('#tariff').on('focus', function () {
    $(this).one('mouseup', function () {
        $(this).select();
        return false;
    })
    .select();
});

$('#tariff').on('blur', function() {
    const value = currency($(this).val()).value;
    $(this).val(value.toFixed(2));
});

// Acuerdo de autores
$('#payer').on('change', function() {
    if ($(this).val() == 3) {
        $('.represent :input').attr('disabled', false).change();
        $('.represent').show();
    } else {
        $('.represent').hide();
        $('.represent :input').attr('disabled', true);
        customStorage.removeField('tariff_representation');
    }
});
$('#payer').change();

// Tabla de derechohabientes
window.$peopleTable = $('#peopleTable').DataTable({
    paging: false,
    searching: false,
    ordering:  false,
    info: false,
    language: {
        zeroRecords: 'Haga click en "Agregar Persona" para comenzar...'
    },
});

/**
 * Búsqueda de socios
*/
// Capturamos los submits del form de búsqueda y los redirigimos
// al evento click
$('#memberSearchForm').on('submit', function(event) {
    $('#memberSearchButton').click();

    event.preventDefault();
});

// Búsqueda de autores
$('#memberSearchButton').on('click', function(event) {
    $(event.target).attr('disabled', true);

    const $results = $('#memberSearchResults');

    $results.empty();
    personSearchResults = [];

    if ($('#memberSearchQuery').val().trim() == '') {
        $results.append(`<div class="alert alert-danger" role="alert">No se encontraron socios con el criterio ingresado.</div>`);
        return;
    }

    $.ajax({
        url: `/${ userType }/work/search_author`,
        method: 'POST',
        dataType: "json",
        data: {
            query: $('#memberSearchQuery').val()
        },
        complete: ({responseJSON: data}) => {
            personSearchResults = data;

            if (data.length) {
                $results.append(`<h3>${data.length} socios encontrado(s):</h3>`);

                let $resultsList = $('<ul></ul>');
                $resultsList.empty();
                $results.append($resultsList);

                // Parseamos los resultados
                data.forEach((member) => {
                    let item = `<a href="#" class="selectPerson" data-id="${ member.codanita }">`;
                    item += `<span class="capitalize">${ member.nombre.toLowerCase() }</span>`;
                    item += `</a></li>`;
                    if (member.codanita) {
                        item = `<li>Socio #<strong>${ member.codanita }</strong>: ` + item;
                    } else {
                        item = `<li>` + item;
                    }

                    $resultsList.append(item);
                });
            } else {
                $results.append(`<div class="alert alert-danger" role="alert">No se encontraron socios con el criterio ingresado.</div>`);
            }

            setTimeout(function() {
                $(event.target).attr('disabled', false);
            }, 500);
        },
        headers: {
            'X-CSRF-TOKEN': $('meta[name="csrf-token"]').attr('content')
        }
    });
});

// Datos iniciales del modal para agregar personas
$('#addPersonModal').on('shown.bs.modal', () => {
    $('.address_country_id').trigger('change');
    $('.address_state_id').trigger('change');
});

$('#addPersonModal').on('show.bs.modal', () => {
    $('#memberSearchForm')[0].reset();
    $('#memberSearchResults').html('');
    $('#noMemberForm')[0].reset();
});

// Agregar socio
$('#memberSearchResults').on('click', '.selectPerson', function(event) {
    event.preventDefault();

    personSelected = personSearchResults.find(m => m.codanita == $(this).data('id'));

    $peopleTable.row.add([
        `<span class="capitalize">${ personSelected.nombre.toLowerCase() }</span>`,
        `${ personSelected.num_doc }`,
        `${ personSelected.codanita }`,
        `<button type="button" class="btn btn-link removePerson"><i class="far fa-trash-alt"></i></button>`,
        ``
    ]).node().id = personSelected.idx;

    $peopleTable.draw();

    customStorage.addPerson({
        type: 'member',
        member_id: personSelected.codanita,
        doc_number: personSelected.num_doc,
        idx: personSelected.idx
    });

    $('#addPersonModal').modal('hide');
});

// Agregar no socio
$('#noMemberForm').on('submit', function(event) {
    event.preventDefault();

    // Convertimos la información del formulario en un objeto
    let personSelected = {};
    $('#noMemberForm').serializeArray().forEach(e => { personSelected[e.name] = e.value });

    // Insertamos el nuevo registro en la tabla
    $peopleTable.row.add([
        `<span class="capitalize">${ personSelected.name.toLowerCase() }</span>`,
        `${ personSelected.doc_number }`,
        ``,
        `<button type="button" class="btn btn-link removePerson"><i class="far fa-trash-alt"></i></button>`,
        `<button type="button" class="btn btn-link editPerson" data-toggle="modal" data-target="#editPersonModal"><i class="far fa-edit"></i></button>`
    ]).node().id = personSelected.doc_number;

    $peopleTable.draw();

    // Guardamos en el storage
    customStorage.addPerson({
        type: 'no-member',
        ...personSelected
    });

    // Escondemos el modal
    $('#addPersonModal').modal('hide');

    // Seleccionamos la pestaña de socios
    $('#member-tab').trigger('click');
});

// Datos iniciales del modal para editar personas
$('#editPersonModal').on('show.bs.modal', function(event) {
    const $target = $(event.relatedTarget);
    const row = $target.parents('tr').get(0);
    const $row = $peopleTable.row(row);

    const docNumber = $peopleTable.cell({row: $row.index(), column: 1}).data();
    const memberId = '';

    const person = customStorage.getPerson(docNumber, memberId);
    const $form = $('#editNoMemberForm');

    currentRow = $row.index();

    $('.name', $form).val(person.name);
    $('.doc_type', $form).val(person.doc_type);
    $('.doc_number', $form).val(person.doc_number);
    $('.birth_country_id', $form).val(person.birth_country_id);
    $('.birth_date', $form).val(person.birth_date);

    $('.address_country_id', $form).val(person.address_country_id);
    $('.address_country_id', $form).change();
    if (person.address_country_id == '32AR') {
        $('.address_state_id', $form).val(person.address_state_id);
        $('.address_state_id', $form).change();
        $('.address_city_id', $form).val(person.address_city_id);
    } else {
        $('.address_state_text', $form).val(person.address_state_text)
        $('.address_city_text', $form).val(person.address_city_text);
    }

    $('.street_name', $form).val(person.street_name);
    $('.street_number', $form).val(person.street_number);
    $('.floor', $form).val(person.floor);
    $('.apartment', $form).val(person.apartment);
    $('.address_zip', $form).val(person.address_zip);
    $('.email', $form).val(person.email);
    $('.phone_country', $form).val(person.phone_country);
    $('.phone_area', $form).val(person.phone_area);
    $('.phone_number', $form).val(person.phone_number);
});

// Editar no socios
$('#editNoMemberForm').on('submit', function(event) {
    event.preventDefault();

    // Recuperamos el nro de documento y los demás datos del storage de la persona a actualizar
    const oldDocNumber = $peopleTable.cell({row: currentRow, column: 1}).data();

    // Recuperamos los datos de la persona a modificar
    let personSelected = customStorage.getPerson(oldDocNumber, '');

    // Actualizamos los datos con lo ingresado en el formulario
    $('#editNoMemberForm').serializeArray().forEach(e => { personSelected[e.name] = e.value });

    // Actualizamos el storage
    customStorage.updatePerson(oldDocNumber,'', personSelected);

    // Actualizamos la tabla
    $peopleTable.cell({row: currentRow, column: 0}).data(`<span class="capitalize">${ personSelected.name.toLowerCase() }</span>`);
    $peopleTable.cell({row: currentRow, column: 1}).data(`${ personSelected.doc_number }`);

    // Escondemos el modal
    $('#editPersonModal').modal('hide');

    // Reseteamos el form
    $('#editNoMemberForm').trigger("reset");
});

// Quitar personas
$('#peopleTable').on('click', '.removePerson', (event) => {
    $(event.currentTarget).attr('disabled', true);

    const row = $(event.target).parents('tr').get(0);
    const idx = $peopleTable.row(row).index();
    const memberId = $peopleTable.cell({row: idx, column: 2}).data();
    const docNumber = $peopleTable.cell({row: idx, column: 1}).data();

    // Si tiene una distribución asociada (ya fue guardado) la eliminamos
    customStorage.getPerson(docNumber, memberId);

    // Quitamos el dato del storage
    customStorage.removePerson(docNumber, memberId);

    // Quitamos el row de la tabla
    $peopleTable.row(row).remove().draw();
});

// Guardar borrador
$('#saveRegister').on('click', (event) => {
    const $buttton = $(event.target);
    $buttton.attr('disabled', true);

    // Elegimos el método en función de si ya se hizo un envío previo o no
    let method = 'POST';
    let url = `/${ userType }/jingles/`
    if (customStorage.getField('id')) {
        method = 'PUT';
        url = `/${ userType }/jingles/${ customStorage.getField('id') }`;
    }

    // Parche IE11
    const data = customStorage.get();
    if (navigator.userAgent.indexOf('Trident/7.0') !== -1) {
        if (data.air_date) {
            data.air_date = data.air_date.split('/').reverse().join('-')
        }
    }

    // Hacemos el envío
    axios({
        method,
        url,
        data
    })
    .then(({ data }) => {
        // Guardamos el id
        customStorage.setField('id', data.id);
        // Reemplazamos la página de creación por la de edición
        history.replaceState(null, '', `/${ userType }/jingles/${data.id}/edit`);
        // Mostramos mensaje
        toastr.success('Se guardó correctamente la solicitud.');
    })
    .catch(({ response }) => {
        // Errores de validación
        if (response.status == 422) {
            for (const attr in response.data.errors) {
                toastr.warning(response.data.errors[attr]);
            }
        // Otros errores
        } else {
            toastr.error('Ocurrió un error imprevisto mientras se intentaba procesar la solicitud');
        }
    })
    .finally(() => {
        // Habilitamos el botón de envío
        setTimeout(function() {
            $buttton.attr('disabled', false);
        }, 500);
    });
});

// Dump inicial del form
const formData = serializeForm(document.getElementById('jingleRegistrationForm'));
for(let key in formData) {
    if (excludedFields.includes(key)) {
        continue;
    }

    if (key.slice(-2) == '[]') {
        let value = '';
        if (key == 'territory_id[]') {
            value = $('#ad_details_territory_states').select2('data').map(e => e.id);
        } else {
            value = $(`.${key.slice(0, -2)}:enabled`).get().map(e => e.value);
        }

        customStorage.setField(key.slice(0, -2), value);
        continue;
    }

    if (key.slice(-2) == '[]') {
        const value = $(`.${key.slice(0, -2)}:enabled`).get().map(e => e.value);
        customStorage.setField(key.slice(0, -2), value);
        continue;
    }

    customStorage.setField(key, formData[key]);
}

$('#jingleRegistrationForm :input:checkbox:not(:checked)').each((idx, e) => {
    customStorage.setField(e.name, e.checked ? '1': '0');
});

// Actualización de cambios
$('#jingleRegistrationForm :input:not(:checkbox)').on('change', function(event) {
    // Si el campo está en la lista de excluidos, lo omitimos
    if (excludedFields.includes(event.target.name)) {
        return;
    }

    // Detenemos la propagación del evento, después de guardarlo en el storage
    // no tiene que sufrir modificaciones sin disparar otro evento
    event.stopPropagation();

    // Si el campo modificado es el CUIT, antes de cambiarlo nos aseguramos de
    // sacar los guiones y demás caracteres no numéricos
    if (event.target.name.slice(-6) == '[cuit]') {
        $(event.target).val($(event.target).val().replace(/[^0-9]/g, ''));
    }

    // Si el campo modificado es parte de un arreglo
    if (event.target.name.slice(-2) == '[]') {
        let value = '';
        // Y si es un select2
        if (event.target.classList.contains('select2-hidden-accessible')) {
            // Recuperamos el nuevo valor utilizando la API de select 2
            value = $(event.target).select2('data').map(e => e.id);
        // Y si es un select standard
        } else if (event.target.tagName == 'SELECT') {
            // Recuperamos el nuevo utilizando la API de HTML5
            value = [event.target.value];
        // Y si no es un select
        } else {
            // Armamos un arreglo con todos los valores del arreglo de campos
            value = $(`.${event.target.name.slice(0, -2)}:enabled`).get().map(e => e.value);
        }

        // Lo guardamos excluyendo los corchetes del nombre
        customStorage.setField(event.target.name.slice(0, -2), value);
        return;
    }

    // Si el campo modificado es el arancel (único monetario) lo
    // guardamos después de haberlo pasado por currency para normalizarlo
    if (event.target.name == 'authors_tariff') {
        customStorage.setField(
            event.target.name,
            currency($(event.target).val()).value
        );
        return;
    }

    // Si no hay ninguna regla particular, guardamos el campo actualizado
    // en el storage
    customStorage.setField(event.target.name, $(event.target).val());
});

// Los checkbox reciben un tratamiento diferente
$('#jingleRegistrationForm :input:checkbox').on('input', function(event) {
    // Si el campo está en la lista de excluidos, lo omitimos
    if (excludedFields.includes(event.target.name)) {
        return;
    }

    // Detenemos la propagación del evento, después de guardarlo en el storage
    // no tiene que sufrir modificaciones sin disparar otro evento
    event.stopPropagation();

    // Guardamos el campo actualizado en el storage
    customStorage.setField(event.target.name, event.target.checked ? '1': '0');
});

// Envío de solicitud
$('#sendRegister').on('click', function(event) {
    // Desabilitamos el botón
    const $buttton = $(event.target);
    $buttton.attr('disabled', true);

    // Elegimos el método en función de si ya se hizo un envío previo o no
    let method = 'POST';
    let url = `/${ userType }/jingles/`
    if (customStorage.getField('id')) {
        method = 'PUT';
        url = `/${ userType }/jingles/${ customStorage.getField('id') }`;
    }

    // Parche IE11
    const data = customStorage.get();
    if (navigator.userAgent.indexOf('Trident/7.0') !== -1) {
        if (data.air_date) {
            data.air_date = data.air_date.split('/').reverse().join('-')
        }
    }


    // Enviamos los datos y agregamos el status
    axios({
        method,
        url,
        data: {
            ...data,
            status_id: 1
        }
    })
    .then(() => {
        // Mostramos mensaje
        toastr.success('Se envió correctamente la solicitud.');

        // Volvemos al listado (el form ya no se puede modificar)
        window.location = `/${ userType }/jingles/`;
    })
    .catch(({ response }) => {
        // Errores de validación
        if (response.status == 422) {
            for (const attr in response.data.errors) {
                toastr.warning(response.data.errors[attr]);
            }
        // Otros errores
        } else {
            toastr.error('Ocurrió un error imprevisto mientras se intentaba procesar la solicitud');
        }
    })
    .finally(() => {
        // Habilitamos el botón de envío
        setTimeout(function() {
            $buttton.attr('disabled', false);
        }, 500);
    });
});

// Solo IE11
if (navigator.userAgent.indexOf('Trident/7.0') !== -1) {
    // Corregimos las fechas
    $('input[type="date"]').each((i, e) => $(e).val( $(e).val().split('-').reverse().join('/') ))

    // Enmascaramos los datepicker
    $('input[type="date"]').mask('00/00/0000', {
        clearIfNotMatch: true,
        selectOnFocus: true
    });
}
