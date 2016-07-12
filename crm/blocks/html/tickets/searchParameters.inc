<?php
/**
 * Displays the fields the user searched on
 *
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Apache_Solr_Response $this->solrObject
 */
use Application\Models\Person;
use Application\Models\Search;
use Blossom\Classes\Block;
use Blossom\Classes\Url;

$urlString = "http" . (($_SERVER['SERVER_PORT'] == 443) ? "s://" : "://" . $_SERVER['HTTP_HOST']) .  $_SERVER['REQUEST_URI'];
$currentURL = new Url($urlString);
$helper = $this->template->getHelper('buttonLink');

if (!empty($_GET['query'])
	|| isset($this->solrObject->responseHeader->params->fq)) {
	$q = !empty($_GET['query'])
		? 'Results for: '.self::escape($_GET['query'])
		: $this->_('search_current');
	$fq = isset($this->solrObject->responseHeader->params->fq)
		?       $this->solrObject->responseHeader->params->fq
		: [];
	echo "
	<div class=\"searchParameters\">
		<h2>$q</h2>
	";

	if (Person::isAllowed('bookmarks', 'update')) {
		$bookmarkForm = new Block('bookmarks/updateForm.inc', ['type'=>'search']);
		echo $bookmarkForm->render('html', $this->template);
	}

	if (count($fq)) {
		echo "
		<p>{$this->_('search_parameters_description', 'messages')}</p>
		<span>
		";
		// There may only be one filterQuery
		if (!is_array($fq)) { $fq = [$fq]; }

		foreach ($fq as $filter) {
			if (preg_match('/([^:]+):(.+)/', $filter, $matches)) {
                $key   = $matches[1];
                $value = $matches[2];

                if (array_key_exists($key, Search::$searchableFields)) {
                    $deleteURL = clone $currentURL;

                    if ($key === 'coordinates') {
                        if (isset($deleteURL->zoom)) { unset($deleteURL->zoom); }
                        if (isset($deleteURL->bbox)) { unset($deleteURL->bbox); }

                        $displayName = Search::$searchableFields['bbox'];
                        echo $helper->buttonLink($deleteURL, $displayName, 'delete');
                    }
                    else {
                        if (isset($deleteURL->$key)) { unset($deleteURL->$key); }

                        $displayName  = Search::$searchableFields[$key];
                        $displayValue = Search::getDisplayName($key, $value);
                        if ($displayValue) {
                            echo $helper->buttonLink($deleteURL, "$displayName: $displayValue", 'delete');
                        }
                    }
                }
            }
            else {
                if ($filter == Search::SLA_OVERDUE_FUNCTION) {
                    $deleteURL = clone $currentURL;
                    if (isset($deleteURL->sla)) { unset($deleteURL->sla); }
                    echo $helper->buttonLink($deleteURL, "{$this->_('sla')}: {$this->_('overdue')}", 'delete');
                }
            }
		}
		echo "</span>";
	}
	echo "
	</div>
	";
}
echo '<div id="resultFormatButtons">';
	$url = clone $currentURL;

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
