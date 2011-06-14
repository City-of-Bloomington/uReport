<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
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

if (isset($_POST['resolution'])) {
	$ticket->setResolution($_POST['resolution']);
	$ticket->setStatus('closed');

	// add a record to ticket history
	$history = new History();
	$history->setAction('close');
	$history->setEnteredByPerson($_SESSION['USER']);
	$history->setActionPerson($_SESSION['USER']);
	$history->setNotes($_POST['notes']);
	$ticket->updateHistory($history);

	try {
		$ticket->save();
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
	array('history'=>$ticket->getHistory(),'disableButtons'=>true)
);
$template->blocks['issue-panel'][] = new Block(
	'tickets/issueList.inc',
	array('issueList'=>$ticket->getIssues(),'disableButtons'=>true)
);
if ($ticket->getLocation()) {
	$template->blocks['location-panel'][] = new Block(
		'locations/locationInfo.inc',
		array('location'=>$ticket->getLocation())
	);
	$template->blocks['location-panel'][] = new Block(
		'tickets/ticketList.inc',
		array(
			'ticketList'=>new TicketList(array('location'=>$ticket->getLocation())),
			'title'=>'Other tickets for this location',
			'disableButtons'=>true,
			'filterTicket'=>$ticket
		)
	);
}
echo $template->render();
