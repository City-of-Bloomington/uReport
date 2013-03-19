"use strict";
YUI().use('dd-delegate','dd-drop-plugin', function(Y) {
	var del = new Y.DD.Delegate({
		cont: '#right .personList',
		nodes: 'tbody tr'
	});
	var personA = new Y.DD.Drop({
		node:'#person_id_a'
	});
	var personB = new Y.DD.Drop({
		node:'#person_id_b'
	});

	Y.DD.DDM.on('drop:hit',function(e) {
		var matches = e.drag.get('node').one('td:first-child a').get('href').match(/person_id=(.+)$/);
		e.drop.get('node').set('value',matches[1]);
	});
	Y.one('#right .personList tbody').addClass('yui3-dd-draggable');
});