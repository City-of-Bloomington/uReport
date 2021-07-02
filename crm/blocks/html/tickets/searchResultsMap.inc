<?php
/**
 * Displays a map of the search results
 *
 * @copyright 2013-2021 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param \Solarium\QueryType\Select\Result\Result $this->result
 */
use Blossom\Classes\Block;
use Blossom\Classes\Url;

$this->_include('tickets/partials/searchResultsFormatButtons.inc');
$c = $this->result->getNumFound();
?>
<section class="searchResults">
    <header>
        <h1><?= "{$this->_('bbox')}: (<span id=\"search_results_total\">$c</span>)"; ?></h1>
        <div class="tools">
        <?php
            $url = new Url(URl::current_url(BASE_HOST));
            unset($url->resultFormat);

            $helper = $this->template->getHelper('buttonLink');
            echo $helper->buttonLink($url, $this->_('apply'), 'apply', null, ['id'=>'apply_bbox_button']);
        ?>
        </div>
    </header>
    <?php

        // $this->solrObject->responseHeader->params contains all the information in current URL.
        // Use the json_encode() to convert php variable to javascript variable.
        $centerLatitude  = DEFAULT_LATITUDE;
        $centerLongitude = DEFAULT_LONGITUDE;
        if (isset($_GET['bbox'])) {
            list($minLat, $minLng, $maxLat, $maxLng) = explode(',', $_GET['bbox']);
            $centerLatitude  = ((float)$minLat + (float)$maxLat) / 2;
            $centerLongitude = ((float)$minLng + (float)$maxLng) / 2;
        }
        $zoom = isset($_GET['zoom']) ? (int)$_GET['zoom'] : 12;

        $query  = $this->result->getQuery()->getOptions();
        $fq     = [];
        foreach ($query['filterquery'] as $q) { $fq[] = $q['query']; }
        $params = json_encode(['query'=>$query['query'], 'fq'=>$fq ]);

        echo "
        <div id=\"location_map\"></div>
        <script type=\"text/javascript\">
            var CENTER_LATITUDE  = $centerLatitude;
            var CENTER_LONGITUDE = $centerLongitude;
            var ZOOM = $zoom;
            var SOLR_PARAMS = $params;
        </script>
        ";

        $this->template->addToAsset('scripts', GOOGLE_MAPS);
        $this->template->addToAsset('scripts', BASE_URI.'/js/urlParsing-'.VERSION.'.js');
        $this->template->addToAsset('scripts', BASE_URI.'/js/markerCluster-'.VERSION.'.js');
        $this->template->addToAsset('scripts', BASE_URI.'/js/tickets/searchResultsMap-'.VERSION.'.js');
        $this->template->addToAsset('scripts', BASE_URI.'/js/oms.min-'.VERSION.'.js');
    ?>
</section>
