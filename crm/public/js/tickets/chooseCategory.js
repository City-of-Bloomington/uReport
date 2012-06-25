"use strict";
var CATEGORY_CHOOSER = {
	popup: {},
	setCategory: function(category_id) {
		CATEGORY_CHOOSER.updateCustomFields(category_id);

		YUI().use('node', 'io', 'json', function (Y) {
			var selectHasCategory = false;
			var chosenCategory = Y.one('#chosenCategory');
			var options = chosenCategory.get('options');
			var l = options.length;
			var url = CRM.BASE_URL + '/categories/view?format=json;category_id=' + category_id;


			for (var i=0; i<l; i++) {
				if (options[l].value == category_id) { selectHasCategory=true; break; }
			}

			if (!selectHasCategory) {
				Y.io(url, {
					on: {
						complete: function (id, o, args) {
							var c = Y.JSON.parse(o.responseText);
							chosenCategory.append('<option value="' + c.id + '">' + c.name +'</option>');
							chosenCategory.set('value', c.id);
						}
					}
				});
			}
		});

		CATEGORY_CHOOSER.popup.close();
	},
	updateCustomFields: function (category_id) {
		var url = CRM.BASE_URL + '/tickets/add?partial=tickets/addTicketForm.inc;category_id=' + category_id;
		YUI().use('node', 'io', function (Y) {
			Y.io(url, {
				on: {
					complete: function (id, o, args) {
						var addTicketForm = Y.Node.create(o.responseText);
						Y.one('#customFields').setContent(addTicketForm.one('#customFields'));
					}
				}
			});
		});
	}
};
YUI().use('node', 'io', 'json', function (Y) {

	Y.one('#chooseCategoryForm button').setStyle('display','none');

	Y.on('submit', function (e) {
		e.preventDefault();
	}, '#chooseCategoryForm form');

	Y.on('change', function (e) {
		CATEGORY_CHOOSER.updateCustomFields(e.target.get('value'));
	}, '#chosenCategory');

	Y.on('click', function (e) {
		CATEGORY_CHOOSER.popup = window.open(
			CRM.BASE_URL + '/categories/choose?popup=1;callback=CATEGORY_CHOOSER.setCategory',
			'popup',
			'menubar=no,location=no,status=no,toolbar=no,width=800,height=600,resizeable=yes,scrollbars=yes'
		);
		e.preventDefault();
	}, '#moreCategoriesLink');
});
