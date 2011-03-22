<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET person_id_a
 * @param GET person_id_b
 */
if (!userIsAllowed('People')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

try {
	$personA = new Person($_GET['person_id_a']);
	$personB = new Person($_GET['person_id_b']);
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL);
	exit();
}

$template = new Template('merging');

$template->blocks['merge-panel-one'][] = new Block(
	'people/personInfo.inc',
	array('person'=>$personA,'disableButtons'=>true)
);

$reportedTickets = $personA->getReportedTickets();
if (count($reportedTickets)) {
	$template->blocks['merge-panel-one'][] = new Block(
		'tickets/searchResults.inc',
		array(
			'ticketList'=>$personA->getReportedTickets(),
			'title'=>'Tickets With Issues Reported By '.$personA->getFullname(),
			'disableButtons'=>true,
			'disableComments'=>true
		)
	);
}
 

$template->blocks['merge-panel-two'][] = new Block(
	'people/personInfo.inc',
	array('person'=>$personB,'disableButtons'=>true)
);
$reportedTickets = $personB->getReportedTickets();
if (count($reportedTickets)) {
	$template->blocks['merge-panel-two'][] = new Block(
		'tickets/searchResults.inc',
		array(
			'ticketList'=>$personB->getReportedTickets(),
			'title'=>'Tickets With Issues Reported By '.$personB->getFullname(),
			'disableButtons'=>true,
			'disableComments'=>true
		)
	);
}
 
echo $template->render();