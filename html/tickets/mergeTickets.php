<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET ticket_id_a
 * @param GET ticket_id_b
 */
if (!userIsAllowed('Tickets')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

try {
	$ticketA = new Ticket($_GET['ticket_id_a']);
	$ticketB = new Ticket($_GET['ticket_id_b']);
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL);
	exit();
}

$template = new Template('merging');

$template->blocks['merge-panel-one'][] = new Block(
	'tickets/ticketInfo.inc',
	array('ticket'=>$ticketA,'disableButtons'=>true)
);
$template->blocks['merge-panel-one'][] = new Block(
	'tickets/history.inc',
	array('ticketHistory'=>$ticketA->getHistory(),'disableComments'=>true)
);
$template->blocks['merge-panel-one'][] = new Block(
	'issues/issueList.inc',
	array(
		'issueList'=>$ticketA->getIssues(),
		'ticket'=>$ticketA,
		'disableButtons'=>true,
		'disableComments'=>true
	)
);

$template->blocks['merge-panel-two'][] = new Block(
	'tickets/ticketInfo.inc',
	array('ticket'=>$ticketB,'disableButtons'=>true)
);
$template->blocks['merge-panel-two'][] = new Block(
	'tickets/history.inc',
	array('ticketHistory'=>$ticketB->getHistory(),'disableComments'=>true)
);
$template->blocks['merge-panel-two'][] = new Block(
	'issues/issueList.inc',
	array(
		'issueList'=>$ticketB->getIssues(),
		'ticket'=>$ticketB,
		'disableButtons'=>true,
		'disableComments'=>true
	)
);
echo $template->render();