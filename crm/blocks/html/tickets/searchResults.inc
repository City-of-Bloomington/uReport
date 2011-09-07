<?php
/**
 * Displays a list of Tickets
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$ticketList = new TicketList();
$ticketList->findRawData($_GET);

echo "
<div class=\"searchResults\">
	<h1>Search Results</h1>
";
	if (count($ticketList)) {
		echo "<table>";

		$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
		$ticketList = $ticketList->getPaginator(50,$page);

		$header = new Block(
			'tickets/partials/searchResultsColumnHeaders.inc',
			array('ticketList'=>$ticketList)
		);
		echo $header->render('html',$this->template);

		$body = new Block(
			'tickets/partials/searchResultsRows.inc',
			array('ticketList'=>$ticketList)
		);
		echo $body->render('html',$this->template);

		echo "</table>";
	}
	else {
		echo "<p>No cases found<p>";
	}
echo "
</div>
";

if ($ticketList instanceof Zend_Paginator) {
	$block = new Block('pageNavigation.inc',array('paginator'=>$ticketList));
	echo $block->render('html',$this->template);
}
