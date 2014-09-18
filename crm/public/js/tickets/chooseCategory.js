"use strict";
var CATEGORY_CHOOSER = {
	popup: {},
    /**
     * Selects the chosen category in the categories drop down
     *
     * If the chosen category is not already in the drop down,
     * the category is added.
     * @param int category_id
     */
	setCategory: function (category_id) {
        var selectHasCategory = false,
            select = document.getElementById('category_id'),
            url = CRM.BASE_URL + '/categories/view?format=json;category_id=' + category_id,
            len = select.options.length,
            i   = 0;

        CATEGORY_CHOOSER.updateCustomFields(category_id);

        for (i=0; i<len; i++) {
            if (select.options[i].value == category_id) {
                selectHasCategory=true;
                break;
            }
        }

        if (!selectHasCategory) {
            jQuery.ajax(url, {
                dataType: 'json',
                success: function (json) {
                    var o = document.createElement('option');
                    o.setAttribute('value', json.id);
                    o.innerHTML = json.name;
                    select.appendChild(o);
                    select.selectedIndex = len; // New element index is the same as the previous length
                }
            });
        }
		CATEGORY_CHOOSER.popup.close();
	},
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
	}
};
jQuery(function ($) {
    $('#chooseCategoryForm button').css('display', 'none');
    $('#chooseCategoryForm form').on('submit', function (e) {
        e.preventDefault();
        return false;
    });

    $('#category_id').on('change', function (e) {
        CATEGORY_CHOOSER.updateCustomFields(e.target.value);
    });

    $('#moreCategoriesLink').on('click', function (e) {
        e.preventDefault();
        CATEGORY_CHOOSER.popup = window.open(
            CRM.BASE_URL + '/categories/choose?popup=1;callback=CATEGORY_CHOOSER.setCategory',
            'popup',
            'menubar=no,location=no,status=no,toolbar=no,width=800,height=600,resizeable=yes,scrollbars=yes'
        );
        return false;
    });
});
