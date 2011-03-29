<?php
/**
 * The controller for handling issue editing.
 *
 * Choosing a person involves going through a whole person finding process
 * at a different url.  Once the user has chosen a new person, they will
 * return here, passing in the person_id they have chosen
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST issue_id
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
if (isset($_REQUEST['issue_id']) && $_REQUEST['issue_id']) {
	try {
		$issue = new Issue($_REQUEST['issue_id']);
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}
else {
	$issue = new Issue();
}

if (isset($_REQUEST['ticket_id']) && $_REQUEST['ticket_id']) {
	try {
		$issue->setTicket_id($_REQUEST['ticket_id']);
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
		header('Location: '.BASE_URL);
		exit();
	}
}

if (isset($_REQUEST['person_id']) && $_REQUEST['person_id']) {
	try {
		$issue->setReportedByPerson_id($_REQUEST['person_id']);
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
		// No need to send them away.
		// They can just choose another person
	}
}
else {
	$issue->setReportedByPerson($_SESSION['USER']->getPerson());
}

//-------------------------------------------------------------------
// Handle any stuff the user posts
//-------------------------------------------------------------------
if (isset($_POST['issue'])) {
	if (!$issue->getEnteredByPerson_id()) {
		$issue->setEnteredByPerson_id($_SESSION['USER']->getPerson_id());
	}
	$fields = array(
		'issueType_id','reportedByPerson_id',
		'contactMethod_id','responseMethod_id',
		'case_number','notes'
	);
	foreach ($fields as $field) {
		$set = 'set'.ucfirst($field);
		$issue->$set($_POST['issue'][$field]);
	}

	try {
		$issue->save();
		$issue->saveCategories($_POST['issue']['categories']);
		header('Location: '.$issue->getTicket()->getURL());
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
	array('ticket'=>$issue->getTicket())
);
$template->blocks['history-panel'][] = new Block(
	'tickets/history.inc',
	array('ticketHistory'=>$issue->getTicket()->getHistory())
);
$template->blocks['issue-panel'][] = new Block(
	'issues/updateIssueForm.inc',
	array('issue'=>$issue)
);
$template->blocks['location-panel'][] = new Block(
	'locations/locationInfo.inc',
	array('location'=>$issue->getTicket()->getLocation())
);
if ($issue->getTicket()->getLocation()) {
	$template->blocks['location-panel'][] = new Block(
		'tickets/ticketList.inc',
		array(
			'ticketList'=>new TicketList(array('location'=>$issue->getTicket()->getLocation())),
			'title'=>'Other tickets for this location',
			'disableButtons'=>true,
			'filterTicket'=>$issue->getTicket()
		)
	);
}
echo $template->render();
