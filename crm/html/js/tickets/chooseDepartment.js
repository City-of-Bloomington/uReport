"use strict";
YUI().use('node', 'io', 'json', function (Y) {
	Y.on('submit', function (e) {
		e.preventDefault();
	}, '#ticket-panel .chooseDepartmentForm form');

	Y.on('change', function (e) {
		var id = e.target.get('value');
		var url = BASE_URL + '/people?format=json;department=' + id;
		Y.io(url, {
			on: {
				complete: function (id, o, args) {
					var html = '';
					if (o.responseText) {
						var people = Y.JSON.parse(o.responseText);
						for (var i in people) {
							html += '<option value="' + people[i].id + '">' + people[i].name + '</option>';
						}
					}
					document.getElementById('assignedPerson').innerHTML = html;
				}
			}
		});
	}, '#department_id');
});