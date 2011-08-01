"use strict";
YUI().use('dd-delegate','dd-drop-plugin', function(Y) {
	var del = new Y.DD.Delegate({
		cont: '#location-panel .ticketList',
		nodes: 'tbody tr'
	});
	var ticketA = new Y.DD.Drop({
		node:'#ticket_id_a'
	});
	var ticketB = new Y.DD.Drop({
		node:'#ticket_id_b'
	});

	Y.DD.DDM.on('drop:hit',function(e) {
		var matches = e.drag.get('node').one('td:first-child a').get('href').match(/ticket_id=(.+)$/);
		e.drop.get('node').set('value',matches[1]);
	});
	Y.one('#location-panel .ticketList tbody').addClass('yui3-dd-draggable');
});