<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
// Grab the format from the file extension used in the url
$format = preg_match("/[^.]+$/",$_SERVER['REQUEST_URI'],$matches)
	? strtolower($matches[0])
	: 'html';
$template = new Template('open311',$format);

// See if they're asking for a particular request (ticket)
preg_match('|/open311/v2/requests/?([0-9a-f]{24})?.*|',$_SERVER['REQUEST_URI'],$matches);
if (isset($matches[1]) && $matches[1]) {
	try {
		$ticket = new Ticket($matches[1]);
		if (isset($_POST)) {
			// Edit an existing ticket

		}
		else {
			// Display an existing ticket
		}
	}
	catch (Exception $e) {
		// Unknown ticket
	}
}
else {
	if (isset($_POST)) {
		// Add a new ticket
		$ticket = new Ticket();
		$ticket->setEnteredByPerson($_SESSION['USER']);
		$ticket->set($_POST);

		// Create the issue
		$issue->setEnteredByPerson($_SESSION['USER']);
		$issue->set($_POST['issue']);

		// Create the History entries
		$open = new History();
		$open->setAction('open');
		$open->setEnteredByPerson($_SESSION['USER']);
		$open->setActionPerson($_SESSION['USER']);

		// Record the assignment
		$assignment = new History();
		$assignment->setAction('assignment');
		$assignment->setEnteredByPerson($_SESSION['USER']);
		$assignment->setActionPerson($ticket->getAssignedPerson());
		$assignment->setNotes($_REQUEST['notes']);
	}
	else {
		// Do a search for tickets
	}
}

echo $template->render();