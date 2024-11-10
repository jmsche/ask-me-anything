import 'jquery';
import 'bootstrap/dist/js/bootstrap.js';
import 'popper.js/dist/umd/popper.js';

import './modals.js';
import '../styles/app.scss';

$(function () {
    $('[data-toggle="tooltip"]').tooltip({ container: 'body' });
});
