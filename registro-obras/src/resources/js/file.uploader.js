let currentUploads = [];
const userType = window.location.pathname.split('/')[1];

$(document).on('click', '.fileUploader .btn-primary', (event) => {
    const $fileUploader = $(event.target).parents('.fileUploader');
    $('input[type="file"]', $fileUploader).click();
});

// Subir archivo
$(document).on('change', '.fileUploader input[type="file"]', (event) => {
    if (event.target.value.length == 0) {
        return;
    }

    if (event.target.files[0].size >= maxFileUploadSize) {
        toastr.warning(`El tamaño del archivo excede los ${ maxFileUploadSizeFormatted }`);
        return;
    }

    // Verificamos si entre los mime types soportados se encuentra el que informa el navegador
    // es del archivo a subir. Como el valor de accept puede incluir espacios, lo trimeamos antes
    // de comparar
    if  (!event.target.accept.split(',').map(i => i.trim()).includes(event.target.files[0].type)) {
        toastr.warning(`El tipo de archivo no está soportado para este campo`);
        return;
    }

    const $fileUploader = $(event.target).parents('.fileUploader');

    const files = event.target.files;
    const formData = new FormData();

    // Agregamos el archivo
    formData.append('file', files[0]);

    // Agregamos el id del registro
    if (customStorage.getField('registration_id')) {
        formData.append('registration_id', customStorage.getField('registration_id'));
    }

    // Si el archivo a subir es de la distribución, no del registro, y no tiene todavía
    // distribution_id (no se guardó) mandamos también los datos de la persona
    const registerFiles = ['file_dnda_contract', 'file_dnda_triage', 'lyric_file', 'audio_file', 'script_file'];
    const name = $('input[type="hidden"][name="name"]', $fileUploader).val();
    const doc_number = $('input[name="doc_number"]', $fileUploader).val();
    const member_id = $('input[name="member_id"]', $fileUploader).val();

    if (!registerFiles.includes(name)) {
        formData.append('person', JSON.stringify(customStorage.getPerson(doc_number, member_id)));
    }

    $('input[type="hidden"]', $fileUploader).each((idx, e) => {
        formData.append(e.name, e.value);
    });

    const ajax = new XMLHttpRequest();
    currentUploads.push({
        doc_number,
        member_id,
        name,
        ajax
    });

    ajax.upload.addEventListener('progress', (e) => progressHandler($fileUploader, e), false);

    ajax.addEventListener('load', (e) => completeHandler($fileUploader, e), false);
    ajax.addEventListener('error', (e) => errorHandler($fileUploader, e), false);
    ajax.addEventListener('abort', (e) => abortHandler($fileUploader, e), false);

    ajax.open('POST', '/files/upload');

    ajax.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));

    $('.btn-primary', $fileUploader).prop('disabled', true);
    $('.btn-danger', $fileUploader).removeClass('d-none');
    $('.progress-bar', $fileUploader).addClass('progress-bar-animated');
    $('.progress-bar', $fileUploader).removeClass('bg-success bg-danger');

    $('.progress-bar', $fileUploader).text('');
    $('input[name="id"]', $fileUploader).val('');
    setProgressValue($fileUploader, 0);

    ajax.send(formData);
});

// Cancelar subida
$(document).on('click', '.fileUploader .btn-danger', (event) => {
    const $fileUploader = $(event.target).parents('.fileUploader');
    const doc_number = $('input[name="doc_number"]', $fileUploader).val();
    const member_id = $('input[name="member_id"]', $fileUploader).val();
    const name = $('input[name="name"]', $fileUploader).val();

    $('input[type="file"]', $fileUploader).val('');

    let upload = currentUploads.find(e => e.doc_number == doc_number && e.member_id == member_id && e.name == name);

    if (upload) {
        upload.ajax.abort();
        removeUpload(doc_number, member_id, name);
    }
});

// Eliminar archivo subido
$(document).on('click', '.fileUploader .btn-warning', (event) => {
    const $fileUploader = $(event.target).parents('.fileUploader');

    $('.btn-primary', $fileUploader).prop('disabled', false);
    $('.btn-danger', $fileUploader).addClass('d-none');
    $('.btn-warning', $fileUploader).addClass('d-none');
    $('input[type="file"]', $fileUploader).val('');

    const formData = new FormData();
    const ajax = new XMLHttpRequest();
    ajax.open('POST', '/files/delete/' +  $('input[name="id"]', $fileUploader).val());
    ajax.setRequestHeader('X-CSRF-TOKEN', $('meta[name="csrf-token"]').attr('content'));
    ajax.send(formData);

    $('.progress-bar', $fileUploader).text('');
    $('input[name="id"]', $fileUploader).val('');

    setProgressValue($fileUploader, 0);
});

