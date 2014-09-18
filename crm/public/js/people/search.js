"use strict";
jQuery('#firstname').autocomplete({
    source: CRM.BASE_URL + '/people/distinct?format=json;field=firstname'
});
jQuery('#lastname').autocomplete({
    source: CRM.BASE_URL + '/people/distinct?format=json;field=lastname'
});
jQuery('#email').autocomplete({
    source: CRM.BASE_URL + '/people/distinct?format=json;field=email'
});
jQuery('#organization').autocomplete({
    source: CRM.BASE_URL + '/people/distinct?format=json;field=organization'
});
