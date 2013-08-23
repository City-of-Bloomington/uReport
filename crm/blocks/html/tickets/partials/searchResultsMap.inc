<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Quan Zhang <quanzhang@acm.org>
 * @param Apache_Solr_Response $this->solrObject
 */

// $this->solrObject->responseHeader->params contains all the information in current URL.
// Use the json_encode() to convert php variable to javascript variable.

$centerLatitude;
$centerLongitude;
if(isset($_GET['bbox'])) {
	list($minLat, $minLng, $maxLat, $maxLng) = explode(',', $_GET['bbox']);
	$centerLatitude = ((float)$minLat + (float)$maxLat) / 2;
	$centerLongitude = ((float)$minLng + (float)$maxLng) / 2;
}
else {
	$centerLatitude = DEFAULT_LATITUDE;
	$centerLongitude = DEFAULT_LONGITUDE;
}
$zoom = isset($_GET['zoom']) ? $_GET['zoom'] : 15;

echo "
<div id=\"location_map\"></div>
<script type=\"text/javascript\">
	var CENTER_LATITUDE  = ".$centerLatitude.";
	var CENTER_LONGITUDE = ".$centerLongitude.";
	var ZOOM = ".$zoom.";
	var SOLR_PARAMS = ".json_encode($this->solrObject->responseHeader->params).";
	var SOLR_SERVER_HOSTNAME = '".SOLR_SERVER_HOSTNAME."';
	var SOLR_SERVER_PATH = '".SOLR_SERVER_PATH."';
</script>
";

$this->template->addToAsset('scripts', YUI.'/yui/yui-min.js');
$this->template->addToAsset('scripts', GOOGLE_MAPS);
$this->template->addToAsset('scripts', BASE_URL.'/js/urlParsing.js');
$this->template->addToAsset('scripts', BASE_URL.'/js/tickets/searchResultsMap.js');
$this->template->addToAsset('scripts', BASE_URL.'/js/oms.min.js');

