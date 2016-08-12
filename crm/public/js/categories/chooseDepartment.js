"use strict";
var DEPARTMENT_CHOOSER = {
    loadDepartmentData: function (e) {
        var department_id = e.target.value;
        jQuery.ajax(CRM.BASE_URL + '/departments/view?format=json;department_id=' + department_id, {
            dataType: 'json',
            success: function (department, status, xhr) {
                DEPARTMENT_CHOOSER.reloadPersonOptions(department);
            }
        });
    },
    reloadPersonOptions: function (department) {
        var url = CRM.BASE_URL + '/people?format=json;department_id=' + department.id;
        jQuery.ajax(url, {
            dataType: 'json',
            success: function (people, status, xhr) {
                var select   = document.getElementById('defaultPerson_id'),
                    options  = '<option value=""></option>',
                    selected = '',
                    len = people.length,
                    i   = 0;

                for (i=0; i<len; i++) {
                    options += '<option value="' + people[i].id + '">' + people[i].name + '</option>';
                }
                select.innerHTML = options;
            }
        });
    }
};
document.getElementById('department_id').addEventListener('click', DEPARTMENT_CHOOSER.loadDepartmentData, false);