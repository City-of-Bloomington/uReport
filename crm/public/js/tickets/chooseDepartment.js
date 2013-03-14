"use strict";
YUI().use('node', 'io', 'json', function (Y) {
	var loadDepartmentData = function (e) {
		Y.io(CRM.BASE_URL + '/departments/view?format=json;department_id=' + e.target.get('value'), {
			on: {
				complete: function (id, o, args) {
					var department = Y.JSON.parse(o.responseText);
					reloadAssignedPersonOptions(department);
				}
			}
		});
	};
	var reloadAssignedPersonOptions = function (department) {
		var url = CRM.BASE_URL + '/people?format=json;department_id=' + department.id;
		Y.io(url, {
			on: {
				complete: function (id, o, args) {
					var html = '';
					var assignedPerson_id = Y.one('#assignedPerson_id');

					if (o.responseText) {
						var people = Y.JSON.parse(o.responseText),
							selected,
							i, len;
						len = people.length;
						for (i=0; i<len; i++) {
							html += '<option value="' + people[i].id + '">' + people[i].name + '</option>';
						}
					}
					assignedPerson_id.setContent(html);
					if (department.defaultPerson_id) {
						assignedPerson_id.set('value', department.defaultPerson_id);
					}
				}
			}
		});
	};

	Y.one('#chooseDepartmentForm button').setStyle('display','none');
	Y.on('submit', function (e) { e.preventDefault(); }, '#chooseDepartmentForm form');
	Y.on('change', loadDepartmentData, '#department_id');
});
