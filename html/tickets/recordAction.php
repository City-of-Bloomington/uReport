<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST ticket_id
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

// Handle any stuff the user posts
if (isset($_POST['action_id'])) {
	// add a record to ticket history
	$history = new TicketHistory();
	$history->setTicket($ticket);
	$history->setAction_id($_POST['action_id']);
	$history->setActionDate($_POST['actionDate']);
	$history->setEnteredByPerson_id($_SESSION['USER']->getPerson_id());
	$history->setActionPerson_id($_SESSION['USER']->getPerson_id());
	$history->setNotes($_POST['notes']);

	try {
		$history->save();
		if ($history->getStatus()) {
			$ticket->setStatus($history->getStatus());
			$ticket->save();
		}
		header('Location: '.$ticket->getURL());
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

header('Location: '.$ticket->getURL());