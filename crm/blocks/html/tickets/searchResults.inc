<?php
/**
 * Displays a list of Tickets from a Solr response
 *
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Apache_Solr_Response $this->solrObject
 */
$tickets = Search::hydrateDocs($this->solrObject);
if (count($tickets)) {
	$c = $this->solrObject->response->numFound;
	echo "
	<div class=\"searchResults\">
	<h1>Search Results</h1>
	<div># Cases in Current Search: $c</div>
	<table>
	";
	$header = new Block('tickets/partials/searchResultsColumnHeaders.inc', array('tickets'=>$tickets));
	echo $header->render('html', $this->template);
	echo "</table>";

	$row = new Block('tickets/partials/searchResultsRows.inc', array('tickets'=>$tickets));
	echo $row->render('html', $this->template);
	echo "
	</div>
	";
}
else {
	echo "<div class=\"searchResults\"><h2>No Cases Found</h2></div>";
}

// Solr rows start at 0, but pages start at 1
$currentPage = round($this->solrObject->response->start/Search::ITEMS_PER_PAGE) + 1;

$paginator = new Zend_Paginator(new SolrPaginatorAdapter($this->solrObject));
$paginator->setItemCountPerPage(Search::ITEMS_PER_PAGE);
$paginator->setCurrentPageNumber($currentPage);
$block = new Block('pageNavigation.inc',array('paginator'=>$paginator));
echo $block->render('html', $this->template);
