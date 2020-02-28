"use strict";
var ISSUE_PERSON_CHANGER = {
	popup: {},
	setPerson: function (person_id) {
        CRM.ajax(CRM.BASE_URL + '/people/view?format=json;person_id=' + person_id, function (request) {
            const person = JSON.parse(request.responseText);

            document.getElementById('reportedByPerson_id').value       = person.id;
            document.getElementById('reportedByPerson-name').innerHTML = person.fullname;
            ISSUE_PERSON_CHANGER.popup.close();
        });
	}
}
