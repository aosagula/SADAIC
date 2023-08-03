const CustomStorage = require('./storage').PeopleStorage;
const userType = window.location.pathname.split('/')[1];

// Select de funciones de personas
window.selectFn = (name, empty = false, value = null) => {
    let output = '';
    output += `<select class="custom-select ${ name }">`;
    if (empty && !value) {
        output += `<option value="" selected>Seleccione el rol...</option>`;
    }
    for(const role in roleOptions) {
        output += `<option value="${ roleOptions[role].code }" ${ roleOptions[role].code == value ? 'selected' : '' }>${ roleOptions[role].description.trim() }</option>`;
    }
    output += '</select>';

    return output;
}

window.customStorage = new CustomStorage('work.register');

let personSearchResults = [];
let personSelected = null;
let dndaEnabled = false;
let currentRow = null;

// Setup de la tabla de distribución
window.$distributionTable = $('#distributionTable').DataTable({
    paging: false,
    searching: false,
    ordering:  false,
    info: false,
    columnDefs: [
        {
            targets: [0, 4, 5, 6],
            render: function(data, type, row, meta){
                if(type === 'display'){
                   var api = new $.fn.dataTable.Api(meta.settings);

                   var $el = $('input, select, textarea', api.cell({ row: meta.row, column: meta.col }).node());

                   var $html = $(data).wrap('<div/>').parent();

                   if($el.prop('tagName') === 'INPUT'){
                      $('input', $html).attr('value', $el.val());
                      if($el.prop('checked')){
                         $('input', $html).attr('checked', 'checked');
                      }
                   } else if ($el.prop('tagName') === 'TEXTAREA'){
                      $('textarea', $html).html($el.val());

                   } else if ($el.prop('tagName') === 'SELECT'){
                      $('option:selected', $html).removeAttr('selected');
                      $('option', $html).filter(function(){
                         return ($(this).attr('value') === $el.val());
                      }).attr('selected', 'selected');
                   }

                   data = $html.html();
                }

                return data;
             }
        }
    ],
    responsive: {
        breakpoints: [
            { name: 'desktop',  width: Infinity },
            { name: 'tablet-l', width: 1200 },
            { name: 'tablet-p', width: 1024 },
            { name: 'mobile-l', width: 768 },
            { name: 'mobile-p', width: 480 }
        ],
        details: {
            renderer: function ( api, rowIdx, columns ) {
                var data = $.map( columns, function ( col, i ) {
                    return col.hidden ?
                        '<tr data-dt-row="'+col.rowIndex+'" data-dt-column="'+col.columnIndex+'">'+
                            '<td>'+col.title+':'+'</td> '+
                            '<td>'+col.data+'</td>'+
                        '</tr>' :
                        '';
                } ).join('');

                return data ?
                    $('<table/>').append( data ) :
                    false;
            }
        }
    }
});

// https://www.gyrocode.com/articles/jquery-datatables-form-inputs-with-responsive-extension/
$('#distributionTable tbody').on('keyup change', '.child input, .child select, .child textarea', function(e){
    let $el = $(this);
    let rowIdx = $el.closest('tr').data('dt-row');
    let colIdx = $el.closest('tr').data('dt-column');
    let cell = $distributionTable.cell({ row: rowIdx, column: colIdx }).node();

    $('input, select, textarea', cell).val($el.val());
    if($el.is(':checked')) {
       $('input', cell).prop('checked', true);
    } else {
       $('input', cell).removeProp('checked');
    }
});

