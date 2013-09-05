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
			var minLat 			= bounds.getSouthWest().lat(),
				minLng 			= bounds.getSouthWest().lng(),
				maxLat 			= bounds.getNorthEast().lat(),
				maxLng 			= bounds.getNorthEast().lng(),
				latDist			= maxLat - minLat,
				lngDist			= maxLng - minLng;
			minLat = minLat - latDist / 4;
			maxLat = maxLat + latDist / 4;
			minLng = minLng - lngDist / 4;
			maxLng = maxLng + lngDist / 4;
			return '[' + minLat + ',' + minLng + ' TO ' + maxLat + ',' + maxLng + ']';
		},
		// bbox format: xx.xxxxxx,xx.xxxxxx,xx.xxxxxx,xx.xxxxxx
		generateBBox = function (bounds) {
			var minLat 			= bounds.getSouthWest().lat(),
				minLng 			= bounds.getSouthWest().lng(),
				maxLat 			= bounds.getNorthEast().lat(),
				maxLng 			= bounds.getNorthEast().lng();
			return minLat + ',' + minLng + ',' + maxLat + ',' + maxLng;
		},
		getSolrStats = function (SOLR_PARAMS, coordinates, clusterLevel) {
			var solrQueryString = '',
				queryHeader 	= 'http://'+SOLR_SERVER_HOSTNAME+SOLR_SERVER_PATH+'/select?',
				param_sort 		= SOLR_PARAMS.sort,
				param_q 		= SOLR_PARAMS.q,
				param_fq 		= SOLR_PARAMS.fq,
				i	 			= 0;
				
			solrQueryString += queryHeader + 'sort=' + param_sort + '&q=' + param_q;
			solrQueryString += '&stats=true&stats.field=latitude&stats.field=longitude&stats.facet=cluster_id_lv' + clusterLevel;
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
			solrQueryString += '&start=0&rows=0';
			
			return solrQueryString;
		},
		generateClusterIdString = function (smallClusters) {
			var i,
				clusterIdString = '';
			for(i = 0; i < smallClusters.length; i += 1) {
				if(i == 0) {
					clusterIdString += smallClusters[i];
				}
				else {
					clusterIdString += ' OR ' + smallClusters[i];
				}
			}
			return clusterIdString;
		},
		getSolrIndiv = function (SOLR_PARAMS, coordinates, clusterLevel, smallClusters) {
			var solrQueryString = '',
				queryHeader 	= 'http://'+SOLR_SERVER_HOSTNAME+SOLR_SERVER_PATH+'/select?',
				param_sort 		= SOLR_PARAMS.sort,
				param_q 		= SOLR_PARAMS.q,
				param_fq 		= SOLR_PARAMS.fq,
				i	 			= 0;
				
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
			solrQueryString += '&fq=cluster_id_lv' + clusterLevel + ':(' + generateClusterIdString(smallClusters) +')';
			solrQueryString += '&fq=coordinates:' + coordinates;
			solrQueryString += '&wt=' + SOLR_PARAMS.wt + '&json.nl=' + SOLR_PARAMS['json.nl'];
			solrQueryString += '&start=0&rows=1000';
			
			return solrQueryString;
		},
		getClusterLevel = function (zoomLevel) {
			if(zoomLevel < 10) {
				return 6;
			}
			else {
				return 10 - (Math.floor(zoomLevel / 2));
			}
		},
		refreshMap = function () {
			var zoomLevel 		= map.getZoom(),
				bounds 			= map.getBounds(),
				bbox			= generateBBox(bounds),
				coordinates 	= generateCoordinates(bounds),
				clusterLevel	= getClusterLevel(zoomLevel),
				solrStatsQuery,
				solrIndivQuery,
				textResultHref,
				mapResultHref,
				i;
			
			// Correspond with Solr Server
			solrStatsQuery = getSolrStats(SOLR_PARAMS, coordinates, clusterLevel);
			YUI().use('io', 'json-parse', function (Y) {
				Y.io(solrStatsQuery, {
					on: {
						complete: function (id, o, args) {
							var response 	= Y.JSON.parse(o.responseText),
								latStats 	= response.stats.stats_fields.latitude.facets['cluster_id_lv'+clusterLevel],
								lngStats 	= response.stats.stats_fields.longitude.facets['cluster_id_lv'+clusterLevel],
								clusterId,
								centroidLat,
								centroidLng,
								clusterLatLng,
								count,
								i,
								largeClusterIndex,
								smallClusterIndex;
								
							for(i = 0; i < largeClusters.length; i += 1) {
								largeClusters[i].setMap(null);
							}
							largeClusters = [];
							smallClusters = [];
							largeClusterIndex = 0;
							smallClusterIndex = 0;
							for(clusterId in latStats) {
								centroidLat = latStats[clusterId].mean;
								centroidLng = lngStats[clusterId].mean;
								clusterLatLng = new google.maps.LatLng(centroidLat, centroidLng);
								count = latStats[clusterId].count;
								if(count >= 5) {
									largeClusters[largeClusterIndex] = new MarkerCluster(map, clusterLatLng, count);
									largeClusterIndex += 1;
								}
								else {
									smallClusters[smallClusterIndex] = clusterId;
									smallClusterIndex += 1;
								}
							}
							for(i = 0; i < indivMarkers.length; i += 1) {
								indivMarkers[i].setMap(null);
							}
							indivMarkers = [];
							if(smallClusters.length > 0) {
								solrIndivQuery = getSolrIndiv(SOLR_PARAMS, coordinates, clusterLevel, smallClusters);
								YUI().use('io', 'json-parse', function (Y) {
									Y.io(solrIndivQuery, {
										on: {
											complete: function (id, o, args) {
												var response 	= Y.JSON.parse(o.responseText),
													tickets 	= response.response.docs,
													coordinates,
													latlng,
													markerLatLng,
													i;
												
												for(i = 0; i < tickets.length; i += 1) {
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
				
				updateBBox(Y.one('#text-result'));
				updateBBox(Y.one('#map-result'));
				Y.all('.searchParameters .btn').each(updateBBox);
				Y.all('#advanced-search a').each(updateBBox);
			});
			
		};

	google.maps.event.addListener(map, 'idle', refreshMap);
});
