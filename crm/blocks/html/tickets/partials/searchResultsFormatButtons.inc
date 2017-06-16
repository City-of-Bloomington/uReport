<?php
/**
 * @copyright 2016-2017 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Apache_Solr_Response $this->solrObject
 */
use Application\Models\Person;
use Blossom\Classes\Url;

$fq = isset($this->solrObject->responseHeader->params->fq)
    ?       $this->solrObject->responseHeader->params->fq
    : [];

echo '<div id="resultFormatButtons">';
    $url = new Url(URl::current_url(BASE_HOST));
    $helper = $this->template->getHelper('buttonLink');

    $resultFormat = (isset($_GET['resultFormat'])) ? trim($_GET['resultFormat']) : 'text';

    unset($url->resultFormat);
    $type = $resultFormat === 'text' ? 'text current' : 'text';
    echo $helper->buttonLink($url, $this->_('search_results_text'), $type);

    $url->resultFormat = 'map';
    $type = $resultFormat === 'map' ? 'map current' : 'map';
    echo $helper->buttonLink($url, $this->_('search_results_map'), $type);

    // Users should filter the results somehow before they are allowed to
    // call for the full, raw results
    if (isset($fq) && count($fq) && Person::isAllowed('tickets', 'print')) {
        $url->resultFormat = 'raw';
        $type = $resultFormat === 'raw' ? 'print current' : 'print';
        echo $helper->buttonLink($url, $this->_('search_results_print'), $type);
    }
echo "</div>";
