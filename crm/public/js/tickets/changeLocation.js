"use strict";
function setLocation(location, latitude, longitude) {
    document.querySelector('#changeLocationForm input[name="location"]' ).value = location;
    document.querySelector('#changeLocationForm input[name="latitude"]' ).value = latitude;
    document.querySelector('#changeLocationForm input[name="longitude"]').value = longitude;
    document.getElementById('changeLocationForm').submit();
}
