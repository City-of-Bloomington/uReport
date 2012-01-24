"use strict";
YUI().use('node', function(Y) {
	var services = Y.all('#groupChooser .serviceChooser');

	function closeAll() {
		services.each(function (node) {
			node.one('ul').setStyle('display','none');
		});
	}

	function toggle(e) {
		var ul = this.one('ul'),
			s = ul.getStyle('display'),
			t = s=='none' ? 'block' : 'none';

		closeAll();
		this.one('ul').setStyle('display',t);
	}

	closeAll();
	Y.delegate('click', toggle, '#groupChooser', '.serviceChooser');
});