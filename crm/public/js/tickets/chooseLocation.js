"use strict";
/**
 * Opens a new window for the user to lookup a location
 *
 * When the user finally selects a location, the HTML is supposed
 * to call the callback function, LOCATION_CHOOSER.setLocation().
 *
 * Every HTML block involved needs to pass along the callback parameter.
 * Any link or action that can be considered selecting a person should
 * use the callback function, instead of it's normal href.
 *
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
var LOCATION_CHOOSER = {
	popup: {},
	setLocation: function (location, latitude, longitude) {
		YUI().use('node', 'io', function (Y) {
			// Update the hidden inputs
			// Clear out any previous lat/long
			Y.one('#location').set('value', location);
			if (latitude)  { Y.one('#latitude') .set('value', latitude);  }
			if (longitude) { Y.one('#longitude').set('value', longitude); }

			// Draw location information into the LocationChooser body
			var bd = Y.one('#locationChooser .bd');
			bd.setContent('<img src="' + CRM.BASE_URL + '/skins/local/images/busy.gif" />');

			Y.io(CRM.BASE_URL + '/locations/view?partial=locations/locationInfo.inc,tickets/ticketList.inc;disableLinks=1;location=' + location, {
				on: {
					complete: function (id, o, args) {
						bd.setContent(o.responseText);
						LOCATION_CHOOSER.popup.close();
					}
				}
			});
		});
	}
};
YUI().use('node', function (Y) {
	Y.on('click', function (e) {
		LOCATION_CHOOSER.popup = window.open(
			CRM.BASE_URL + '/locations?popup=1;callback=LOCATION_CHOOSER.setLocation',
			'popup',
			'menubar=no,location=no,status=no,toolbar=no,width=800,height=600,resizeable=yes,scrollbars=yes'
		);
		// Make sure to pass the setLocation function to the popup window, so
		// that window can callback when the user chooses a place on the map.
		LOCATION_CHOOSER.popup.setLocation = LOCATION_CHOOSER.setLocation;
		e.preventDefault();
	}, '#findAddressButton');
});