// Setup de la tabla de adjuntos
const $attachmentsTable = $('#attachmentsTable').DataTable({
    paging: false,
    searching: false,
    ordering:  false,
    info: false,
    columnDefs: [
        { className: 'd-block d-md-table-cell', targets: [0, 1] }
    ]
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

/**
 * ABM de personas de la distribución
 */
// Agregar socio
$('#memberSearchResults').on('click', '.selectPerson', function() {
    personSelected = personSearchResults.find(m => m.codanita == $(this).data('id'));

    const publicSuggestion = 100 - getTotal('public') > 0 ? 100 - getTotal('public') : 0;
    const mechanicSuggestion = 100 - getTotal('mechanic') > 0 ? 100 - getTotal('mechanic') : 0;
    const syncSuggestion = 100 - getTotal('sync') > 0 ? 100 - getTotal('sync') : 0;

    $distributionTable.row.add([
        `${ selectFn('fn', true) }`,
        `${ personSelected.codanita }`,
        `<span class="capitalize">${ personSelected.nombre.toLowerCase() }</span>`,
        `${ personSelected.num_doc }`,
        `<input type="number" class="form-control publicAmount" name="public" step="0.01" min="0" max="100" value="${ publicSuggestion }">`,
        `<input type="number" class="form-control mechanicAmount" name="mechanic" step="0.01" min="0" max="100" value="${ mechanicSuggestion }">`,
        `<input type="number" class="form-control syncAmount" name="sync" step="0.01" min="0" max="100" value="${ syncSuggestion }">`,
        `<button type="button" class="btn btn-link removePerson"><i class="far fa-trash-alt"></i></button>`,
        ``
    ]).draw();

    $('#addPersonModal').modal('hide');

    // Guardamos
    customStorage.addPerson({
        type: 'member',
        member_id: personSelected.codanita,
        doc_number: personSelected.num_doc,
        public: publicSuggestion,
        mechanic: mechanicSuggestion,
        sync: syncSuggestion,
    });

    updateTotal();
});

// Agregar no socio
$('#noMemberForm').on('submit', function(event) {
    event.preventDefault();

    // Convertimos la información del formulario en un objeto
    let personSelected = {};
    $('#noMemberForm').serializeArray().forEach(e => { personSelected[e.name] = e.value });

    // Calculamos porcentajes para "sugerir"
    const publicSuggestion = 100 - getTotal('public') > 0 ? 100 - getTotal('public') : 0;
    const mechanicSuggestion = 100 - getTotal('mechanic') > 0 ? 100 - getTotal('mechanic') : 0;
    const syncSuggestion = 100 - getTotal('sync') > 0 ? 100 - getTotal('sync') : 0;

    // Insertamos el nuevo registro en la tabla
    const idx = $distributionTable.row.add([
        `${ selectFn('fn', true) }`,
        ``,
        `<span class="capitalize">${
            personSelected.name
            .toLowerCase()
            .split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ')
        }</span>`,
        `${ personSelected.doc_number }`,
        `<input type="number" class="form-control publicAmount" name="public" step="0.01" min="0" max="100" value="${ publicSuggestion }">`,
        `<input type="number" class="form-control mechanicAmount" name="mechanic" step="0.01" min="0" max="100" value="${ mechanicSuggestion }">`,
        `<input type="number" class="form-control syncAmount" name="sync" step="0.01" min="0" max="100" value="${ syncSuggestion }">`,
        `<button class="btn btn-link removePerson"><i class="far fa-trash-alt"></i></button>`,
        `<button class="btn btn-link editPerson" data-toggle="modal" data-target="#editPersonModal"><i class="far fa-edit"></i></button>`
    ]).draw().index();

    // Escondemos el modal
    $('#addPersonModal').modal('hide');

    // Seleccionamos la pestaña de socios
    $('#member-tab').trigger('click');

    // Reseteamos el form
    $('#noMemberForm').trigger("reset");

    // Agregamos la solicitud de copia del documento
    addFileRequest(
        `file_no-member_doc`,
        `Documento de <strong>${
            personSelected.name
            .toLowerCase()
            .split(' ')
            .map(word => word.charAt(0).toUpperCase() + word.slice(1))
            .join(' ')
        }</strong>`,
        'image/png, image/jpeg, application/pdf',
        { doc_number: personSelected.doc_number }
    );

    // Guardamos en el storage
    customStorage.addPerson({
        type: 'no-member',
        ...personSelected,
        public: publicSuggestion,
        mechanic: mechanicSuggestion,
        sync: syncSuggestion
    });

    // Actualizamos los totales de la tabla
    updateTotal();
});

// Editar no socio
$('#editNoMemberForm').on('submit', function(event) {
    event.preventDefault();

    // Recuperamos el nro de documento y los demás datos del storage de la persona a actualizar
    const oldMemberId = $distributionTable.cell({row: currentRow, column: 1}).data();
    const oldDocNumber = $distributionTable.cell({row: currentRow, column: 3}).data();

    // Recuperamos los datos de la persona a modificar
    let personSelected = customStorage.getPerson(oldDocNumber, oldMemberId);

    // Actualizamos los datos con lo ingresado en el formulario
    $('#editNoMemberForm').serializeArray().forEach(e => { personSelected[e.name] = e.value });

    // Actualizamos el storage
    customStorage.updatePerson(oldDocNumber, oldMemberId, personSelected);

    // Actualizamos la tabla
    $distributionTable.cell({row: currentRow, column: 2}).data(`<span class="capitalize">${ personSelected.name.toLowerCase() }</span>`);
    $distributionTable.cell({row: currentRow, column: 3}).data(`${ personSelected.doc_number }`);

    // Actualizamos el n° de documento de los adjuntos
    $(`#attachmentsTable input[value=${ oldDocNumber }]`).val(personSelected.doc_number);

    // Escondemos el modal
    $('#editPersonModal').modal('hide');

    // Reseteamos el form
    $('#editNoMemberForm').trigger("reset");
});

// Quitar personas
$('#distributionTable').on('click', '.removePerson', (event) => {
    $(event.currentTarget).attr('disabled', true);

    const row = $(event.target).parents('tr').get(0);
    const idx = $distributionTable.row(row).index();
    const memberId = $distributionTable.cell({row: idx, column: 1}).data();
    const docNumber = $distributionTable.cell({row: idx, column: 3}).data();

    // Si tiene una distribución asociada (ya fue guardado) la eliminamos
    let person = customStorage.getPerson(docNumber, memberId);
    if (person.distribution_id) {
        axios.post(`/${ userType }/work/distribution/delete`, {
            registration_id: customStorage.getField('registration_id'),
            distribution_id: person.distribution_id
        });
    }

    // Quitamos el row de la tabla
    $distributionTable.row(row).remove().draw();

    // Quitamos los adjuntos asociados
    removeFileRequest('', docNumber, memberId);

    // Quitamos el dato del storage
    customStorage.removePerson(docNumber, memberId);

    // Actualizamos los totales
    updateTotal();
});

/**
 * Eventos
 */
// Quitar la marca de error si cambiamos el valor del select
$('#distributionTable').on('change', '.custom-select', (e) => {
    $(e.target).removeClass('is-invalid');
});

// Cambio de función de las personas
let previous_value = -1;
$('#distributionTable').on('click', '.fn', function() {
    previous_value = $(this).val();
}).on('change', '.fn', (event) => {
    const row = $(event.target).parents('tr').get(0);
    const $row = $distributionTable.row(row);
    const current_value = $(event.target).val();
    const memberId = $row.data()[1];
    const docNumber = $row.data()[3];

    // Si ya no es editor, quitamos la solicitud del archivo
    if (previous_value == 'E' || previous_value == 'SE') {
        removeFileRequest(`file_${ previous_value == 'E' ? 'editor' : 'subeditor' }_contract`, docNumber, memberId);
        removeFileRequest(`file_${ previous_value == 'E' ? 'editor' : 'subeditor' }_triage`, docNumber, memberId)
    }

    // Si ahora es editor, agregamos las solicitudes de documentos
    if (current_value == 'E' || current_value == 'SE') {
        addFileRequest(
            `file_${ current_value == 'E' ? 'editor' : 'subeditor' }_contract`,
            `Contrato de <strong>${ $row.data()[2] }</strong>`,
            'image/png, image/jpeg, application/pdf',
            { member_id: memberId, doc_number: docNumber }
        );

        addFileRequest(
            `file_${ current_value == 'E' ? 'editor' : 'subeditor' }_triage`,
            `Control de tiraje <strong>${ $row.data()[2] }</strong>`,
            'image/png, image/jpeg, application/pdf',
            { member_id: memberId, doc_number: docNumber }
        );
    }

    if (current_value == 'A' || current_value == 'CA') {
        $('#scriptAttachment input[type="file"]').prop('disabled', false);
        $('#scriptAttachment').show();
    } else if (previous_value == 'A' || previous_value == 'CA') {
        $('#scriptAttachment').hide();
        $('#scriptAttachment input[type="file"]').prop('disabled', true);
    }

    customStorage.updatePersonField(docNumber, memberId, 'fn', current_value);
    previous_value = current_value;
});

// Cambios en la sección general
$('#generalSection').on('change', 'input:not([type="file"]):not([type="checkbox"]), select', (event) => {
    const $target = $(event.target);

    // Arreglo de elementos
    if ($target.attr('name').slice(-2) == '[]') {
        const value = $(`input[name="${$target.attr('name')}"]`).get().map(e => e.value);

        customStorage.setField($target.attr('name').slice(0, -2), value);
        return;
    }

    // Elementos regulares
    if ($target.val() == '') {
        customStorage.removeField($target.attr('name'));
    } else {
        customStorage.setField($target.attr('name'), $target.val());
    }

    // Elementos especiales
    switch($target.attr('name')) {
        case 'lyric_dnda_in_file':
        case 'audio_dnda_in_file':
        case 'dnda_in_date':
        case 'lyric_dnda_ed_file':
        case 'audio_dnda_ed_file':
        case 'dnda_ed_date':
            // Agregar solicitud de comprobantes
            if ($target.val() != '' && !dndaEnabled) {
                addFileRequest(
                    'file_dnda_contract',
                    'Constancia DNDA',
                    'image/png, image/jpeg, application/pdf'
                );

                dndaEnabled = true;
            } else if (dndaEnabled) {

                // Recuperamos todos los campos DNDA vacios
                const emptyFieldsDNDA = $('#generalSection input[name*="dnda"]').filter((idx, e) => $.trim($(e).val()).length == 0);

                // Si todos los campos están vacios, quitados el file request
                if (emptyFieldsDNDA.length == 6) {
                    removeFileRequest('file_dnda_contract');
                    customStorage.removeField('file_dnda_contract');
                    dndaEnabled = false;
                }
            }
            break;
    }
});

$('#generalSection').on('change', 'input[type="checkbox"]', (event) => {
    const $target = $(event.target);

    // Elementos regulares
    if (!$target.prop('checked')) {
        customStorage.removeField($target.attr('name'));
    } else {
        customStorage.setField($target.attr('name'), $target.val());
    }
});

// Cambios en los montos de la tabla de distribución
$('#distributionTable').on('change keyup', 'input[class$="Amount"]', (event) => {
    const $target = $(event.target);
    const row = $(event.target).parents('tr').get(0);
    const $row = $distributionTable.row(row);

    customStorage.updatePersonField($row.data()[3], $row.data()[1], $target.attr('name'), parseFloat($target.val()));

    updateTotal();
});

// Selección de provincia en formulario no socios
$('.address_state').on('change', (event) => {
    const $origin = $(event.target);
    const $target = $('.address_city', $origin.parents('form'));
    const stateId = $origin.val();

    const cities = citiesOptions.filter(e => e.state_id == stateId);

    $target.empty();
    cities.forEach(city => {
        $target.append(`<option value="${city.id}">${city.city}</option>`);
    });
});

$('#addPersonModal').on('show.bs.modal', () => {
    $('#memberSearchForm')[0].reset();
    $('#memberSearchResults').html('');
    $('#noMemberForm')[0].reset();
});

// Datos iniciales del modal para agregar personas
$('#addPersonModal').on('shown.bs.modal', () => {
    $('.address_country_id').trigger('change');
    $('.address_state_id').trigger('change');
});

// Datos iniciales del modal para editar personas
$('#editPersonModal').on('show.bs.modal', (event) => {
    const $target = $(event.relatedTarget);
    const row = $target.parents('tr').get(0);
    const $row = $distributionTable.row(row);
    const docNumber = $distributionTable.cell({row: $row.index(), column: 3}).data();
    const memberId = $distributionTable.cell({row: $row.index(), column: 1}).data();
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

/**
 * Funciones auxiliares
 */
// Calculo de totales
const getTotal = (type = 'public') => {
    let total = 0;
    $(`input.${type}Amount`).each((idx, e) => { total += parseFloat(e.value) || 0 });
    return total;
}

// Actualización de totales
window.updateTotal = () => {
    ['public', 'mechanic', 'sync'].forEach((type) => {
        $(`#${type}Total`).html(`${ Math.round(getTotal(type) * 100) / 100 } %`);

        if (getTotal(type) != 100) {
            $(`#${ type }Total`).addClass('text-danger');
        } else {
            $(`#${ type }Total`).removeClass('text-danger');
        }
    });
};

updateTotal();

// Funciones para simplificar la gestión de los documentos a adjuntar
window.addFileRequest = (name, desc, exts, fields = {}) => {
    const html = `<div class="d-flex flex-row fileUploader" style="max-width: 82vw" data-base="/${ userType }/work/files">
        <input type="file" class="d-none" accept="${ exts }" />
        <input type="hidden" name="name" value="${ name }" />
        <input type="hidden" name="id" value="" />
        ${ Object.keys(fields).map((name) => `<input type="hidden" name="${ name }" value="${ fields[name] }" />`).join('') }
        <button type="button" class="btn btn-sm btn-primary text-nowrap">Subir Archivo</button>
        <div class="progress flex-grow-1" style="height: 2rem">
            <div class="progress-bar progress-bar-striped text-dark overflow-hidden" role="progressbar" style="width: 0%"
                aria-valuenow="0" aria-valuemin="0" aria-valuemax="100"></div>
        </div>
        <button type="button" class="btn btn-sm btn-danger d-none">Cancelar</button>
        <button type="button" class="btn btn-sm btn-warning d-none">Eliminar</button>
    </div>`;

    $attachmentsTable.row.add([
        desc,
        html
    ]).draw();
}

const removeFileRequest = (fileName, docNumber = null, memberId = null) => {
    let row = null;
    if (docNumber && memberId) {
        // Recuperamos todos los adjuntos que coincidan con el número de documento
        row = $(`#attachmentsTable input[value="${ docNumber }"]`).parent();
        // Filtramos todos los adjuntos que no coincidan con el número de socio
        row = row.find(`input[value="${memberId}"]`).parent();
    } else if (docNumber && !memberId) {
        // Recuperamos todos los adjuntos que coincidan con el número de documento
        row = $(`#attachmentsTable input[value="${ docNumber }"]`).parent();
    } else if (!docNumber && memberId) {
        // Recuperamos todos los adjuntos que coincidan con el número de socio
        row = $(`#attachmentsTable input[value="${ memberId }"]`).parent();
    } else {
        return;
    }

    // De los adjuntos que recuperamos nos quedamos con el que coincida con el nombre pasado
    row = row.find(`input[value="${fileName}"]`).parents('tr');

    if (row) {
        $attachmentsTable.rows(row).remove().draw();
    }
}

$('#saveRegister').on('click', (event) => {
    const $buttton = $(event.target);
    $buttton.attr('disabled', true);

    const data = customStorage.get();
    if (navigator.userAgent.indexOf('Trident/7.0') !== -1) {
        if (data.dnda_in_date) {
            data.dnda_in_date = data.dnda_in_date.split('/').reverse().join('-')
        }
        if (data.dnda_ed_date) {
            data.dnda_ed_date = data.dnda_ed_date.split('/').reverse().join('-')
        }
        if (data.birth_date) {
            data.birth_date = data.birth_date.split('/').reverse().join('-')
        }
    }

    axios.post(`/${ userType }/work/save`, data)
        .then(({ data }) => {
            if (data.status == 'success') {
                customStorage.setField('registration_id', data.registration_id);
                history.replaceState(null, '', `/${ userType }/work/edit/${data.registration_id}`);

                // Si viene información sobre la distribución
                if (data.people) {
                    // Actualizamos los ids
                    data.people.forEach(current => {
                        let person = customStorage.getPerson(current.doc_number, current.member_id);
                        person.distribution_id = current.id;
                        customStorage.updatePerson(current.doc_number, current.member_id, person);
                    });
                }

                toastr.success('Se guardó correctamente la solicitud.');
            } else {
                toastr.error('Hubo un problema al intentar guardar la solicitud.');
            }
        })
        .finally(() => {
            setTimeout(function() {
                $buttton.attr('disabled', false);
            }, 500);
        });
});

$('#sendRegister').on('click', (event) => {
    const $buttton = $(event.target);
    $buttton.attr('disabled', true);

    const data = customStorage.get();
    if (navigator.userAgent.indexOf('Trident/7.0') !== -1) {
        if (data.dnda_in_date) {
            data.dnda_in_date = data.dnda_in_date.split('/').reverse().join('-')
        }
        if (data.dnda_ed_date) {
            data.dnda_ed_date = data.dnda_ed_date.split('/').reverse().join('-')
        }
        if (data.birth_date) {
            data.birth_date = data.birth_date.split('/').reverse().join('-')
        }
    }

    axios.post(`/${ userType }/work/register`, data)
        .then((response) => {
            if (response && response.data && response.data.status == 'success') {
                if ($('#do_ri').prop('checked')) {
                    window.location = `/user/member/register?work_id=${ response.data.id }`;
                } else {
                    window.location = `/${ userType }/work/list`;
                }
            } else {
                toastr.error('Hubo un problema al intentar enviar la solicitud.');
            }
        })
        .catch(({ response }) => {
            if (response.status == 422) {
                for (const attr in response.data.errors) {
                    toastr.warning(response.data.errors[attr]);
                }
            }
        })
        .finally(() => {
            setTimeout(function() {
                $buttton.attr('disabled', false);
            }, 500);
        });
});

$('#duration').mask('00.99', { placeholder: "mm.ss", reverse: true });

$('#addAltTitle').on('click', (event) => {
    const $buttton = $(event.target);

    $buttton.attr('disabled', true);

    const newInput = `
    <div class="input-group mb-3">
        <input type="text" class="form-control alternative_titles" placeholder="" name="alternative_titles[]">
        <div class="input-group-append">
            <button class="btn btn-outline-secondary delete_alternative_title" type="button">Borrar</button>
        </div>
    </div>`;

    $(newInput).insertBefore($buttton);

    $buttton.attr('disabled', false);
});

$('#generalSection').on('click', '.delete_alternative_title', (event) => {
    const $buttton = $(event.target);

    $buttton.attr('disabled', true);

    // Quitamos el elemento
    $buttton.parents('.input-group').remove();

    // Actualizamo el valor
    const value = $(`input[name="alternative_titles[]"]`).get().map(e => e.value);
    customStorage.setField('alternative_titles', value);

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
