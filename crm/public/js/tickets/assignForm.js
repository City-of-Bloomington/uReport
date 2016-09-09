var ASSIGN_FORM = {
    setDepartment: function (e) {
        var select        = e.target,
            department_id = select.options[select.selectedIndex].value;

        e.stopPropagation();
        CRM.ajax(
            CRM.BASE_URL + '/departments/view?format=json;department_id=' + department_id,
            function (request) {
                var department = JSON.parse(request.responseText);
                CRM.reloadPersonOptions(department, document.getElementById('assignedPerson_id'));
            }
        );
    }
};
document.getElementById('department_id').addEventListener('change', ASSIGN_FORM.setDepartment, false);
