require('url-polyfill');

const axios = require("axios");

$('.sadaic-internal-form').on('submit', (event) => {
  const $form = $(event.target);

  axios.post('/sadaic/submit', {
    url: $form.data('action'),
    formData: $form.serialize()
  }).then(function ({ data }) {
    if (data.redirect != '') {
      const url = new URL(data.redirect);

      $("#embed-content").load(
        `/sadaic/embed?url=${encodeURIComponent(url.pathname + url.search)}&selector=.general-texto`
      );
    } else {
      $("#embed-content").html(data.response)
    }
  });

  event.preventDefault();
});

$('.sadaic-internal-link').on('click', (event) => {
  let url = $(event.target).data('href');

  // No procesar links que no tengan data-href
  if (!url) {
    return;
  }

  if (url.substring(0, 4) == 'http') {
    const urlParsed = new URL(url);
    url = urlParsed.pathname + urlParsed.search
  }

  $("#embed-content").load(
    `/sadaic/embed?url=${encodeURIComponent(url)}&selector=.general-texto`
  );

  event.preventDefault();
});

// Workaround Rocket Loader de Cloudflare
window.__cfRLUnblockHandlers = true;
