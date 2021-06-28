<?php
/**
 * Displays a list of Tickets from a Solr response
 *
 * @copyright 2011-2021 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param \Solarium\QueryType\Select\Result\Result $this->result
 * @param bool                 $this->paginated   Whether the response is paginated or not
 * @param array                $this->fields      Ticket fields the user asked to see
 */
use Application\Models\Search;

use Blossom\Classes\Block;
use Blossom\Classes\SolrPaginatorAdapter;

use Application\Paginator;

$this->_include('tickets/partials/searchResultsFormatButtons.inc');
?>
<section class="searchResults">
<?php
$c = $this->result->getNumFound();
if ($c) {
    $this->_include('tickets/partials/searchResultsColumnHeaders.inc');
    $this->_include('tickets/partials/searchResultsRows.inc');
}
else {
	echo "<header><h1>{$this->_('no_tickets_found', 'messages')}</h1></header>";
}

// Raw results should not be paginated
if ($this->paginated) {
	// Solr rows start at 0, but pages start at 1
	$options     = $this->result->getQuery()->getOptions();

	$paginator = new Paginator($this->result->getNumFound(),
                               Search::ITEMS_PER_PAGE,
                               round($options['start'] / Search::ITEMS_PER_PAGE) + 1);
	$block = new Block('pageNavigation.inc', ['paginator'=>$paginator]);
	echo $block->render('html', $this->template);
}
?>
</section>
