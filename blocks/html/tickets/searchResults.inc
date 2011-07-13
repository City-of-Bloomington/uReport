<?php
/**
 * Displays a list of Tickets
 *
 * This page only displays Ticket and Issue fields that are asked for
 *
 * The fields desired need to be in the form of TicketList::getDisplayableFields()
 * Example:
 * $this->fieldsToDisplay = array (
 *		'enteredByPerson'=>array(
 *			'displayName'=>'Case Entered By',
 *			'searchOn'=>'enteredByPerson._id',
 *			'sortOn'=>'enteredByPerson.fullname'
 *		),
 *		'categories'=>array(
 *			'displayName'=>'Categories',
 *			'searchOn'=>'issues.category._id',
 *			'sortOn'=>'issues.category.name'
 *		)
 *	)
 * For the full list of possible fields for use here,
 * See: TicketList::getDisplayableFields()
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param array $this->search The search to be passed to the TicketList
 * @param array $this->fieldsToDisplay The ticket fields to display
 * @param array $this->sort (optional) A mongo array that defines the sort
 */
$returnFields = array();
foreach ($this->fieldsToDisplay as $field=>$definition) {
	$returnFields[] = $definition['sortOn'];
}

$ticketList = new TicketList();
$ticketList->findRawData($this->search,$this->sort,$returnFields);

$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
$ticketList = $ticketList->getPaginator(50,$page);

echo "
<div class=\"searchResults\">
	<h1>Search Results</h1>
";
	if (count($ticketList)) {
		echo "<table>";

		$header = new Block(
			'tickets/partials/searchResultsColumnHeaders.inc',
			array('ticketList'=>$ticketList,'fieldsToDisplay'=>$this->fieldsToDisplay)
		);
		echo $header->render('html',$this->template);

		$body = new Block(
			'tickets/partials/searchResultsRows.inc',
			array('ticketList'=>$ticketList,'fieldsToDisplay'=>$this->fieldsToDisplay)
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

$block = new Block('pageNavigation.inc',array('paginator'=>$ticketList));
echo $block->render('html',$this->template);
