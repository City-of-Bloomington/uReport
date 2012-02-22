"use strict";
var ISSUE_PERSON_CHANGER = {
	popup: {},
	setPerson: function (person_id) {
		YUI().use('node', 'io', 'json-parse', function (Y) {
			Y.io(CRM.BASE_URL + '/people/view?format=json;person_id=' + person_id, {
				on: {
					complete: function (id, o, args) {
						var person = Y.JSON.parse(o.responseText);
						Y.one('#issue-reportedByPerson').set('value', person.id);
						Y.one('#issue-reportedByPerson-name').setContent(person.fullname);
						ISSUE_PERSON_CHANGER.popup.close();
					}
				}
			});
		});
	}
}

YUI().use('node', function (Y) {
	Y.on('click', function (e) {
		ISSUE_PERSON_CHANGER.popup = window.open(
			CRM.BASE_URL + '/people?popup=1;callback=ISSUE_PERSON_CHANGER.setPerson',
			'popup',
			'menubar=no,location=no,status=no,toolbar=no,width=800,height=600,resizeable=yes,scrollbars=yes'
		);
		e.preventDefault();
	}, '.reportedByPerson .button');
});