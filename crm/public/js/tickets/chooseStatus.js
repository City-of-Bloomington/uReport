"use strict";
document.getElementById('status').addEventListener('change', function (e) {
    var status = e.target.value;
    jQuery.ajax(CRM.BASE_URL + '/substatus?format=json;status=' + status, {
        dataType: 'json',
        success: function (list, status, xhr) {
            var html = (status === 'open') ? '<option value=""></option>' : '',
                len = list.length,
                i   = 0;
            for (i=0; i<len; i++) {
                html += '<option value="' + list[i].id + '">' + list[i].name + '</option>';
            }
            document.getElementById('substatus_id').innerHTML = html;
        }
    });
}, false);
