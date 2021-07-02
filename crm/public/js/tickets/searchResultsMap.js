/**
 * @copyright 2013-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
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
        // bbox format: xx.xxxxxx,xx.xxxxxx,xx.xxxxxx,xx.xxxxxxjson
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
			var url  = CRM.BASE_URI + '/solr?';
			    url += SOLR_PARAMS.q ? 'q=' + SOLR_PARAMS.q : 'q=*:*';
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
		/**
		 * Creates the solr query url for individual tickets matching
		 * @param string coordinates
		 * @param int clusterLevel
		 * @param array smallClusters
		 * @return string
		 */
		ticketsUrl = function (coordinates, clusterLevel, smallClusters) {
			return getSolrBaseUrl(coordinates) +
				'&fq=cluster_id_' + clusterLevel + ':(' + smallClusters.join(' OR ') +')' +
				'&start=0&rows=1000';
		},
		getClusterLevel = function (zoom) {
            return (zoom < 10)
                ? 6
                : 10 - (Math.floor(zoom / 2));
        },

        /**
         * Draws markers on the map for individual tickets
         *
         * @param XMLHttpRequest xhr   Request to SOLR for ticket data
         */
        drawTickets = function (xhr) {
            const json    = JSON.parse(xhr.responseText),
                  tickets = json.response.docs;

            let coordinates  = {},
                latlng       = [],
                markerLatLng = {},
                infoWindow   = new google.maps.InfoWindow(),
                len          = 0,
                i            = 0;

            len = tickets.length;
            for (i=0; i<len; i++) {
                coordinates     = tickets[i].coordinates;
                latlng          = coordinates.split(",");
                markerLatLng    = new google.maps.LatLng(latlng[0],latlng[1]);
                indivMarkers[i] = new google.maps.Marker({
                    position:  markerLatLng,
                    map:       map,
                    ticket_id: tickets[i].id,
                    title:     tickets[i].id.toString(),
                    location:  tickets[i].location,
                    category:  tickets[i].category
                });
                indivMarkers[i].addListener('click', function (e) {
                    infoWindow.setContent(infoWindowMarkup(this));
                    infoWindow.open(map, this);
                });
            }
            len = indivMarkers.length;
            for (i = 0; i<len; i++) {
                oms.addMarker(indivMarkers[i]);
            }
        },
        /**
         * @param  google.maps.Marker marker
         * @return string
         */
        infoWindowMarkup = function (marker) {
            return '<a href=' + CRM.BASE_URI + '/tickets/view?ticket_id=' + marker.ticket_id + '>'
                 + '<h2>#' + marker.ticket_id + '</h2>'
                 + marker.location
                 + '</a>';
        },


        /**
         * Draws cluster markers on the map
         *
         * @param JSON   solr          SOLR response with cluster data
         * @param int    clusterLevel  Zoom Level to draw
         * @param Bounds bounds        Google Map Bounds object for the map
         * @param string coordinates   Google Map coordinate string for the bounds
         */
        drawClusters = function (solr, clusterLevel, bounds, coordinates) {
            const latStats = solr.stats.stats_fields.latitude .facets['cluster_id_'+clusterLevel],
                  lngStats = solr.stats.stats_fields.longitude.facets['cluster_id_'+clusterLevel];

            let clusterId,
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
                CRM.ajax(ticketsUrl(coordinates, clusterLevel, smallClusters), drawTickets);
            }
        },

        /**
         * Callback function for map change events
         */
		refreshMap = function () {
			const zoomLevel    = map.getZoom(),
				  bounds       = map.getBounds(),
				  bbox         = generateBBox(bounds),
				  coordinates  = generateCoordinates(bounds),
				  clusterLevel = getClusterLevel(zoomLevel),
                  url          = statsUrl(coordinates, clusterLevel),
                  updateBBox   = function (link) {
                      let url  = link.getAttribute('href');

                      url = URL.replaceParam(url, 'bbox', bbox);
                      url = URL.replaceParam(url, 'zoom', zoomLevel);
                      link.setAttribute('href', url);
                  };

            updateBBox(document.getElementById('apply_bbox_button'));

			// Correspond with Solr Server
            CRM.ajax(url, function (xhr) {
                drawClusters(JSON.parse(xhr.responseText), clusterLevel, bounds, coordinates);
            });
		};

    map_div.style.height = Math.floor(document.getElementById('panel-one').getBoundingClientRect().bottom - map_div.getBoundingClientRect().top) + 'px'
	google.maps.event.addListener(map, 'idle', refreshMap);
});
