<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET ticket_id
 */
$ticket = new Ticket($_GET['ticket_id']);

$template = new Template('tickets');
$template->blocks['ticket-panel'][] = new Block('tickets/ticketInfo.inc',array('ticket'=>$ticket));

if (userIsAllowed('Tickets') && $ticket->getStatus()!='closed') {
	$template->blocks['ticket-panel'][] = new Block(
		'tickets/actionForm.inc',
		array('ticket'=>$ticket)
	);
}

$template->blocks['history-panel'][] = new Block(
	'tickets/history.inc',
	array('history'=>$ticket->getHistory())
);

$template->blocks['issue-panel'][] = new Block(
	'tickets/issueList.inc',
	array(
		'issueList'=>$ticket->getIssues(),
		'ticket'=>$ticket,
		'disableButtons'=>$ticket->getStatus()=='closed'
	)
);

if ($ticket->getLocation()) {
	$template->blocks['location-panel'][] = new Block(
		'locations/locationInfo.inc',
		array('location'=>$ticket->getLocation(),'disableButtons'=>true)
	);
	$template->blocks['location-panel'][] = new Block(
		'tickets/ticketLocationInfo.inc',
		array('ticket'=>$ticket)
	);

	$ticketList = new TicketList(array('location'=>$ticket->getLocation()));
	if (count($ticketList) > 1) {
		$template->blocks['location-panel'][] = new Block(
			'tickets/ticketList.inc',
			array(
				'ticketList'=>$ticketList,
				'title'=>'Other cases for this location',
				'filterTicket'=>$ticket,
				'disableButtons'=>true
			)
		);
	}
}
echo $template->render();