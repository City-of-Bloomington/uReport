"use strict";
/**
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
var ACTION_FORM = {
	popup: {},
	closeAndReload: function () {
		ACTION_FORM.popup.close();
		document.location.reload();
	},
	handleFormSuccess: {
		changeStatus:	function () { ACTION_FORM.closeAndReload(); },
		assign:			function () { ACTION_FORM.closeAndReload(); },
		refer:			function () { ACTION_FORM.closeAndReload(); },
		changeCategory: function (category_id) {
            jQuery.ajax(CRM.BASE_URL + '/tickets/changeCategory?ticket_id=' + CRM.ticket_id + ';category_id=' + category_id, {
                complete: function (id, o, args) {
                    ACTION_FORM.closeAndReload();
                }
            });
		},
		changeLocation: function (location, lat, long) {
            var busy = ACTION_FORM.popup.document.getElementById('left');
            busy.innerHTML = '<img src="' + CRM.BASE_URL + '/skins/local/images/busy.gif" />';

            jQuery.ajax(CRM.BASE_URL +  '/tickets/changeLocation?ticket_id=' + CRM.ticket_id +
                                        ';location='  + location +
                                        ';latitude='  + lat +
                                        ';longitude=' + long, {
                complete: function () {
                    ACTION_FORM.closeAndReload();
                }
            });
		}
	}
};
jQuery('#ticket-panel ul .fa-pencil').on('click', function (e) {
    e.preventDefault();
    var a = e.target,
        callback = a.getAttribute('data-callback'),
        url      = a.getAttribute('href') + ';popup=1;callback=ACTION_FORM.handleFormSuccess.' + callback;

    ACTION_FORM.popup = window.open(
        url,
        'popup',
        'menubar=no,location=no,status=no,toolbar=no,width=800,height=600,resizeable=yes,scrollbars=yes'
    );
    if (callback === 'changeLocation') {
        // We've added the mapChooser to the chooseLocation form.
        // We need to tell that mapChooser.js what to callback when the
        // user hits the "use this location" button.
        ACTION_FORM.popup.setLocation = ACTION_FORM.handleFormSuccess.changeLocation;
    }

    return false;
});
