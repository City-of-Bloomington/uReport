<?php
/**
 * The controller for handling issue editing.
 *
 * Choosing a person involves going through a whole person finding process
 * at a different url.  Once the user has chosen a new person, they will
 * return here, passing in the person_id they have chosen
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST ticket_id
 * @param REQUEST index The index number of the issue
 * @param REQUEST person_id The new person to apply to the issue
 */
//-------------------------------------------------------------------
// Make sure they're supposed to be here
//-------------------------------------------------------------------
if (!userIsAllowed('Issues')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

//-------------------------------------------------------------------
// Load all the data that's passed in
//-------------------------------------------------------------------
try {
	$ticket = new Ticket($_REQUEST['ticket_id']);
	$issues = $ticket->getIssues();
	if (isset($_REQUEST['index']) && array_key_exists($_REQUEST['index'],$issues)) {
		$issue = $issues[$_REQUEST['index']];
		$index = (int)$_REQUEST['index'];
	}
	else {
		$issue = new Issue();
		$index = null;
	}
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL.'/tickets');
	exit();
}

if (isset($_REQUEST['person_id'])) {
	$issue->setReportedByPerson($_REQUEST['person_id']);
}

//-------------------------------------------------------------------
// Handle any stuff the user posts
//-------------------------------------------------------------------
if (isset($_POST['issue'])) {
	if (!$issue->getEnteredByPerson()) {
		$issue->setEnteredByPerson($_SESSION['USER']);
	}
	$issue->set($_POST['issue']);
	$ticket->updateIssues($issue,$index);

	try {
		$ticket->save();
		header('Location: '.$ticket->getURL());
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

//-------------------------------------------------------------------
// Display the view
//-------------------------------------------------------------------
$template = new Template('tickets');
$template->blocks['ticket-panel'][] = new Block(
	'tickets/ticketInfo.inc',
	array('ticket'=>$ticket,'disableButtons'=>true)
);
$template->blocks['history-panel'][] = new Block(
	'tickets/history.inc',
	array('history'=>$ticket->getHistory())
);
$template->blocks['issue-panel'][] = new Block(
	'tickets/updateIssueForm.inc',
	array('ticket'=>$ticket,'index'=>$index,'issue'=>$issue)
);
$template->blocks['location-panel'][] = new Block(
	'locations/locationInfo.inc',
	array('location'=>$ticket->getLocation())
);
if ($ticket->getLocation()) {
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
