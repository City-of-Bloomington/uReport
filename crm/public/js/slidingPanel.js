"use strict";
/**
 * Adds a button that toggles the classes on the panelContainer
 * The Layout css should have styles for all this.
 */
YUI().use('node', function(Y) {
	Y.one('#left').insert('<button type="button" class="slide" id="slideButton">Slide</button>', 0);
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
