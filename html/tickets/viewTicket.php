<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET ticket_id
 */
$ticket = new Ticket($_GET['ticket_id']);

$template = new Template('tickets');
$template->blocks['ticket-panel'][] = new Block('tickets/ticketInfo.inc',array('ticket'=>$ticket));

if (userIsAllowed('Tickets')
	&& $_SESSION['USER']->getDepartment()
	&& $_SESSION['USER']->getDepartment()->getActions()) {
	$template->blocks['ticket-panel'][] = new Block('tickets/actionForm.inc',array('ticket'=>$ticket));
}

$template->blocks['history-panel'][] = new Block('tickets/history.inc',
												array('ticketHistory'=>$ticket->getHistory()));

$template->blocks['issue-panel'][] = new Block('issues/issueList.inc',
												array('issueList'=>$ticket->getIssues()));

if ($ticket->getLocation()) {
	$template->blocks['location-panel'][] = new Block(
		'locations/locationInfo.inc',
		array('location'=>$ticket->getLocation())
	);
	$template->blocks['location-panel'][] = new Block(
		'tickets/searchResults.inc',
		array(
			'ticketList'=>new TicketList(array('location'=>$ticket->getLocation())),
			'title'=>'Other tickets for this location'
		)
	);
}
echo $template->render();