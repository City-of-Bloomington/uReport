"use strict";
YUI().use('node', function(Y) {
	var fieldset = Y.one('#advanced-search');
	var select = Y.Node.create('<select><option></option></select>');
	var label;


	fieldset.all('tr').each( function() {
		label = this.one('label');
		select.append(
			'<option value="' + label.getAttribute('for') + '">' + label.getContent() + '</option>'
		);
		if (!this.one('#'+label.getAttribute('for')).get('value')) {
			this.addClass('hidden');
		}
	});

	select.on('change',function (e) {
		fieldset.one('#'+e.currentTarget.get('value')).get('parentNode.parentNode').removeClass('hidden');
		this.set('selectedIndex',-1);
	});

	fieldset.append(select);
});
