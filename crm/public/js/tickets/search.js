"use strict";
YUI().use('node', function(Y) {
	var fields = Y.all('#advanced-search .multiselect');
	fields.each(function (node) {
		node.addClass('collapsed');
	});

	function toggle(e) {
		if (this.hasClass('collapsed')) {
			this.replaceClass('collapsed','expanded');
		}
		else {
			this.replaceClass('expanded','collapsed');
		}
	}

	Y.delegate('click', toggle, '#advanced-search', '.multiselect');
});