"use strict";
var CATEGORY_CHOOSER = {
    /**
     * Load the custom fields HTML for the chosen category
     *
     * @param int category_id
     */
	updateCustomFields: function (e) {
		var category_id = e.target.value;

        CRM.ajax(
            CRM.BASE_URL + '/tickets/add?partial=tickets/customFieldsForm.inc;category_id=' + category_id,
            function (request) {
                document.getElementById('customFields').innerHTML = request.responseText;
                CATEGORY_CHOOSER.loadDepartmentData(category_id);
            }
        );
	},
    loadDepartmentData: function (category_id) {
        CRM.ajax(
            CRM.BASE_URL + '/departments/view?format=json;category_id=' + category_id,
            function (request) {
                CRM.reloadPersonOptions(JSON.parse(request.responseText), document.getElementById('assignedPerson_id'));
            }
        );

    }
};
document.getElementById('category_id').addEventListener('change', CATEGORY_CHOOSER.updateCustomFields, false);
