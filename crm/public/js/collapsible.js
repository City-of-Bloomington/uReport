"use strict";
YUI().use('node', function(Y) {
	var fields = Y.all('.collapsible');
	fields.each(function (node) {
		node.addClass('collapsed');
		node.delegate('click', function () {
			if (this.get('parentNode').hasClass('collapsed')) {
				this.get('parentNode').replaceClass('collapsed','expanded');
			}
			else {
				this.get('parentNode').replaceClass('expanded','collapsed');
			}
		}, '.hd');

	});
});