<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param $_GET location
 */
// Make sure we have the location in the system
$ticketList = new TicketList(array('location'=>$_GET['location']));

$template = new Template('locations');
$template->blocks['location-panel'][] = new Block(
	'locations/locationInfo.inc',array('location'=>$_GET['location'])
);
$template->blocks['ticket-panel'][] = new Block(
	'tickets/ticketList.inc',
	array(
		'ticketList'=>$ticketList,
		'title'=>'Tickets Associated with this Location'
	)
);

echo $template->render();
