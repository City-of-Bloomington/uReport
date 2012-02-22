"use strict";
YUI().use('node','overlay','json',function(Y) {
	var overlay = new Y.Overlay({
		srcNode:'#map_overlay',
		xy: [20,60],
		bodyContent: '<div id="location_map"></div>',
		footerContent:'<button type="button" id="useThisLocation" class="add">Use this location</button><button type="button" class="cancel">Cancel</button>',
	});
	overlay.render();
	overlay.hide();
	Y.on('click',Y.bind(overlay.hide, overlay),'#map_overlay button.cancel');
	Y.on('click',function(e) {
		e.preventDefault();
		overlay.show();
		var geocoder = new google.maps.Geocoder();
		var map = new google.maps.Map(document.getElementById('location_map'), {
			zoom: 14,
			center: new google.maps.LatLng(DEFAULT_LATITUDE, DEFAULT_LONGITUDE),
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
			icon:CRM.BASE_URL + '/js/open311/cross-hairs-small-black-cropped.png'
		});
		crosshairs.bindTo('position',map,'center');

		Y.on('click',function(e) {
			var newLocation = '';
			document.getElementById('lat').value = map.getCenter().lat();
			document.getElementById('long').value = map.getCenter().lng();
			geocoder.geocode({latLng:map.getCenter()}, function(results,status) {
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
							Y.one('#location').setContent(newLocation);
							document.getElementById('address_string').value = newLocation;
						}
					}
				}
			});
			overlay.hide();
		},'#useThisLocation');
	},'#openMapButton');
});
