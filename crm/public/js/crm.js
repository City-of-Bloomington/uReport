var CRM = {
    ajax: function (url, callback) {
        var request = new XMLHttpRequest();

        request.onreadystatechange = function () {
            if (request.readyState === 4) {
                if (request.status === 200) {
                    callback(request);
                }
            }
        }
        request.open('GET', url);
        request.send();
    },
    reloadPersonOptions: function (department, select) {
        var url = CRM.BASE_URL + '/people?format=json;department_id=' + department.id;
        CRM.ajax(url, function (request) {
            var people   = JSON.parse(request.responseText),
                options  = '',
                selected = '',
                len      = people.length,
                i        = 0;

            for (i=0; i<len; i++) {
                selected = (people[i].id == department.defaultPerson_id)
                ? ' selected="selected"'
                : '';
                options += '<option value="' + people[i].id + '"' + selected + '>' + people[i].name + '</option>';
            }
            select.innerHTML = options;
        });
    }
};
