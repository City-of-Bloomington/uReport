"use strict";
/**
 * Adds a button that toggles the classes on the panelContainer
 * The Layout css should have styles for all this.
 */
YUI().use('node', function(Y) {
	Y.one('#left').insert('<button class="icon-angle-left" id="slideButton"><span class="hidden-label">Slide</span></button>', 0);
	function togglePanel(e) {
		var panelContainer = this.get('parentNode').get('parentNode');
		if (panelContainer.hasClass('hideLeft')) {
			panelContainer.removeClass('hideLeft');
		}
		else {
			panelContainer.addClass('hideLeft');
		}
	}
	Y.delegate('click', togglePanel, '#left', '#slideButton');
});
