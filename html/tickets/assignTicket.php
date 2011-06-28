<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST ticket_id
 */
// Make sure they're supposed to be here
if (!userIsAllowed('Tickets')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

// Handle any Department choice passed in
if (isset($_GET['department_id'])) {
	try {
		$currentDepartment = new Department($_GET['department_id']);
	}
	catch (Exception $e) {
	}
}
if (!isset($currentDepartment)) {
	$dept = $_SESSION['USER']->getDepartment();
	$currentDepartment = new Department((string)$dept['_id']);
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
if (isset($_REQUEST['assignedPerson'])) {
	try {
		$ticket->setAssignedPerson($_REQUEST['assignedPerson']);

		// add a record to ticket history
		$history = new History();
		$history->setAction('assignment');
		$history->setEnteredByPerson($_SESSION['USER']);
		$history->setActionPerson($ticket->getAssignedPerson());
		$history->setNotes($_REQUEST['notes']);
		$ticket->updateHistory($history);

		$ticket->save();

		$history->sendNotification($ticket);

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
	'departments/chooseDepartmentForm.inc',
	array('currentDepartment'=>$currentDepartment)
);
$template->blocks['ticket-panel'][] = new Block(
	'tickets/assignTicketForm.inc',
	array('ticket'=>$ticket,'currentDepartment'=>$currentDepartment)
);
$template->blocks['history-panel'][] = new Block(
	'tickets/history.inc',
	array('history'=>$ticket->getHistory(),'disableButton'=>true)
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
