$('input[id$="_cuit"]').on('change', (event) => {
    event.stopPropagation();

    const $button = $(event.target);
    const $agency = $button.parents('.agency-input');

    try {
        $button.attr('disabled', true);
        $('input[id$="_cuit"]', $agency).attr('disabled', true);
        $('input[id$="_name"]', $agency).val('').change().attr('disabled', true);
        $('input[id$="_address"]', $agency).val('').change().attr('disabled', true);
        $('input[id$="_phone"]', $agency).val('').change().attr('disabled', true);
        $('input[id$="_email"]', $agency).val('').change().attr('disabled', true);

        axios.get('/agency', {
            params: {
                cuit: $('input[id$="_cuit"]', $agency).val()
            }
        })
        .then(({ data }) => {
            if (data == '') {
                $('input[id$="_editable"]', $agency).val(1).change();
                $('input[id$="_cuit"]', $agency).attr('disabled', false);
                $('input[id$="_name"]', $agency).attr('disabled', false);
                $('input[id$="_address"]', $agency).attr('disabled', false);
                $('input[id$="_phone"]', $agency).attr('disabled', false);
                $('input[id$="_email"]', $agency).attr('disabled', false);
                return;
            }

            $('input[id$="_editable"]', $agency).val(0).change();
            $('input[id$="_name"]', $agency).val(data.nombre.trim()).change().attr('disabled', true);
            $('input[id$="_address"]', $agency).val(data.direccion.trim()).change().attr('disabled', true);
            $('input[id$="_phone"]', $agency).val(`${data.tel_pais.trim()} ${data.tel_area.trim()} ${data.tel_numero.trim()}`).change().attr('disabled', true);
            $('input[id$="_email"]', $agency).val(data.email.trim()).change().attr('disabled', true);

            $('input[id$="_cuit"]', $agency).attr('disabled', false);
            $button.attr('disabled', false);
        })
        .catch((err) => {
            console.log(err);

            $button.attr('disabled', false);
        });
    } catch (err) {
        console.log(err);
    }
});

$('.agency-input').on('keypress', 'input[id$="_cuit"]', (event) => {
    if (event.keyCode == 13) {
        $(event.target).trigger('change');
    }
});