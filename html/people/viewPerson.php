<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
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
$template->blocks['person-panel'][] = new Block('people/stats.inc',array('person'=>$person));

$tickets = $person->getTickets('issues.reportedBy');
if (count($tickets)) {
	$template->blocks['person-panel'][] = new Block(
		'tickets/ticketList.inc',
		array(
			'ticketList'=>$tickets,
			'title'=>'Reported Tickets',
			'limit'=>10,
			'moreLink'=>BASE_URL."/tickets?reportedByPerson={$person->getId()}"
		)
	);
}
$tickets = $person->getTickets('assigned');
if (count($tickets)) {
	$template->blocks['person-panel'][] = new Block(
		'tickets/ticketList.inc',
		array(
			'ticketList'=>$tickets,
			'title'=>'Assigned Tickets',
			'limit'=>10,
			'moreLink'=>BASE_URL."/tickets?assignedPerson={$person->getId()}"
		)
	);
}
$tickets = $person->getTickets('referred');
if (count($tickets)) {
	$template->blocks['person-panel'][] = new Block(
		'tickets/ticketList.inc',
		array(
			'ticketList'=>$tickets,
			'title'=>'Referred Tickets',
			'limit'=>10,
			'moreLink'=>BASE_URL."/tickets?referredPerson={$person->getId()}"
		)
	);
}

echo $template->render();
