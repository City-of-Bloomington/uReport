"use strict";
jQuery(function ($) {
	var loadDepartmentData = function (e) {
            $.ajax(CRM.BASE_URL + '/departments/view?format=json;department_id=' + e.target.value, {
                dataType: 'json',
                success: function (department, status, xhr) {
                    reloadAssignedPersonOptions(department);
                }
            });
        },
        reloadAssignedPersonOptions = function (department) {
            var url = CRM.BASE_URL + '/people?format=json;department_id=' + department.id;
            $.ajax(url, {
                dataType: 'json',
                success: function (people, status, xhr) {
                    var select = document.getElementById('assignedPerson_id'),
                        options  = '',
                        selected = '',
                        len = people.length,
                        i   = 0;

                    for (i=0; i<len; i++) {
                        selected = (people[i].id == department.defaultPerson_id)
                            ? ' selected="selected"'
                            : '';
                        options += '<option value="' + people[i].id + '"' + selected + '>' + people[i].name + '</option>';
                    }
                    select.innerHTML = options;
                }
            });
        };

    $('#department_id').on('change', loadDepartmentData);
});
