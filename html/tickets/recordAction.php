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
if (isset($_POST['action'])) {
	// add a record to ticket history
	$history = new History();
	$history->setAction($_POST['action']);
	$history->setActionDate($_POST['actionDate']);
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

header('Location: '.$ticket->getURL());