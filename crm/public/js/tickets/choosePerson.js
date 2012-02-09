"use strict";
YUI().use('node', 'overlay', 'io-form', function (Y) {
	var overlay = new Y.Overlay({
		srcNode: '#find_person_overlay',
		footerContent: '<button type="button" class="cancel">Cancel</button>',
		align: {
			node: '#findPersonButton',
			points: [Y.WidgetPositionAlign.TL, Y.WidgetPositionAlign.BL]
		}
	});
	Y.io(BASE_URL + '/people/partial?partial=people/searchForm.inc', {
		on: {
			complete: function (id, o, args) {
				overlay.set('bodyContent', o.responseText);
			}
		}
	});
	overlay.render();
	overlay.hide();

	Y.on('click', function (e) {
		e.preventDefault();
		overlay.show();
	}, '#findPersonButton');


	Y.on('click', Y.bind(overlay.hide, overlay), '#find_person_overlay button.cancel');

	Y.on('submit', function (e) {
		e.preventDefault();
		Y.io(BASE_URL + '/people/partial?partial=people/searchResults.inc;disableButtons=1', {
			form: { id: e.target },
			on: {
				complete: function (id, o, args) {
					var results = Y.one('#find_person_overlay .findPeopleResults');
					if (results) {
						results.remove(true);
					}
					overlay.setStdModContent(
						Y.WidgetStdMod.BODY,
						o.responseText,
						Y.WidgetStdMod.AFTER
					);
					Y.all('#find_person_overlay .findPeopleResults table a').on('click', function (e) {
						e.preventDefault();
						var matches = /person_id=([0-9a-f]{24})/.exec(e.target.get('href'));
						var person_id = matches[1];
						var personPanel = Y.one('#person-panel');
						overlay.hide();

						var uri = e.target.get('href') + ';partial=person-panel;disableLinks=1';
						personPanel.setContent('<img src="' + BASE_URL + '/skins/local/images/busy.gif" />');
						Y.io(uri, {
							on: {
								complete: function (id, o, args) {
									personPanel.setContent(o.responseText);
									document.getElementById('issue-reportedByPerson').value = person_id;
								}
							}
						});
					});
				}
			}
		});
	},'#find_person_overlay form');
});