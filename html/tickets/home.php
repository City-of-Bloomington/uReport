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
	'neighborhoodAssociation','township'
);
if (count(array_intersect($fields,array_keys($_GET)))) {
	$ticketList = new TicketList();
	$ticketList->search($_GET);
	$template->blocks[] = new Block('tickets/ticketList.inc',array('ticketList'=>$ticketList));
}


echo $template->render();