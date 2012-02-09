"use strict";
YUI().use('node', function(Y) {
	var fields = Y.all('#advanced-search div');
	fields.each(function (node) {
		node.addClass('collapsed');
	});

	function toggle(e) {
		if (this.get('parentNode').hasClass('collapsed')) {
			this.get('parentNode').replaceClass('collapsed','expanded');
		}
		else {
			this.get('parentNode').replaceClass('expanded','collapsed');
		}
	}

	Y.delegate('click', toggle, '#advanced-search', 'h3');
});