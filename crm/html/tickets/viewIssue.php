<?php
/**
 * Page to view a single issue from a ticket
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
try {
	$ticket = new Ticket($_GET['ticket_id']);
	$issues = $ticket->getIssues();
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL.'/tickets');
	exit();
}

if (!isset($issues[$_GET['index']])) {
	$_SESSION['errorMessages'][] = new Exception('tickets/unknownIssue');
	header('Location: '.$ticket->getURL());
	exit();
}

$issue = $issues[$_GET['index']];

$template = new Template('issues');
$template->blocks['ticket-panel'][] = new Block('tickets/ticketInfo.inc',array('ticket'=>$ticket));

$person = $issue->getPersonObject('reportedByPerson');
if ($person) {
	$template->blocks['person-panel'][] = new Block(
		'people/personInfo.inc',
		array('person'=>$person,'disableButtons'=>true)
	);
}
$template->blocks['issue-panel'][] = new Block(
	'tickets/issueInfo.inc',
	array('ticket'=>$ticket,'issue'=>$issue,'index'=>$_GET['index'])
);

echo $template->render();