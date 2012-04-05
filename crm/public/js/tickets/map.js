google.maps.event.addDomListener(window, 'load', function() {
	var map = new google.maps.Map(document.getElementById('location_map'), {
		zoom:15,
		center: new google.maps.LatLng(points[0].latitude, points[0].longitude),
		mapTypeId: google.maps.MapTypeId.ROADMAP
	});
	for (var i in points) {
		var marker = new google.maps.Marker({
			position: new google.maps.LatLng(points[i].latitude, points[i].longitude),
			map: map
		});
	}
});
