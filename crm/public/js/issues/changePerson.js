"use strict";
var ISSUE_PERSON_CHANGER = {
	popup: {},
	setPerson: function (person_id) {
        jQuery.ajax(CRM.BASE_URL + '/people/view?format=json;person_id=' + person_id, {
            dataType: 'json',
            success: function (person, status, xhr) {
                document.getElementById('reportedByPerson_id').value       = person.id;
                document.getElementById('reportedByPerson-name').innerHTML = person.fullname;
                ISSUE_PERSON_CHANGER.popup.close();
            }
        });
	}
}
jQuery('.reportedByPerson .button').on('click', function (e) {
    e.preventDefault();
    ISSUE_PERSON_CHANGER.popup = window.open(
        CRM.BASE_URL + '/people?popup=1;callback=ISSUE_PERSON_CHANGER.setPerson',
        'popup',
        'menubar=no,location=no,status=no,toolbar=no,width=800,height=600,resizeable=yes,scrollbars=yes'
    );
    return false;
});
