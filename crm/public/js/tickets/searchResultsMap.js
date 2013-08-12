/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Quan Zhang <quanzhang@acm.org>
 */

'use strict';

google.maps.event.addDomListener(window, 'load', function() {
	var initCenter = new google.maps.LatLng(CENTER_LATITUDE, CENTER_LONGITUDE),
		zoomLevel = ZOOM,
		map = new google.maps.Map(document.getElementById('location_map'), {
			zoom: zoomLevel,
			center: initCenter,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}),
		// Array to store the markers that will be shown
		allMarkers = [],
		// Tool in solving markers overlapping problem
		oms = new OverlappingMarkerSpiderfier(map, {markersWontMove: true, markersWontHide: true}),
		// InfoWindow to show tickets' description
		iw = new google.maps.InfoWindow(),
		// Refresh button to refresh the map
		refresh = document.getElementById('refresh'),
		// Update "Show Top Results:" options with the URL parameter "topResultNum"
		updateTopResultNum = function () {
			var val = TOP_RESULT_NUM,
				sel = document.getElementById('rows'),
				i, j;
    		for(i, j = 0; i = sel.options[j]; j++) {
        		if(i.value == val) {
            		sel.selectedIndex = j;
            		break;
        		}
    		}
		},
		// coordinates format: [xx.xxxxxx,xx.xxxxxx TO xx.xxxxxx,xx.xxxxxx]
		generateCoordinates = function (bounds) {
			var minLat 			= bounds.ba.b,
				minLng 			= bounds.fa.b,
				maxLat 			= bounds.ba.d,
				maxLng 			= bounds.fa.d;
			return '[' + minLat + ',' + minLng + ' TO ' + maxLat + ',' + maxLng + ']';
		},
		// bbox format: xx.xxxxxx,xx.xxxxxx,xx.xxxxxx,xx.xxxxxx
		generateBBox = function (bounds) {
			var minLat 			= bounds.ba.b,
				minLng 			= bounds.fa.b,
				maxLat 			= bounds.ba.d,
				maxLng 			= bounds.fa.d;
			return minLat + ',' + minLng + ',' + maxLat + ',' + maxLng;
		},
		generateSolrQuery = function (SOLR_PARAMS, coordinates) {
			var solrQueryString = '',
				queryHeader 	= 'http://'+SOLR_SERVER_HOSTNAME+SOLR_SERVER_PATH+'/select?',
				param_sort 		= SOLR_PARAMS.sort,
				param_q 		= SOLR_PARAMS.q,
				param_fq 		= SOLR_PARAMS.fq,
				i 				= 0,
				rows 			= parseInt(document.getElementById('rows').value, 10);
				
			solrQueryString += queryHeader + 'sort=' + param_sort + '&q=' + param_q;
			if(param_fq instanceof Array) {
				for(i = 0; i < param_fq.length; i += 1) {
					if(param_fq[i].substr(0,12) !== 'coordinates:') {
						solrQueryString += '&fq=' + param_fq[i];
					}
				}
			}
			else {
				if(param_fq.substr(0,12) !== 'coordinates:') {
					solrQueryString += '&fq=' + param_fq;
				}
			}
			solrQueryString += '&fq=coordinates:' + coordinates;
			solrQueryString += '&wt=' + SOLR_PARAMS.wt + '&json.nl=' + SOLR_PARAMS['json.nl'];
			solrQueryString += '&start=0&rows=' + rows;
			
			return solrQueryString;
		},
		refreshMap = function () {
			var bounds 			= map.getBounds(),
				bbox			= generateBBox(bounds),
				coordinates 	= generateCoordinates(bounds),
				solrQueryString = generateSolrQuery(SOLR_PARAMS, coordinates),
				topResultNum	= parseInt(document.getElementById('rows').value, 10),
				textResultHref,
				mapResultHref;
			
			// Correspond with Solr Server
			YUI().use('io', 'json-parse', function (Y) {
				Y.io(solrQueryString, {
					on: {
						complete: function (id, o, args) {
							var response 	= Y.JSON.parse(o.responseText),
								tickets 	= response.response.docs,
								showMarkers = function (tickets) {
									// clear all the previous markers and shrink the allMarkers array to the size of tickets.
									var i = 0,
										coordinates,
										latlng,
										markerLatLng;
									
									for(i = 0; i < allMarkers.length; i += 1) {
										allMarkers[i].setMap(null);
									}
									allMarkers = [];
									for(i = 0; i < tickets.length; i += 1) {
										coordinates = tickets[i].coordinates;
										latlng = coordinates.split(",");
										markerLatLng = new google.maps.LatLng(latlng[0],latlng[1]);
										allMarkers[i] = new google.maps.Marker({
											position: markerLatLng,
											map: map,
											title: i.toString()
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
									for (i = 0; i < allMarkers.length; i += 1) {
										oms.addMarker(allMarkers[i]);
									}
								};
							showMarkers(tickets);
						}
					}
				});
			});
			zoomLevel = map.getZoom();
			
			YUI().use('node', function(Y) {
				var updateBBox = function (node) {
					var href = node.get('href');
					href = URL.replaceParam(href, 'bbox', bbox);
					href = URL.replaceParam(href, 'zoom', zoomLevel);
					href = URL.replaceParam(href, 'topResultNum', topResultNum);
					node.set('href', href);
				};
				
				updateBBox(Y.one('#text-result'));
				updateBBox(Y.one('#map-result'));
				Y.all('.searchParameters .btn').each(updateBBox);
				Y.all('#advanced-search a').each(updateBBox);
			});
			
		};
		
	updateTopResultNum();
	google.maps.event.addListener(map, 'idle', refreshMap);
	google.maps.event.addDomListener(refresh, 'click', refreshMap);
});
