<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET person_id
 */
if (!isset($_GET['person_id'])) {
	header('Location: '.BASE_URL.'/people');
	exit();
}
try {
	$person = new Person($_GET['person_id']);
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL.'/people');
	exit();
}

$template = new Template('people');
$template->title = $person->getFullname();
$template->blocks['person-panel'][] = new Block('people/personInfo.inc',array('person'=>$person));

$reportedTickets = $person->getReportedTickets();
if (count($reportedTickets)) {
	$template->blocks['ticket-panel'][] = new Block(
		'tickets/ticketList.inc',
		array(
			'ticketList'=>$person->getReportedTickets(),
			'title'=>'Tickets With Issues Reported By '.$person->getFullname()
		)
	);
}

echo $template->render();
