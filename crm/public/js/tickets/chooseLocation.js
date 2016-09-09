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
 * @copyright 2012-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
var LOCATION_CHOOSER = {
	popup: {},
	setLocation: function (location, latitude, longitude) {
        var bd = document.querySelector('#locationChooser h1');
        bd.innerHTML = location;

        // Update the hidden inputs
        // Clear out any previous lat/long
        document.getElementById('location').value = location;
        if (latitude && longitude) {
            document.getElementById('latitude' ).value = latitude;
            document.getElementById('longitude').value = longitude;
        }

        LOCATION_CHOOSER.popup.close();
	}
};
document.getElementById('findAddressButton').addEventListener('click', function (e) {
    e.preventDefault();
    LOCATION_CHOOSER.popup = window.open(
        CRM.BASE_URL + '/locations?popup=1;callback=LOCATION_CHOOSER.setLocation',
        'popup',
        'menubar=no,location=no,status=no,toolbar=no,width=800,height=600,resizeable=yes,scrollbars=yes'
    );
    // Make sure to pass the setLocation function to the popup window, so
    // that window can callback when the user chooses a place on the map.
    // See: /js/locations/mapChooser.js
    LOCATION_CHOOSER.popup.setLocation = LOCATION_CHOOSER.setLocation;
    return false;
}, false);
