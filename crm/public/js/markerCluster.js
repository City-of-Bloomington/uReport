/**
 * Javascript class to render custom overlays in Google Map
 *
 * For the map clustering, we want to be able to display custom
 * marker images as well as be able to put text (numbers) directly
 * on the marker image.
 *
 * Google Maps does not support this with thier basic Marker class,
 * so we have to extend Google's OverlayView class to implement
 * our own (marker images + text) class.
 *
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Quan Zhang <quanzhang@acm.org>
 */

MarkerCluster.prototype = new google.maps.OverlayView();

function getIconLevel(count) {
	if (count <= 20) {
		return 1;
	}
	else if (count > 20 && count <= 200) {
		return 2;
	}
	else if (count > 200 && count <= 2000) {
		return 3;
	}
	else if (count > 2000 && count <= 20000) {
		return 4;
	}
	else if (count > 20000) {
		return 5;
	}
}

function getIconWidth(iconLevel) {
	var width;
	switch(iconLevel) {
		case 1: width = 53; break;
		case 2: width = 56; break;
		case 3: width = 66; break;
		case 4: width = 78; break;
		case 5: width = 90; break;
		default:
			width = 0;
	}
	return width;
}

function getIconPath(iconLevel) {
	return 'http://google-maps-utility-library-v3.googlecode.com/svn/trunk/markerclusterer/images/m' + iconLevel + '.png';
}

function MarkerCluster(map, position, count) {
	// Now initialize all properties.
	this.map_ = map;
	this.position_ = position;
	this.icon_ = getIconPath(getIconLevel(count));
	this.iconWidth_ = getIconWidth(getIconLevel(count));
	this.count_ = count;

	// We define a property to hold the image's
	// div. We'll actually create this div
	// upon receipt of the add() method so we'll
	// leave it null for now.
	this.div_ = null;

	// Explicitly call setMap on this overlay
	this.setMap(map);
}

MarkerCluster.prototype.onAdd = function () {
	// Note: an overlay's receipt of add() indicates that
	// the map's panes are now available for attaching
	// the overlay to the map via the DOM.

	// Create the DIV and set some basic attributes.
	var div = document.createElement('div');
	div.style.border = 'none';
	div.style.borderWidth = '0px';
	div.style.position = 'absolute';

	// Set the div's background Image.
	div.style.backgroundImage = "url('" + this.icon_ + "')";

	// Add number on cluster icon.
	div.innerHTML = '<p style="font-weight:bold; color: white; margin: 0px; padding:0px; text-align: center; line-height:' + this.iconWidth_ + 'px">' + this.count_ + '</p>';

	// Set the overlay's div_ property to this DIV
	this.div_ = div;

	// We add an overlay to a map via one of the map's panes.
	// We'll add this overlay to the overlayImage pane.
	var panes = this.getPanes();
	panes.overlayImage.appendChild(this.div_);
}

MarkerCluster.prototype.draw = function () {
	var overlayProjection = this.getProjection();

	// Retrieve the southwest and northeast coordinates of this overlay
	// in latlngs and convert them to pixels coordinates.
	var loc = overlayProjection.fromLatLngToDivPixel(this.position_);

	// Set the marker at the right position.
	this.div_.style.left  = (loc.x - this.iconWidth_ / 2) + 'px';
	this.div_.style.top   = (loc.y - this.iconWidth_ / 2) + 'px';
	this.div_.style.width = this.iconWidth_ + 'px';
}

MarkerCluster.prototype.onRemove = function () {
	this.div_.parentNode.removeChild(this.div_);
}
