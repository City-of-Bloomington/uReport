"use strict";
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
var ACTION_FORM = {
	popup: {},
	closeAndReload: function () {
		ACTION_FORM.popup.close();
		document.location.reload();
	},
	handleFormSuccess: {
		ChangeCategory:	function () { ACTION_FORM.closeAndReload(); },
		ChangeStatus:	function () { ACTION_FORM.closeAndReload(); },
		Assign:			function () { ACTION_FORM.closeAndReload(); },
		Refer:			function () { ACTION_FORM.closeAndReload(); },
		ChangeLocation: function (location) {
			YUI().use('io', function (Y) {
				Y.io(CRM.BASE_URL + '/tickets/changeLocation.php?ticket_id=' + CRM.ticket_id + ';location=' + location, {
					on: {
						complete: function (id, o, args) {
							ACTION_FORM.closeAndReload();
						}
					}
				});
			});
		}
	}
};

YUI().use('node', function (Y) {
	Y.on('click', function (e) {
		var buttonName = Y.Lang.trim(this.getContent()).replace(' ','');

		var url = this.get('href') + ';popup=1;callback=ACTION_FORM.handleFormSuccess.' + buttonName;
		ACTION_FORM.popup = window.open(
			url,
			'popup',
			'menubar=no,location=no,status=no,toolbar=no,width=800,height=600,resizeable=yes,scrollbars=yes'
		);
		e.preventDefault();
	}, '#ticket-panel ul .button');
});
