<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST ticket_id
 * @param REQUEST index
 */
// Make sure they're supposed to be here
if (!userIsAllowed('Issues')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

// Load the ticket
try {
	$ticket = new Ticket($_REQUEST['ticket_id']);
	$index = (int)$_REQUEST['index'];
	$issue = $ticket->getIssue($index);
	if (!$issue) {
		throw new Exception('unknownIssue');
	}
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL);
	exit();
}

// Handle what the user posts
if (isset($_POST['contactMethod'])) {
	$response = new Response();
	$response->setPerson($_SESSION['USER']);
	$response->setContactMethod($_POST['contactMethod']);
	$response->setNotes($_POST['notes']);

	try {
		$ticket->addResponse($index,$response);
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
	array('ticket'=>$ticket)
);
$template->blocks['history-panel'][] = new Block(
	'tickets/history.inc',
	array('history'=>$ticket->getHistory())
);
$template->blocks['issue-panel'][] = new Block(
	'tickets/responseForm.inc',
	array('ticket'=>$ticket,'index'=>$index)
);
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
echo $template->render();
