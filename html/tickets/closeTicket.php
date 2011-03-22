<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
// Make sure they're supposed to be here
if (!userIsAllowed('Tickets')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

// Load the ticket
try {
	$ticket = new Ticket($_REQUEST['ticket_id']);
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL);
	exit();
}

if (isset($_POST['resolution_id'])) {
	$ticket->setResolution_id($_POST['resolution_id']);

	// add a record to ticket history
	$history = new TicketHistory();
	$history->setTicket($ticket);
	$history->setAction('close');
	$history->setEnteredByPerson_id($_SESSION['USER']->getPerson_id());
	$history->setActionPerson_id($ticket->getAssignedPerson_id());
	$history->setNotes($_POST['notes']);

	try {
		$ticket->save();
		$history->save();
		header('Location: '.$ticket->getURL());
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}


// Display the view
$template = new Template('tickets');
$template->blocks['ticket-panel'][] = new Block(
	'tickets/ticketInfo.inc',
	array('ticket'=>$ticket,'disableButtons'=>true)
);
$template->blocks['ticket-panel'][] = new Block(
	'tickets/closeTicketForm.inc',
	array('ticket'=>$ticket)
);
$template->blocks['history-panel'][] = new Block(
	'tickets/history.inc',
	array('ticketHistory'=>$ticket->getHistory(),'disableButtons'=>true)
);
$template->blocks['issue-panel'][] = new Block(
	'issues/issueList.inc',
	array('issueList'=>$ticket->getIssues(),'disableButtons'=>true)
);
if ($ticket->getLocation()) {
	$template->blocks['location-panel'][] = new Block(
		'locations/locationInfo.inc',
		array('location'=>$ticket->getLocation())
	);
	$template->blocks['location-panel'][] = new Block(
		'tickets/searchResults.inc',
		array(
			'ticketList'=>new TicketList(array('location'=>$ticket->getLocation())),
			'title'=>'Other tickets for this location',
			'disableButtons'=>true
		)
	);
}
echo $template->render();
