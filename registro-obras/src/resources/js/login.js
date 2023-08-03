require('url-polyfill');

// Transiciones
$('#loginMembers').on('click', () => {
    window.location.hash = "members";
});

$('#loginPlayers').on('click', () => {
    window.location.hash = "players";
});

$('#loginUsers').on('click', () => {
    window.location.hash = "users";
});

$('#loginRegister').on('click', () => {
    window.location.hash = "register";
});

$('.goBack').on('click', () => {
    window.location.hash = "";
});

// Registro
$('#formRegistration').on('submit', (event) => {
    const $buttton = $(event.originalEvent.submitter);
    $buttton.attr('disabled', true);

    axios.post('/register', $('#formRegistration').serialize())
    .then((data) => {
        window.location = '/email/verify';
    })
    .catch(({ response }) => {
        if (response.status == 422) {
            for (const attr in response.data.errors) {
                toastr.warning(response.data.errors[attr], '', {container: 'users'});
            }
        } else {
            toastr.error('Ocurrió un error inesperado al intentar realizar el registro. Por favor, intente nuevamente más tarde');
        }
    })
    .finally(() => {
        setTimeout(function() {
            $buttton.attr('disabled', false);
        }, 500);
    });

    event.preventDefault();
});

// Login socios
$('#members form').on('submit', (event) => {
    const $buttton = $(event.originalEvent.submitter);
    $buttton.attr('disabled', true);

    axios.post('/login', $('#members form').serialize())
    .then((response) => {
        if (response.data.status == 'success') {
            window.location = response.data.intended;
        } else {
            for (const attr in response.data.errors) {
                toastr.warning(response.data.errors[attr], '', {container: 'members'});
            }
        }
    })
    .catch(({ response }) => {
        if (response.status == 422) {
            for (const attr in response.data.errors) {
                toastr.warning(response.data.errors[attr], '', {container: 'members'});
            }
        } else {
            toastr.warning('Se produjo un error mientras se intentaba realizar la acción.')
        }
    })
    .finally(() => {
        setTimeout(function() {
            $buttton.attr('disabled', false);
        }, 500);
    });

    event.preventDefault();
});

// Login intérpretes
$('#players form').on('submit', (event) => {
    const $buttton = $(event.originalEvent.submitter);
    $buttton.attr('disabled', true);

    axios.post('/login', $('#players form').serialize())
    .then((response) => {
        if (response.data.status == 'success') {
            window.location = response.data.intended;
        } else {
            for (const attr in response.data.errors) {
                toastr.warning(response.data.errors[attr], '', {container: 'players'});
            }
        }
    })
    .catch(({ response }) => {
        if (response.status == 422) {
            for (const attr in response.data.errors) {
                toastr.warning(response.data.errors[attr], '', {container: 'players'});
            }
        } else {
            toastr.warning('Se produjo un error mientras se intentaba realizar la acción.')
        }
    })
    .finally(() => {
        setTimeout(function() {
            $buttton.attr('disabled', false);
        }, 500);
    });

    event.preventDefault();
});

// Login Users
$('#users form').on('submit', (event) => {
    const $buttton = $(event.originalEvent.submitter);
    $buttton.attr('disabled', true);

    axios.post('/login', $('#users form').serialize())
    .then((response) => {
        if (response.data.status == 'success') {
            window.location = response.data.intended;
        } else {
            for (const attr in response.data.errors) {
                toastr.warning(response.data.errors[attr]);
            }
        }
    })
    .catch(({ response }) => {
        if (response.status == 422) {
            for (const attr in response.data.errors) {
                toastr.warning(response.data.errors[attr]);
            }
        } else {
            toastr.warning('Se produjo un error mientras se intentaba realizar la acción.')
        }
    })
    .finally(() => {
        setTimeout(function() {
            $buttton.attr('disabled', false);
        }, 500);
    });

    event.preventDefault();
});

// Carga inicial
if (window.location.hash) {
    if($(window).width() < 767) {
        $(window.location.hash).toggle();
    } else {
        $(window.location.hash).css('display', 'flex');
    }

    $('html, body').scrollTop($(window.location.hash).offset().top);
}

window.addEventListener('hashchange', (event) => {
    const newURL = new URL(window.location.href);
    let target;

    switch(newURL.hash) {
        case '':
        case '#start':
            target = '#start';
            break;
        case '#members':
            target = '#members';
            break;
        case '#players':
            target = '#players';
            break;
        case '#users':
            target = '#users';
            break;
        case '#register':
            target = '#register';
            break;
    }

    // Target
    if (!target) {
        return;
    }

    // Caso especial: Se ocultan los steps, scrollea a 0
    if (target === '#start') {
        $('html, body').animate({
            scrollTop: 0
        }, 'slow', () => { $(".step2").hide() });

    // Caso genérico: Se muestra un step, scrollea al div
    } else {
        if($(window).width() < 767) {
            $(target).toggle();
        } else {
            $(target).css('display', 'flex');
        }

        $('html, body').animate({
            scrollTop: $(target).offset().top
        }, 500);
    }
})
