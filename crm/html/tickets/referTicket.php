<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST ticket_id
 * @param REQUEST person_id
 */
// Make sure they're supposed to be here
if (!userIsAllowed('Tickets')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

// Load the Ticket and Person
try {
	$ticket = new Ticket($_REQUEST['ticket_id']);
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL);
	exit();
}

if (isset($_REQUEST['person_id'])) {
	try {
		$person = new Person($_REQUEST['person_id']);
	}
	catch (Exception $e) {
	}
}

// Handle any stuff the user posts
if (isset($_POST['referredPerson'])) {
	try {
		$ticket->setReferredPerson($_POST['referredPerson']);

		// add a record to ticket history
		$history = new History();
		$history->setAction('referral');
		$history->setEnteredByPerson($_SESSION['USER']);
		$history->setActionPerson($ticket->getReferredPerson());
		$history->setNotes($_POST['notes']);
		$ticket->updateHistory($history);

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
if (isset($person)) {
	$template->blocks['ticket-panel'][] = new Block(
		'tickets/referTicketForm.inc',
		array('ticket'=>$ticket,'person'=>$person)
	);
}
else {
	$template->blocks['ticket-panel'][] = new Block(
		'people/searchForm.inc',
		array('return_url'=>BASE_URL.'/tickets/referTicket.php?ticket_id='.$ticket->getId())
	);
}
$template->blocks['history-panel'][] = new Block(
	'tickets/history.inc',
	array('history'=>$ticket->getHistory(),'disableButtons'=>true)
);
$template->blocks['issue-panel'][] = new Block(
	'tickets/issueList.inc',
	array('ticket'=>$ticket,'issueList'=>$ticket->getIssues(),'disableButtons'=>true)
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
			'disableButtons'=>true
		)
	);
}
echo $template->render();
