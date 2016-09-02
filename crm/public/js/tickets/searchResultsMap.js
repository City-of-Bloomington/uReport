/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
'use strict';

google.maps.event.addDomListener(window, 'load', function () {
	var map_div       = document.getElementById('location_map'),
        indivMarkers  = [], // Array to store the individual markers that will be shown
        largeClusters = [], // Array to store the clusters with no less than 5 tickets.
        smallClusters = [], // Array to store the ids of clusters with less than 5 tickets.
        map           = new google.maps.Map(map_div, {
                                 zoom: ZOOM,
                               center: new google.maps.LatLng(CENTER_LATITUDE, CENTER_LONGITUDE),
                            mapTypeId: google.maps.MapTypeId.ROADMAP
                        }),
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
		getClusterLevel = function (zoom) {
            return (zoom < 10)
                ? 6
                : 10 - (Math.floor(zoom / 2));
        },
		refreshMap = function () {
			var zoomLevel    = map.getZoom(),
				bounds       = map.getBounds(),
				bbox         = generateBBox(bounds),
				coordinates  = generateCoordinates(bounds),
				clusterLevel = getClusterLevel(zoomLevel),
                url          = statsUrl(coordinates, clusterLevel),
                updateBBox   = function (link) {
                    var url  = link.getAttribute('href');

                    url = URL.replaceParam(url, 'bbox', bbox);
                    url = URL.replaceParam(url, 'zoom', zoomLevel);
                    link.setAttribute('href', url);
                };

            updateBBox(document.getElementById('apply_bbox_button'));

			// Correspond with Solr Server
            jQuery.ajax(url, {
                dataType: 'json',
                success: function (json, status, xhr) {
                    var latStats = json.stats.stats_fields.latitude .facets['cluster_id_'+clusterLevel],
                        lngStats = json.stats.stats_fields.longitude.facets['cluster_id_'+clusterLevel],
                        clusterId,
                        centroidLat,
                        centroidLng,
                        clusterLatLng,
                        count             = 0,
                        total             = 0,
                        largeClusterIndex = 0,
                        smallClusterIndex = 0,
                        len               = 0,
                        i                 = 0;

                    // Remove all the markers from the map
                    len = largeClusterIndex.length; for (i=0; i<len; i++) { largeClusters[i].setMap(null); }
                    len =      indivMarkers.length; for (i=0; i<len; i++) {  indivMarkers[i].setMap(null); }
                    largeClusters = [];
                    smallClusters = [];
                    indivMarkers  = [];

                    // Create markers for the clusters returned from SOLR
                    for (clusterId in latStats) {
                        count         = latStats[clusterId].count;
                        centroidLat   = latStats[clusterId].mean;
                        centroidLng   = lngStats[clusterId].mean;
                        clusterLatLng = new google.maps.LatLng(centroidLat, centroidLng);

                        if (bounds.contains(clusterLatLng)) { total += count; }

                        if (count >= 5) {
                            largeClusters[largeClusterIndex] = new MarkerCluster(map, clusterLatLng, count);
                            largeClusterIndex += 1;
                        }
                        else {
                            smallClusters[smallClusterIndex] = clusterId;
                            smallClusterIndex += 1;
                        }
                    }
                    document.getElementById('search_results_total').innerHTML = total;

                    // If we have any small clusters, look up the data for each individual ticket
                    if (smallClusters.length > 0) {
                        jQuery.ajax(ticketsUrl(coordinates, clusterLevel, smallClusters), {
                            dataType: 'json',
                            success: function (json, status, xhr) {
                                var tickets      = json.response.docs,
                                    coordinates  = {},
                                    latlng       = [],
                                    markerLatLng = {},
                                    len          = 0,
                                    i            = 0;

                                len = tickets.length;
                                for (i=0; i<len; i++) {
                                    coordinates     = tickets[i].coordinates;
                                    latlng          = coordinates.split(",");
                                    markerLatLng    = new google.maps.LatLng(latlng[0],latlng[1]);
                                    indivMarkers[i] = new google.maps.Marker({
                                                          position: markerLatLng,
                                                               map: map,
                                                             title: tickets[i].id.toString()
                                                      });
                                }
                                len = indivMarkers.length;
                                for (i = 0; i<len; i++) {
                                    oms.addMarker(indivMarkers[i]);
                                }
                            }
                        });
                    }
                }
            });
		};

    map_div.style.height = Math.floor(document.getElementById('panel-one').getBoundingClientRect().bottom - map_div.getBoundingClientRect().top) + 'px'
	google.maps.event.addListener(map, 'idle', refreshMap);
});
