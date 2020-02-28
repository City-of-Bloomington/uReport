"use strict";
var DEPARTMENT_CHOOSER = {
    loadDepartmentData: function (e) {
        var department_id = e.target.value;
        CRM.ajax(CRM.BASE_URL + '/departments/view?format=json;department_id=' + department_id, function (request) {
            const department = JSON.parse(request.responseText);

            DEPARTMENT_CHOOSER.reloadPersonOptions(department);
        });
    },
    reloadPersonOptions: function (department) {
        CRM.ajax(CRM.BASE_URL + '/people?format=json;department_id=' + department.id, function (request) {
            const people   = JSON.parse(request.responseText),
                  select   = document.getElementById('defaultPerson_id');
            let   options  = '<option value=""></option>',
                  selected = '',
                  len      = people.length,
                  i        = 0;

            for (i=0; i<len; i++) {
                options += '<option value="' + people[i].id + '">' + people[i].name + '</option>';
            }
            select.innerHTML = options;

        });
    }
};
document.getElementById('department_id').addEventListener('click', DEPARTMENT_CHOOSER.loadDepartmentData, false);
