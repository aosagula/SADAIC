require('./bootstrap');
require('datatables.net');
require('datatables.net-bs4');
require('datatables.net-responsive');
require('datatables.net-responsive-bs4');
require("@chenfengyuan/datepicker");
require('jquery-mask-plugin');
require('select2');

$('#menu_hamb').on('click', function () {
  $('#side_bar').toggleClass("translate-0 ");
  $('.menu-hamb').toggleClass('close-hamb');
});
