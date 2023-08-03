$('#beginAction').on('click', () => {
    axios.post(`/works/${ workId }/status`, {
        status: 'beginAction'
    })
    .then(({ data }) => {
        if (data.errors) {
            data.errors.forEach(e => {
                toastr.warning(e);
            });
        }

        if (data.status == 'success') {
            toastr.success('Proceso iniciado');

            setTimeout(() => { location.reload() }, 1000);
        }
    })
    .catch((err) => {
        toastr.error('Se encontró un problema mientras se realizaba la solicitud');
    });
});

$('#modalRejection').on('show.bs.modal', () => {
    $('textarea', '#modalRejection').val('');
})

$('#modalRejection').on('shown.bs.modal', () => {
    $('textarea', '#modalRejection').focus();
})

$('#rejectAction').on('click', (event) => {
    axios.post(`/works/${ workId }/status`, {
        status: 'rejectAction',
        reason: $('textarea', '#modalRejection').val()
    })
    .then(({ data }) => {
        if (data.status == 'failed') {
            toastr.error('No se pudo realizar el rechazo de la solicitud.');

            data.errors.forEach(e => {
                toastr.warning(e);
            });
        } else if (data.status == 'success') {
            toastr.success('Rechazo guardado correctamente');
            setTimeout(() => { location.reload() }, 1000);
        }
    });
});

$('#sendToInternal').on('click', () => {
    axios.post(`/works/${ workId }/status`, {
        status: 'sendToInternal'
    })
    .then(({ data }) => {
        if (data.status == 'failed') {
            toastr.error('No se pudo registrar el pase a sistema interno de la solicitud.');

            data.errors.forEach(e => {
                toastr.warning(e);
            });
        } else if (data.status == 'success') {
            toastr.success('Pase registrado correctamente');
            setTimeout(() => { location.reload() }, 1000);
        }
    });
});

$('#finishRequest').on('click', () => {
    axios.post(`/works/${ workId }/status`, {
        status: 'finishRequest'
    })
    .then(({ data }) => {
        if (data.status == 'failed') {
            toastr.error('No se pudo registrar la finalización del trámite.');

            data.errors.forEach(e => {
                toastr.warning(e);
            });
        } else if (data.status == 'success') {
            toastr.success('Finalización registrada correctamente');
            setTimeout(() => { location.reload() }, 1000);
        }
    });
});

$('.acceptDistribution').on('click', (event) => {
    axios.post(`/works/${ workId }/response`, {
        response: 'accept',
        distribution_id: $(event.target).data('did')
    })
    .then(({ data }) => {
        if (data.status == 'failed') {
            toastr.error('No se puedo cambiar la respuesta a la solicitud.');

            data.errors.forEach(e => {
                toastr.warning(e);
            });
        } else if (data.status == 'success') {
            toastr.success('Respuesta cambiada correctamente');
            setTimeout(() => { location.reload() }, 1000);
        }
    })
    .catch((err) => {
        toastr.error('Se encontró un problema mientras se realizaba la solicitud')
    });
});

$('.rejectDistribution').on('click', (event) => {
    axios.post(`/works/${ workId }/response`, {
        response: 'reject',
        distribution_id: $(event.target).data('did')
    })
    .then(({ data }) => {
        if (data.status == 'failed') {
            toastr.error('No se puedo cambiar la respuesta a la solicitud.');

            data.errors.forEach(e => {
                toastr.warning(e);
            });
        } else if (data.status == 'success') {
            toastr.success('Respuesta cambiada correctamente');
            setTimeout(() => { location.reload() }, 1000);
        }
    })
    .catch((err) => {
        toastr.error('Se encontró un problema mientras se realizaba la solicitud')
    });
});

$('#saveObservations').on('click', (event) => {
    axios.post(`/works/${ workId }/observations`, {
        content: $('#observations').val(),
    })
    .then(({ data }) => {
        if (data.status == 'failed') {
            toastr.error('No se puedo guardar las observaciones');
        } else if (data.status == 'success') {
            toastr.success('Se guardaron las observaciones');
        }
    })
    .catch((err) => {
        toastr.error('Se encontró un problema mientras se guardaban las observaciones')
    });
});
