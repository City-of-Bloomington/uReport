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
		// coordinates format: [xx.xxxxxx,xx.xxxxxx TO xx.xxxxxx,xx.xxxxxx]
		generateCoordinates = function (bounds) {
			var minLat 			= bounds.getSouthWest().lat(),
				minLng 			= bounds.getSouthWest().lng(),
				maxLat 			= bounds.getNorthEast().lat(),
				maxLng 			= bounds.getNorthEast().lng();
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
		getSolrStats = function (SOLR_PARAMS, coordinates, geohashLevel) {
			var solrQueryString = '',
				queryHeader 	= 'http://'+SOLR_SERVER_HOSTNAME+SOLR_SERVER_PATH+'/select?',
				param_sort 		= SOLR_PARAMS.sort,
				param_q 		= SOLR_PARAMS.q,
				param_fq 		= SOLR_PARAMS.fq,
				i	 			= 0;
				
			solrQueryString += queryHeader + 'sort=' + param_sort + '&q=' + param_q;
			solrQueryString += '&stats=true&stats.field=latitude&stats.field=longitude&stats.facet=geohash_lv' + geohashLevel;
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
		refreshMap = function () {
			var zoomLevel 		= map.getZoom(),
				bounds 			= map.getBounds(),
				bbox			= generateBBox(bounds),
				coordinates 	= generateCoordinates(bounds),
				geohashLevel	= GEOHASH.getGeohashLevel(zoomLevel),
				solrStatsQuery	= getSolrStats(SOLR_PARAMS, coordinates, geohashLevel),
				textResultHref,
				mapResultHref;
			// Correspond with Solr Server
			YUI().use('io', 'json-parse', function (Y) {
				Y.io(solrStatsQuery, {
					on: {
						complete: function (id, o, args) {
							var response 	= Y.JSON.parse(o.responseText),
								tickets 	= response.response.docs;
							console.log(response);
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
