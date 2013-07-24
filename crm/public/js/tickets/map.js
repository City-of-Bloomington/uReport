/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Quan Zhang <quanzhang@acm.org>
 */
 
google.maps.event.addDomListener(window, 'load', function() {

	// Create an empty map
	var defaultCenter = new google.maps.LatLng(DEFAULT_LATITUDE, DEFAULT_LONGITUDE);
	var map = new google.maps.Map(document.getElementById('location_map'), {
		zoom: 15,
		center: defaultCenter,
		mapTypeId: google.maps.MapTypeId.ROADMAP
	});
	// Array to store the markers that will be shown
	var allMarkers = [];
	
	google.maps.event.addListener(map, 'idle', function() {
	
		var bounds = map.getBounds();
		var solrQueryString = parseSolrParams(SOLR_PARAMS, bounds);
		
		YUI().use('io', 'json-parse', function (Y) {
			Y.io(solrQueryString, {
				on: {
					complete: function (id, o, args) {
						
						var response = Y.JSON.parse(o.responseText);
						var tickets = response['response']['docs'];
						showMarkers(tickets);
					}
				}
			});
		});
	});
	
	function parseSolrParams(SOLR_PARAMS, bounds) {
		var solrQueryString = '';
		var minLat = bounds['ba']['b'];
		var minLng = bounds['fa']['b'];
		var maxLat = bounds['ba']['d'];
		var maxLng = bounds['fa']['d'];
		var queryHeader = 'http://'+SOLR_SERVER_HOSTNAME+SOLR_SERVER_PATH+'/select?';
		var param_sort = SOLR_PARAMS['sort'];
		var param_q = SOLR_PARAMS['q'];
		var param_fq = SOLR_PARAMS['fq'];
		solrQueryString += queryHeader+'sort='+param_sort+'&q='+param_q;
		// Replace the last fq with new bounding box.
		for(var i=0;i<param_fq.length-1;i++) {
			solrQueryString += '&fq='+param_fq[i];
		}
		solrQueryString += '&fq=coordinates:['+minLat+','+minLng+' TO '+maxLat+','+maxLng+']';
		solrQueryString += '&wt='+SOLR_PARAMS['wt']+'&json.nl='+SOLR_PARAMS['json.nl'];
		solrQueryString += '&start=0&rows=100';
		return solrQueryString;
	}
	
	function showMarkers(tickets) {
		// clear all the previous markers and shrink the allMarkers array to the size of tickets.
		for(var i=0;i<allMarkers.length;i++) {
			if(i < tickets.length)
				allMarkers[i].setMap(null);
			else
				allMarkers.pop();
		}
		for(var i=0;i<tickets.length;i++) {
			var coordinates = tickets[i]['coordinates'];
			var latlng = coordinates.split(",");
			var markerLatLng = new google.maps.LatLng(latlng[0],latlng[1]);
			allMarkers[i] = new google.maps.Marker({
				position: markerLatLng,
				map: map,
				title: i+""
			});
		}
		//var mc = new MarkerClusterer(map, allMarkers);
	}

});
