<?php
/**
 * Sends CSV data for a list of tickets
 *
 * @copyright 2011-2021 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param \Solarium\QueryType\Select\Result\Result $this->result
 * @param bool                 $this->paginated   Whether the response is paginated or not
 * @param array                $this->fields      Ticket fields to display
 */
use Blossom\Classes\Block;

header("Content-Disposition: attachment; filename=\"tickets.csv\"");

$this->_include('tickets/partials/searchResultsColumnHeaders.inc');
$this->_include('tickets/partials/searchResultsRows.inc');