// Setear manualmente archivo
window.setFileUploaderFile = ($fileUploader, data) => {
    $('.btn-primary', $fileUploader).prop('disabled', false);
    $('.btn-danger', $fileUploader).addClass('d-none');
    $('.progress-bar', $fileUploader).removeClass('progress-bar-animated');
    $('.progress-bar', $fileUploader).addClass('bg-success');
    $('.btn-warning', $fileUploader).removeClass('d-none');

    if (data.distribution_id) {
        const docNumber = $('input[name="doc_number"]', $fileUploader).val();
        const memberId = $('input[name="member_id"]', $fileUploader).val();

        customStorage.updatePersonField(docNumber, memberId, 'distribution_id', data.distribution_id);
    }

    setProgressValue($fileUploader, 100);
    $('.progress-bar', $fileUploader).html(`<a href="/files/download/${ data.id }">${ data.name }</a>`);
    $('input[name="id"]', $fileUploader).val(data.id);
}

const setProgressValue = ($fileUploader, value) => {
    $('.progress-bar', $fileUploader).attr('value', value);
    $('.progress-bar', $fileUploader).css('width', `${value}%`);
}

const progressHandler = ($fileUploader, event) => {
    const progress = Math.ceil(event.loaded / event.total * 100);
    setProgressValue($fileUploader, progress);
}

const completeHandler = ($fileUploader, event) => {
    try {
        const response = JSON.parse(event.target.response);
        const doc_number = $('input[name="doc_number"]', $fileUploader).val();
        const member_id = $('input[name="member_id"]', $fileUploader).val();
        const name = $('input[name="name"]', $fileUploader).val();

        removeUpload(doc_number, member_id, name);

        if (!response.status || response.status != 'success') {
            for (const attr in response.errors) {
                toastr.warning(response.errors[attr]);
            }

            errorHandler($fileUploader);
            return;
        }

        setFileUploaderFile($fileUploader, response);

        if (!customStorage.getField('registration_id')) {
            customStorage.setField('registration_id', response.registration_id);
            history.replaceState(null, '', `/${ userType }/work/edit/${response.registration_id}`);
        }
    } catch (error) {
        errorHandler($fileUploader);
    }
}

const errorHandler = ($fileUploader, event) => {
    if (event) {
        toastr.warning('Ocurrió un error inesperado mientras se subía el archivo. Por favor intente nuevamente');
    }

    const doc_number = $('input[name="doc_number"]', $fileUploader).val();
    const member_id = $('input[name="member_id"]', $fileUploader).val();
    const name = $('input[name="name"]', $fileUploader).val();
    removeUpload(doc_number, member_id, name);

    $('.btn-primary', $fileUploader).prop('disabled', false);
    $('.btn-danger', $fileUploader).addClass('d-none');
    $('.progress-bar', $fileUploader).removeClass('progress-bar-animated');
    $('.progress-bar', $fileUploader).addClass('bg-danger');
    $('input[type="file"]', $fileUploader).val('');
}

const abortHandler = ($fileUploader, event) => {
    const doc_number = $('input[name="doc_number"]', $fileUploader).val();
    const member_id = $('input[name="member_id"]', $fileUploader).val();
    const name = $('input[name="name"]', $fileUploader).val();
    removeUpload(doc_number, member_id, name);

    $('.btn-primary', $fileUploader).prop('disabled', false);
    $('.btn-danger', $fileUploader).addClass('d-none');
    $('.progress-bar', $fileUploader).removeClass('progress-bar-animated');
    $('input[type="file"]', $fileUploader).val('');

    setProgressValue($fileUploader, 0);
}

const getUpload = (doc_number, member_id, name) => {
    let upload = currentUploads;

    if (doc_number) {
        upload = upload.filter(e => e.doc_number == e.doc_number);
    }

    if (member_id) {
        upload = upload.filter(e => e.member_id == e.member_id);
    }

    if (name) {
        upload = upload.filter(e => e.name == e.name);
    }

    if (upload.length != 1) {
        return null;
    }

    return upload[0];
}

const removeUpload = (doc_number, member_id, name) => {
    if (doc_number && member_id) {
        currentUploads = currentUploads.filter(e => e.doc_number != doc_number || e.member_id != member_id || e.name != name);
    } else if (doc_number && !member_id) {
        currentUploads = currentUploads.filter(e => e.doc_number != doc_number || e.name != name);
    } else if (!doc_number && member_id) {
        currentUploads = currentUploads.filter(e => e.member_id != member_id || e.name != name);
    } else {
        currentUploads = currentUploads.filter(e => e.name != name);
    }
}

window.addEventListener('beforeunload', (event) => {
    if (currentUploads.length > 0) {
        // Cancel the event as stated by the standard.
        event.preventDefault();
        // Older browsers supported custom message
        event.returnValue = '';
    }
});
