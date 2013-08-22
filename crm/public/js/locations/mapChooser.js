"use strict";
google.maps.event.addDomListener(window, 'load', function() {
	var map = new google.maps.Map(document.getElementById('location_map'), {
		zoom:15,
		center: new google.maps.LatLng(DEFAULT_LATITUDE, DEFAULT_LONGITUDE),
		mapTypeId: google.maps.MapTypeId.ROADMAP
	});
	var crosshairs = new google.maps.Marker({
		map: map,
		icon: {
			url: CRM.BASE_URL + '/js/locations/cross-hairs.png',
			size:   new google.maps.Size(70,70),
			origin: new google.maps.Point(0,0),
			anchor: new google.maps.Point(35,35)
		}
	});
	crosshairs.bindTo('position',map,'center');

	YUI().use('node', function(Y) {
		Y.on('click', function(e) {
			var center = map.getCenter(),
				geocoder = new google.maps.Geocoder();

			geocoder.geocode({latLng:center}, function(results, status) {
				var newLocation = '';
				if (status == google.maps.GeocoderStatus.OK) {
					if (results[0]) {
						for (var i=0; i<results[0].address_components.length; i++) {
							switch (results[0].address_components[i].types[0]) {
								case 'street_number':
									newLocation = results[0].address_components[i].long_name + ' ';
									break;
								case 'route':
									newLocation += results[0].address_components[i].long_name;
									break;
							}
							// This function must be passed in from the LOCATION_CHOOSER
							// in order to be available here.
							// See chooseLocation.js
							setLocation(newLocation, center.lat(), center.lng());
						}
					}
				}
			});
		}, '#useThisLocation');
	});
});
