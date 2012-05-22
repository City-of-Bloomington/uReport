<?php
/**
 * Displays a list of Tickets from a Solr response
 *
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param SolrObject $this->solrObject
 */
$tickets = Search::hydrateDocs($this->solrObject);
if (count($tickets)) {
	$c = $this->solrObject->response->numFound;
	echo "
	<div class=\"searchResults\">
	<h2>$c Cases</h2>
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

$paginator = new Zend_Paginator(new SolrPaginatorAdapter($this->solrObject));
$paginator->setItemCountPerPage(Search::ITEMS_PER_PAGE);
$paginator->setCurrentPageNumber(round($this->solrObject->response->start/Search::ITEMS_PER_PAGE));
$block = new Block('pageNavigation.inc',array('paginator'=>$paginator));
echo $block->render('html', $this->template);
