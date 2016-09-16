"use strict";

document.getElementById('authenticationMethod').addEventListener('change', function (e) {
    var select    = e.target,
        firstname = document.getElementById('firstname'),
        lastname  = document.getElementById('lastname' ),
        auth      = select.options[select.selectedIndex].value,
        required  = auth === 'local';

    if (required) {
        firstname.setAttribute('required', 'true');
        lastname .setAttribute('required', 'true');
    }
    else {
        firstname.removeAttribute('required');
        lastname .removeAttribute('required');
    }
});
