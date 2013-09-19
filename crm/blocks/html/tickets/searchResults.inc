<div class="searchResults">
<?php
/**
 * Displays a list of Tickets from a Solr response
 *
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Apache_Solr_Response $this->solrObject
 */
$c = $this->solrObject->response->numFound;
if ($c) {
	echo "<h2>Search Results</h2><div># Cases in Current Search: $c</div>";

	$header = new Block('tickets/partials/searchResultsColumnHeaders.inc', array('solrObject'=>$this->solrObject));
	$rows   = new Block('tickets/partials/searchResultsRows.inc',          array('solrObject'=>$this->solrObject));

	echo $header->render('html', $this->template);
	echo $rows  ->render('html', $this->template);
}
else {
	echo "<h2>No Cases Found</h2>";
}

// Solr rows start at 0, but pages start at 1
$currentPage = round($this->solrObject->response->start/Search::ITEMS_PER_PAGE) + 1;

$paginator = new Zend_Paginator(new SolrPaginatorAdapter($this->solrObject));
$paginator->setItemCountPerPage(Search::ITEMS_PER_PAGE);
$paginator->setCurrentPageNumber($currentPage);
$block = new Block('pageNavigation.inc',array('paginator'=>$paginator));
echo $block->render('html', $this->template);
?>
</div>
