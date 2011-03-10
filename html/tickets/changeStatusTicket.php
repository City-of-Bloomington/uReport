<?php
/**
 * The controller for handling ticket status change.
 *
 * Choosing a person involves going through a whole person finding process
 * at a different url.  Once the user has chosen a new person, they will
 * return here, passing in the person_id they have chosen
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov> W. Sibo <sibow@bloomington.in.gov>
 * @param REQUEST ticket_id
 * 
 */
// Make sure they're supposed to be here
if (!userIsAllowed('Ticket')) {
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

// Handle any stuff the user posts
if (isset($_POST['ticket'])) {
	 
	$fields = array(
		'status','resolution_id'
	);
	foreach ($fields as $field) {
		$set = 'set'.ucfirst($field);
		$ticket->$set($_POST['ticket'][$field]);
	}

	try {
		$ticket->save();		
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
	//
	// add a record to ticket history 
	//
	$status = $ticket->getStatus();
	if ($ticket->getResolution()) {
		$status.= ': '.$ticket->getResolution();
	}	
	$history = new TicketHistory();
	$history->setTicket($ticket);
	$history->setAction($status); 
	$history->setEnteredByPerson_id($_SESSION['USER']->getPerson_id());
	$history->setActionPerson_id($_SESSION['USER']->getPerson_id());
	$history->setNotes($_POST['notes']);

	try {
		$history->save();
		header('Location: '.$ticket()->getURL());
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

// Display the view
$template = new Template('tickets');
$template->blocks['ticket-panel'][] = new Block(
	'tickets/changeStatusTicketForm.inc',
	array('ticket'=>$ticket)
);
$template->blocks['history-panel'][] = new Block(
	'tickets/history.inc',
	array('history'=>$ticket->getHistory())
);
$template->blocks['issue-panel'][] = new Block(
	'issues/issueList.inc',
	array('issueList'=>$ticket->getIssues())
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
			'title'=>'Other tickets for this location'
		)
	);
}
echo $template->render();
