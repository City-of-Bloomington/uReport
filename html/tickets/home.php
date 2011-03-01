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

$template = new Template();
$template->blocks[] = new Block('tickets/searchForm.inc');
$fields = array(
	'person_id','location',
	'street_address_id','subunit_id',
	'neighborhoodAssociation','township',
	'issueType_id','category_id','contactMethod_id',
	'actionType_id','actionPerson_id'
);
if (count(array_intersect($fields,array_keys($_GET)))) {
	$page = isset($_GET['page']) ? (int)$_GET['page'] : 0;
	$ticketList = new TicketList(null,50,$page);
	$ticketList->search($_GET);
	$template->blocks[] = new Block(
		'tickets/searchResults.inc',
		array('ticketList'=>$ticketList,'title'=>'Search Results')
	);
	$template->blocks[] = new Block('pageNavigation.inc',array('list'=>$ticketList));
}


echo $template->render();