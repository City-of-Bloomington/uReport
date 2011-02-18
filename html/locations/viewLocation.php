<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param $_GET location
 */
// Make sure we have the location in the system
$ticketList = new TicketList(array('location'=>$_GET['location']));
if (!count($ticketList)) {
	$url = new URL(BASE_URL.'locations');
	$url->location_query = $_GET['location'];
	header("Location: $url");
	exit();
}

$template = new Template('locations');
$template->blocks['location-panel'][] = new Block(
	'locations/locationInfo.inc',array('location'=>$_GET['location'])
);
$fields = array(
	'person_id','location',
	'street_address_id','subunit_id',
	'neighborhoodAssociation','township',
	'issueType_id','category_id','contactMethod_id'
);
if (count(array_intersect($fields,array_keys($_GET)))) {
	$ticketList = new TicketList();
	$ticketList->search($_GET);
	$template->blocks['ticket-panel'][] = new Block(
		'tickets/searchResults.inc',
		array('ticketList'=>$ticketList, 'title'=>'Tickets Associated with this Location')
	);
}

echo $template->render();
