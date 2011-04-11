<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (!userIsAllowed('Tickets')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

$template = new Template('search');
$template->blocks['search-form'][] = new Block('tickets/searchForm.inc');
$fields = array(
	'enteredByPerson_id','assignedPerson_id','department_id','zip',
	'issueType_id','category_id','contactMethod_id',
	'status','action_id','actionType_id','actionPerson_id'
);
if (count(array_intersect($fields,array_keys($_GET)))) {
	$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
	$ticketList = new TicketList(null,50,$page);
	$ticketList->search($_GET);
	$template->blocks['search-results'][] = new Block(
		'tickets/searchResults.inc',
		array(
			'ticketList'=>$ticketList,
			'title'=>'Search Results',
			'fields'=>isset($_GET['fields']) ? $_GET['fields'] : null
		)
	);
	$template->blocks['search-results'][] = new Block('pageNavigation.inc',array('list'=>$ticketList));
}

echo $template->render();
