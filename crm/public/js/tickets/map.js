"use strict";
google.maps.event.addDomListener(window, 'load', function() {
    const geodata   = document.getElementById('location_map'),
          latitude  = geodata .dataset.latitude,
          longitude = geodata .dataset.longitude,
          map       = new google.maps.Map(document.getElementById('location_map'), {
                          zoom:15,
                          center: new google.maps.LatLng(latitude, longitude),
                          mapTypeId: google.maps.MapTypeId.ROADMAP
                      }),
          marker    = new google.maps.Marker({position:new google.maps.LatLng(latitude, longitude), map:map})

    window.onbeforeprint = function () {
        map.setCenter(marker.getPosition());
    }
});
