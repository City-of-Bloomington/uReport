/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Quan Zhang <quanzhang@acm.org>
 */
'use strict';

google.maps.event.addDomListener(window, 'load', function () {
	var initCenter = new google.maps.LatLng(CENTER_LATITUDE, CENTER_LONGITUDE),
		zoomLevel = ZOOM,
		map = new google.maps.Map(document.getElementById('location_map'), {
			zoom: zoomLevel,
			center: initCenter,
			mapTypeId: google.maps.MapTypeId.ROADMAP
		}),

		// Array to store the individual markers that will be shown
		indivMarkers = [],
		// Array to store the clusters with no less than 5 tickets.
		largeClusters = [],
		// Array to store the ids of clusters with less than 5 tickets.
		smallClusters = [],
		// Tool in solving markers overlapping problem
		oms = new OverlappingMarkerSpiderfier(map, {markersWontMove: true, markersWontHide: true}),
		// coordinates format: [xx.xxxxxx,xx.xxxxxx TO xx.xxxxxx,xx.xxxxxx]
		// The coordinates should be larger than bbox so that it can handle the clusters around corners.
		generateCoordinates = function (bounds) {
			var minLat  = bounds.getSouthWest().lat(),
				minLng  = bounds.getSouthWest().lng(),
				maxLat  = bounds.getNorthEast().lat(),
				maxLng  = bounds.getNorthEast().lng(),
				latDist = maxLat - minLat,
				lngDist = maxLng - minLng;
			minLat = minLat - latDist / 4;
			maxLat = maxLat + latDist / 4;
			minLng = minLng - lngDist / 4;
			maxLng = maxLng + lngDist / 4;
			return '[' + minLat + ',' + minLng + ' TO ' + maxLat + ',' + maxLng + ']';
		},
		// bbox format: xx.xxxxxx,xx.xxxxxx,xx.xxxxxx,xx.xxxxxx
		generateBBox = function (bounds) {
			var minLat = bounds.getSouthWest().lat(),
				minLng = bounds.getSouthWest().lng(),
				maxLat = bounds.getNorthEast().lat(),
				maxLng = bounds.getNorthEast().lng();
			return minLat + ',' + minLng + ',' + maxLat + ',' + maxLng;
		},
		/**
		 * Creates a parameter string for all FQ parameters from the SOLR_PARAMS
		 * @return string
		 */
		getFqParameters = function () {
			var p = '',
				i = 0;
			if (SOLR_PARAMS.fq) {
				if (SOLR_PARAMS.fq instanceof Array) {
					for ( i = 0; i < SOLR_PARAMS.fq.length; i += 1) {
						if (SOLR_PARAMS.fq[i].substr(0,12) !== 'coordinates:') {
							p += '&fq=' + SOLR_PARAMS.fq[i];
						}
					}
				}
				else {
					if (SOLR_PARAMS.fq.substr(0,12) !== 'coordinates:') {
						p += '&fq=' + SOLR_PARAMS.fq;
					}
				}
			}
			return p;
		},
		getSolrBaseUrl = function (coordinates) {
			var url = CRM.BASE_URL + '/solr?';
			url += SOLR_PARAMS.q    ? 'q='     + SOLR_PARAMS.q    : 'q=*.*';
			url += SOLR_PARAMS.sort ? '&sort=' + SOLR_PARAMS.sort : '';
			url += getFqParameters() + '&fq=coordinates:' + coordinates;
			url += '&wt=json&json.nl=map';
			return url;
		},
		/**
		 * Creates the solr query url for the map clusters
		 *
		 * @param string coordinates
		 * @param int clusterLevel
		 * @return string
		 */
		statsUrl = function (coordinates, clusterLevel) {
			return getSolrBaseUrl(coordinates) +
				'&stats=true&stats.field=latitude&stats.field=longitude&stats.facet=cluster_id_' + clusterLevel +
				'&start=0&rows=0';
		},
		generateClusterIdString = function (smallClusters) {
			var i,
				clusterIdString = '';
			for (i = 0; i < smallClusters.length; i += 1) {
				if (i == 0) {
					clusterIdString += smallClusters[i];
				}
				else {
					clusterIdString += ' OR ' + smallClusters[i];
				}
			}
			return clusterIdString;
		},
		/**
		 * Creates the solr query url for individual tickets matching
		 * @param string coordinates
		 * @param int clusterLevel
		 * @param array smallClusters
		 * @return string
		 */
		ticketsUrl = function (coordinates, clusterLevel, smallClusters) {
			return getSolrBaseUrl(coordinates) +
				'&fq=cluster_id_' + clusterLevel + ':(' + generateClusterIdString(smallClusters) +')' +
				'&start=0&rows=1000';
		},
		getClusterLevel = function (zoomLevel) {
			if (zoomLevel < 10) {
				return 6;
			}
			else {
				return 10 - (Math.floor(zoomLevel / 2));
			}
		},
		refreshMap = function () {
			var zoomLevel    = map.getZoom(),
				bounds       = map.getBounds(),
				bbox         = generateBBox(bounds),
				coordinates  = generateCoordinates(bounds),
				clusterLevel = getClusterLevel(zoomLevel);

			// Correspond with Solr Server
			YUI().use('io', 'json-parse', function (Y) {
				var url = statsUrl(coordinates, clusterLevel);
				Y.io(url, {
					on: {
						complete: function (id, o, args) {
							var response = Y.JSON.parse(o.responseText),
								latStats = response.stats.stats_fields.latitude .facets['cluster_id_'+clusterLevel],
								lngStats = response.stats.stats_fields.longitude.facets['cluster_id_'+clusterLevel],
								clusterId,
								centroidLat,
								centroidLng,
								clusterLatLng,
								count,
								i,
								largeClusterIndex,
								smallClusterIndex;

							for (i = 0; i < largeClusters.length; i += 1) {
								largeClusters[i].setMap(null);
							}
							largeClusters = [];
							smallClusters = [];
							largeClusterIndex = 0;
							smallClusterIndex = 0;
							for (clusterId in latStats) {
								centroidLat = latStats[clusterId].mean;
								centroidLng = lngStats[clusterId].mean;
								clusterLatLng = new google.maps.LatLng(centroidLat, centroidLng);
								count = latStats[clusterId].count;
								if (count >= 5) {
									largeClusters[largeClusterIndex] = new MarkerCluster(map, clusterLatLng, count);
									largeClusterIndex += 1;
								}
								else {
									smallClusters[smallClusterIndex] = clusterId;
									smallClusterIndex += 1;
								}
							}
							for (i = 0; i < indivMarkers.length; i += 1) {
								indivMarkers[i].setMap(null);
							}
							indivMarkers = [];
							if (smallClusters.length > 0) {
								YUI().use('io', 'json-parse', function (Y) {
									Y.io(ticketsUrl(coordinates, clusterLevel, smallClusters), {
										on: {
											complete: function (id, o, args) {
												var response 	= Y.JSON.parse(o.responseText),
													tickets 	= response.response.docs,
													coordinates,
													latlng,
													markerLatLng,
													i;

												for (i = 0; i < tickets.length; i += 1) {
													coordinates = tickets[i].coordinates;
													latlng = coordinates.split(",");
													markerLatLng = new google.maps.LatLng(latlng[0],latlng[1]);
													indivMarkers[i] = new google.maps.Marker({
														position: markerLatLng,
														map: map,
														title: tickets[i].id.toString()
													});
												}
												for (i = 0; i < indivMarkers.length; i += 1) {
													oms.addMarker(indivMarkers[i]);
												}
											}
										}
									});
								});
							}
						}
					}
				});
			});

			YUI().use('node', function(Y) {
				var updateBBox = function (node) {
					var href = node.get('href');
					href = URL.replaceParam(href, 'bbox', bbox);
					href = URL.replaceParam(href, 'zoom', zoomLevel);
					node.set('href', href);
				};

				Y.all('#resultFormatButtons a').each(updateBBox);
				Y.all('.searchParameters .btn').each(updateBBox);
				Y.all('#advanced-search a')    .each(updateBBox);
			});
		};

	google.maps.event.addListener(map, 'idle', refreshMap);
});
