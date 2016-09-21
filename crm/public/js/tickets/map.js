google.maps.event.addDomListener(window, 'load', function() {
    var geodata   = document.querySelector('#locationInfo .geodata'),
        latitude  = geodata .querySelector('dl.latitude  > dd').innerHTML,
        longitude = geodata .querySelector('dl.longitude > dd').innerHTML,
        map       = new google.maps.Map(document.getElementById('location_map'), {
                        zoom:15,
                        center: new google.maps.LatLng(latitude, longitude),
                        mapTypeId: google.maps.MapTypeId.ROADMAP
                    }),
        marker    = new google.maps.Marker({position:new google.maps.LatLng(latitude, longitude), map:map}),
        info      = new google.maps.InfoWindow({content:geodata.innerHTML});

    marker.addListener('click', function () { info.open(map, marker); });
    window.onbeforeprint = function () {
        map.setCenter(marker.getPosition());
    }
});
