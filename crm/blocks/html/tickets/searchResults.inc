<?php
/**
 * Displays a list of Tickets from a Solr response
 *
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param Apache_Solr_Response $this->solrObject
 */
use Application\Models\Search;

use Blossom\Classes\Block;
use Blossom\Classes\SolrPaginatorAdapter;

use Laminas\Paginator\Paginator;

$this->_include('tickets/partials/searchResultsFormatButtons.inc');
?>
<section class="searchResults">
<?php
$c = $this->solrObject->response->numFound;
if ($c) {
	$header = new Block('tickets/partials/searchResultsColumnHeaders.inc', ['solrObject'=>$this->solrObject]);
	$rows   = new Block('tickets/partials/searchResultsRows.inc',          ['solrObject'=>$this->solrObject]);

	echo $header->render('html', $this->template);
	echo $rows  ->render('html', $this->template);
}
else {
	echo "<header><h1>{$this->_('no_tickets_found', 'messages')}</h1></header>";
}

// Raw results should not be paginated
if (empty($_GET['resultFormat']) || $_GET['resultFormat']!='raw') {
	// Solr rows start at 0, but pages start at 1
	$currentPage = round($this->solrObject->response->start/Search::ITEMS_PER_PAGE) + 1;

	$paginator = new Paginator(new SolrPaginatorAdapter($this->solrObject));
	$paginator->setItemCountPerPage(Search::ITEMS_PER_PAGE);
	$paginator->setCurrentPageNumber($currentPage);
	$block = new Block('pageNavigation.inc', ['paginator'=>$paginator]);
	echo $block->render('html', $this->template);
}
?>
</section>
