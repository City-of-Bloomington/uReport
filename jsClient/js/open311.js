"use strict";
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
var CLIENT = {
	endpoint: 'https://bloomington.in.gov/ureport/open311/v2',
	DEFAULT_LATITUDE: 39.169927,
	DEFAULT_LONGITUDE: -86.536806,
	service: {},
	overlay: {},
	init: function () {
		if (CLIENT.endpoint) {
			CLIENT.getServiceList(); 
		}
		else {
			alert('No open311 server defined');
		}
	},
	getServiceList: function () {
		YUI().use('io','json-parse', function (Y) {
			Y.on('io:complete', function (id, o, args) {
				var services = Y.JSON.parse(o.responseText);
				var select = document.getElementById('service_list');
				for (var i in services) {
					var option = document.createElement('option');
					option.setAttribute('value',services[i].service_code);
					option.text = services[i].service_name;
					select.appendChild(option);
				}
			}, Y);
			Y.io(CLIENT.endpoint + '/services.json');
		});
	},
	getServiceDefinition: function (select) {
		CLIENT.service.name = select.options[select.selectedIndex].text;
		YUI().use('io', 'json-parse', function(Y) {
			Y.on('io:complete', function (id, o, args) {
				CLIENT.service = Y.JSON.parse(o.responseText);
				var html = '';
				for (var i in CLIENT.service.attributes) {
					var field = CLIENT.service.attributes[i];
					html += '<div><label for="' + field.code + '">' + field.description + ':</label><input name="' + field.code + '" /></div>';
				}
				document.getElementById('customfields').innerHTML = html;
				document.getElementById('reportform').addEventListener('submit',CLIENT.postServiceRequest, false);	
			});
			Y.io(CLIENT.endpoint + '/services/' + select.options[select.selectedIndex].value + '.json');
		});
	},
	/**
	 * Send in the post from the form
	 */
	postServiceRequest: function (e) {
		e.preventDefault();
		alert('Your report has been submitted.  Thank you');
		document.getElementById('addressstring').innerHTML = '';
		document.getElementById('reportform').reset();
		/*
		YUI().use('io-form', 'json-parse', function(Y) {
			Y.io(CLIENT.endpoint + '/requests.json',{
				method: 'POST',
				form: { id: document.getElementById('reportform'), upload: true }
			});
		});
		*/
		return false;
	},
	/**
	 * Open up the google map and have them choose a location
	 */
	chooseLocation: function() {
	}
}

window.addEventListener('load', CLIENT.init, false);
document.getElementById('reportform').addEventListener('submit', CLIENT.postServiceRequest, false);

YUI().use('node','overlay',function(Y) {
	var overlay = new Y.Overlay({
		srcNode:'#locationchooser',
		xy: [20,60],
		bodyContent: '<div id="location_map"></div>',
		footerContent:'<span class="button"><span class="submit"><button type="button" id="useThisLocation">Use this location</button></span></span><span class="button"><span class="cancel"><button type="button">Cancel</button></span></span>',
	});
	overlay.render();
	overlay.hide();
	Y.on('click',Y.bind(overlay.hide, overlay),'#locationchooser .cancel button');

	Y.on('click',function(e) {
		e.preventDefault();
		overlay.show();
		var geocoder = new google.maps.Geocoder();
		var map = new google.maps.Map(document.getElementById('location_map'), {
			zoom: 14,
			center: new google.maps.LatLng(CLIENT.DEFAULT_LATITUDE, CLIENT.DEFAULT_LONGITUDE),
			mapTypeId: google.maps.MapTypeId.ROADMAP
		});
		if (navigator.geolocation) {
			navigator.geolocation.getCurrentPosition(function(position) {
				map.setCenter(new google.maps.LatLng(
					position.coords.latitude,position.coords.longitude
				));
			});
		}
		var crosshairs = new google.maps.Marker({
			map: map,
			icon:'cross-hairs-small-yellow-cropped.png'
		});
		crosshairs.bindTo('position',map,'center');

		Y.on('click',function(e) {
			document.getElementById('lat').value = map.getCenter().lat();
			document.getElementById('long').value = map.getCenter().lng();
			geocoder.geocode({latLng:map.getCenter()}, function(results,status) {
				if (status == google.maps.GeocoderStatus.OK) {
					if (results[0]) {
						var address = '';
						for (var i=0; i<results[0].address_components.length; i++) {
							switch (results[0].address_components[i].types[0]) {
								case 'street_number':
									address = results[0].address_components[i].long_name + ' ';
									break;
								case 'route':
									address += results[0].address_components[i].long_name;
									break;
							}
						}
						document.getElementById('address').value = address;
						document.getElementById('addressstring').innerHTML = address;
					}
				}
			});
			overlay.hide();
		},'#useThisLocation');
	},'#openMapButton');
});


