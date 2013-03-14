"use strict";
YUI().use('node', 'io', 'json', function (Y) {
	var loadSubstatusOptions = function (e) {
		var status = e.target.get('value');
		Y.io(CRM.BASE_URL + '/substatus?format=json;status=' + status, {
			on: {
				complete: function (id, o, args) {
					var html = (status === 'open') ? '<option value=""></option>' : '',
						list = Y.JSON.parse(o.responseText),
						i, len;
					len = list.length;
					for (i=0; i<len; i++) {
						html += '<option value="' + list[i].id + '">' + list[i].name + '</option>';
					}
					Y.one('#substatus_id').setContent(html);
				}
			}
		});
	};
	Y.on('change', loadSubstatusOptions, '#status');
});