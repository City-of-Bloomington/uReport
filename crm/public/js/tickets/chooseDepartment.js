"use strict";
YUI().use('node', 'io', 'json', function (Y) {
	Y.one('#chooseDepartmentForm button').setStyle('display','none');

	Y.on('submit', function (e) {
		e.preventDefault();
	}, '#chooseDepartmentForm form');

	Y.on('change', function (e) {
		var department_id = e.target.get('value');
		var department = {};
		var url = CRM.BASE_URL + '/departments/view?format=json;department_id=' + department_id;
		Y.io(url, {
			on: {
				complete: function (id, o, args) {
					department = Y.JSON.parse(o.responseText);
				}
			}
		});

		url = CRM.BASE_URL + '/people?format=json;department_id=' + department_id;
		Y.io(url, {
			on: {
				complete: function (id, o, args) {
					var html = '';
					if (o.responseText) {
						var people = Y.JSON.parse(o.responseText);
						var selected = '';
						for (var i in people) {
							if (department.defaultPerson_id) {
								selected = department.defaultPerson_id == people[i].id ? 'selected="selected"' : '';
							}
							html += '<option value="' + people[i].id + '" ' + selected + '>' + people[i].name + '</option>';
						}
					}
					Y.one('#assignedPerson_id').setContent(html);
				}
			}
		});
	}, '#department_id');
});
