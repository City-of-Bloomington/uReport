"use strict";
YUI().use('node', 'io', 'json', function (Y) {
	Y.one('#ticket-panel .chooseDepartmentForm .button').setStyle('display','none');

	Y.on('submit', function (e) {
		e.preventDefault();
	}, '#ticket-panel .chooseDepartmentForm form');

	Y.on('change', function (e) {
		var department_id = e.target.get('value');
		var department = {};
		var url = BASE_URL + '/departments/viewDepartment.php?format=json;department_id=' + department_id;
		Y.io(url, {
			on: {
				complete: function (id, o, args) {
					department = Y.JSON.parse(o.responseText);
				}
			}
		});

		url = BASE_URL + '/people?format=json;department=' + department_id;
		Y.io(url, {
			on: {
				complete: function (id, o, args) {
					var html = '';
					if (o.responseText) {
						var people = Y.JSON.parse(o.responseText);
						var selected = '';
						for (var i in people) {
							if (department.defaultPerson && department.defaultPerson._id) {
								selected = department.defaultPerson._id.$id==people[i].id ? 'selected="selected"' : '';
							}
							html += '<option value="' + people[i].id + '" ' + selected + '>' + people[i].name + '</option>';
						}
					}
					Y.one('#assignedPerson').setContent(html);
				}
			}
		});
	}, '#department_id');
});
