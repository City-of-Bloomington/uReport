"use strict";
YUI().use('node', 'io', 'json', function (Y) {

	Y.one('#chooseCategoryForm button').setStyle('display','none');

	Y.on('submit', function (e) {
		e.preventDefault();
	}, '#chooseCategoryForm form');

	Y.on('change', function (e) {
		var category_id = e.target.get('value');
		var url = CRM.BASE_URL + '/tickets/add?partial=tickets/addTicketForm.inc;category_id=' + category_id;
		Y.io(url, {
			on: {
				complete: function (id, o, args) {
					var addTicketForm = Y.Node.create(o.responseText);
					Y.one('#customFields').setContent(addTicketForm.one('#customFields'));
				}
			}
		});
	}, '#chosenCategory');
});
