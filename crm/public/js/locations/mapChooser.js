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

    document.getElementById('useThisLocation').addEventListener('click', function (e) {
        var center   = map.getCenter(),
            geocoder = new google.maps.Geocoder();

        geocoder.geocode({latLng:center}, function(results, status) {
            var newLocation = '',
                len = 0,
                i   = 0;
            if (status == google.maps.GeocoderStatus.OK) {
                if (results[0]) {
                    len = results[0].address_components.length;
                    for (i=0; i<len; i++) {
                        switch (results[0].address_components[i].types[0]) {
                            case 'street_number':
                                newLocation = results[0].address_components[i].long_name + ' ';
                                break;
                            case 'route':
                                newLocation += results[0].address_components[i].long_name;
                                break;
                        }
                        // Another javascript chooser should pass a "setLocation" function to be
                        // called once we have an address back from Google.
                        // If there is a function passed in, call that function with the chosen
                        // address information.
                        //
                        // If there is not a "setLocation" function passed in, I'm not sure
                        // what we're trying to do.  I guess just send the user to the
                        // locations/view page for that location
                        //
                        // See chooseLocation.js
                        if (typeof(setLocation) != 'undefined') {
                            setLocation(newLocation, center.lat(), center.lng());
                        }
                        else {
                            document.location.href=CRM.BASE_URL + '/locations/view?location=' + newLocation;
                        }
                    }
                }
            }
        });
    }, false);
});
