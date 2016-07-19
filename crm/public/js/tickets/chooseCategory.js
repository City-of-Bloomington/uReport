"use strict";
var CATEGORY_CHOOSER = {
    /**
     * Load the custom fields HTML for the chosen category
     *
     * @param int category_id
     */
	updateCustomFields: function (category_id) {
		var url = CRM.BASE_URL + '/tickets/add?partial=tickets/addTicketForm.inc;category_id=' + category_id;
        jQuery.ajax(url, {
            dataType: 'html',
            success: function (o, status, xhr) {
                jQuery('#customFields').replaceWith(jQuery(o).find('#customFields'));
            }
        });
	},
    loadDepartmentData: function (category_id) {
        $.ajax(CRM.BASE_URL + '/departments/view?format=json;category_id=' + category_id, {
            dataType: 'json',
            success: function (department, status, xhr) {
                CATEGORY_CHOOSER.reloadAssignedPersonOptions(department);
            }
        });
    },
    reloadAssignedPersonOptions: function (department) {
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
    }
};
jQuery(function ($) {
    $('#category_id').on('change', function (e) {
        CATEGORY_CHOOSER.updateCustomFields(e.target.value);
        CATEGORY_CHOOSER.loadDepartmentData(e.target.value);
    });
});
