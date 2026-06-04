<?php
/**
 * Displays a list of Tickets from a Solr response
 *
 * @copyright 2011-2026 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param \Solarium\QueryType\Select\Result\Result $this->result
 * @param bool                 $this->paginated   Whether the response is paginated or not
 * @param array                $this->fields      Ticket fields the user asked to see
 */
use Application\Models\Search;

use Application\Block;

$this->_include('tickets/partials/searchResultsFormatButtons.inc');
?>
<section class="searchResults">
<?php
$totalItemCount = $this->result->getNumFound();
if ($totalItemCount) {
    $this->_include('tickets/partials/searchResultsColumnHeaders.inc');
    $this->_include('tickets/partials/searchResultsRows.inc');
}
else {
    echo "<header><h1>{$this->_('no_tickets_found', 'messages')}</h1></header>";
}

if ($this->paginated && $totalItemCount > Search::ITEMS_PER_PAGE) {
    // Solr rows start at 0, but pages start at 1
    $options     = $this->result->getQuery()->getOptions();
    $pageNumber  = round($options['start'] / Search::ITEMS_PER_PAGE) + 1;

    $block = new Block('pageNavigation.inc', [
        'totalItemCount' => $totalItemCount,
        'itemsPerPage'   => Search::ITEMS_PER_PAGE,
        'pageNumber'     => $pageNumber
    ]);
    echo $block->render('html', $this->template);
}
?>
</section>
