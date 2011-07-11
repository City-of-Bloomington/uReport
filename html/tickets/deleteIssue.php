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
	$issues = $ticket->getIssues();
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL);
	exit();
}

if (!isset($issues[$_GET['index']])) {
	$_SESSION['errorMessages'][] = new Exception('tickets/unknownIssue');
	header('Location: '.$ticket->getURL());
	exit();
}
$issue = $issues[$_GET['index']];

// Once they've confirmed, go ahead and do the delete
if (isset($_REQUEST['confirm'])) {
	try {
		$ticket->removeIssue($_GET['index']);
		$ticket->save();
		header('Location: '.$ticket->getURL());
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
		header('Location: '.$this->return_url);
		exit();
	}
}

// Display the confirmation form
$template = new Template();
$template->blocks[] = new Block(
	'confirmForm.inc',
	array('title'=>'Confirm Delete','return_url'=>$ticket->getURL())
);
$template->blocks[] = new Block(
	'tickets/issueInfo.inc',
	array('ticket'=>$ticket,'issue'=>$issue,'index'=>$_GET['index'],'disableButtons'=>true)
);
echo $template->render();
