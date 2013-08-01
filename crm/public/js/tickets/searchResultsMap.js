/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Quan Zhang <quanzhang@acm.org>
 */

google.maps.event.addDomListener(window, 'load', function() {
	var defaultCenter = new google.maps.LatLng(DEFAULT_LATITUDE, DEFAULT_LONGITUDE),
		map = new google.maps.Map(document.getElementById('location_map'), {
			zoom: 15,
			center: defaultCenter,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}),
		// Array to store the markers that will be shown
		allMarkers = [],
		// Tools in solving markers overlapping problem
		oms = new OverlappingMarkerSpiderfier(map, {markersWontMove: true, markersWontHide: true}),
		iw = new google.maps.InfoWindow(),
		refresh = document.getElementById('refresh'),
		refreshMap function () {
			var bounds = map.getBounds(),
				solrQueryString = generateSolrQuery(SOLR_PARAMS, bounds);

				// Correspond with Solr Server
				YUI().use('io', 'json-parse', function (Y) {
					Y.io(solrQueryString, {
						on: {
							complete: function (id, o, args) {
								var response = Y.JSON.parse(o.responseText),
									tickets = response['response']['docs'],
									numFound = response['response']['numFound'];

								document.getElementById("reginal_cases").innerHTML = '# Cases in Current Region: ' + numFound;

								showMarkers(tickets);
							}
						}
					});
				});
		},
		generateSolrQuery function (SOLR_PARAMS, bound) {
			var solrQueryString = '';
			var minLat = bounds.ba.b;
			var minLng = bounds.fa.b;
			var maxLat = bounds['ba']['d'];
			var maxLng = bounds['fa']['d'];
			var queryHeader = 'http://'+SOLR_SERVER_HOSTNAME+SOLR_SERVER_PATH+'/select?';
			var param_sort = SOLR_PARAMS['sort'];
			var param_q = SOLR_PARAMS['q'];
			var param_fq = SOLR_PARAMS['fq'];
			solrQueryString += queryHeader + 'sort=' + param_sort + '&q=' + param_q;
			var i = 0;

			if(param_fq instanceof Array) {
				for(i=0; i<param_fq.length; i++) {
					if(param_fq[i].substr(0,12) != 'coordinates:')
						solrQueryString += '&fq='+param_fq[i];
				}
			}
			else {
				if(param_fq.substr(0,12) != 'coordinates:')
					solrQueryString += '&fq='+param_fq;
			}
			solrQueryString += '&fq=coordinates:['+minLat+','+minLng+' TO '+maxLat+','+maxLng+']';
			solrQueryString += '&wt='+SOLR_PARAMS['wt']+'&json.nl='+SOLR_PARAMS['json.nl'];
			var rows = parseInt(document.getElementById('rows').value, 10);
			solrQueryString += '&start=0&rows=' + rows;

			return solrQueryString;
		},
		showMarkers function (tickets) {
			// clear all the previous markers and shrink the allMarkers array to the size of tickets.
			console.log(tickets);
			for(var i=0;i<allMarkers.length;i++) {
				allMarkers[i].setMap(null);
			}
			allMarkers = [];
			for(var i=0;i<tickets.length;i++) {
				var coordinates = tickets[i]['coordinates'];
				var latlng = coordinates.split(",");
				var markerLatLng = new google.maps.LatLng(latlng[0],latlng[1]);
				allMarkers[i] = new google.maps.Marker({
					position: markerLatLng,
					map: map,
					title: i+""
				});
				allMarkers[i].desc = tickets[i].description;
			}
			oms.addListener('click', function(marker, event) {
				iw.setContent(marker.desc);
				iw.open(map, marker);
			});
			oms.addListener('spiderfy', function(markers) {
				iw.close();
			});
			// Solve markers overlapping problem
			for (var i = 0; i < allMarkers.length; i ++) {
				oms.addMarker(allMarkers[i]);
			}
		}
	google.maps.event.addListener(map, 'idle', refreshMap);
	google.maps.event.addDomListener(refresh, 'click', refreshMap);

});
