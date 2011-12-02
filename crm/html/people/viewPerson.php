<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET person_id
 * @param GET disableLinks
 */
$disableLinks = isset($_GET['disableLinks']) ? (bool)$_GET['disableLinks'] : false;

if (!userIsAllowed('People')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

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

$format = isset($_GET['format']) ?  $_GET['format'] : 'html';
$filename = isset($_GET['partial']) ? 'partial' : 'people';
$template = new Template($filename, $format);

$template->title = $person->getFullname();
$template->blocks['person-panel'][] = new Block('people/personInfo.inc',array('person'=>$person));
if (!$disableLinks && userIsAllowed('Tickets')) {
	$template->blocks['person-panel'][] = new Block(
		'tickets/addNewForm.inc',
		array('return_url'=>new URL(BASE_URL.'/tickets/addTicket.php'),'title'=>'Report New Case')
	);
}
$template->blocks['person-panel'][] = new Block('people/stats.inc',array('person'=>$person));

$tickets = $person->getTickets('reportedBy');
if (count($tickets)) {
	$template->blocks['person-panel'][] = new Block(
		'tickets/ticketList.inc',
		array(
			'ticketList'=>$tickets,
			'title'=>'Reported Cases',
			'limit'=>10,
			'disableLinks'=>$disableLinks,
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
			'title'=>'Assigned Cases',
			'limit'=>10,
			'disableLinks'=>$disableLinks,
			'moreLink'=>BASE_URL."/tickets?assignedPerson[]={$person->getId()}"
		)
	);
}
$tickets = $person->getTickets('referred');
if (count($tickets)) {
	$template->blocks['person-panel'][] = new Block(
		'tickets/ticketList.inc',
		array(
			'ticketList'=>$tickets,
			'title'=>'Referred Cases',
			'limit'=>10,
			'disableLinks'=>$disableLinks,
			'moreLink'=>BASE_URL."/tickets?referredPerson={$person->getId()}"
		)
	);
}
$tickets = $person->getTickets('enteredBy');
if (count($tickets)) {
	$template->blocks['person-panel'][] = new Block(
		'tickets/ticketList.inc',
		array(
			'ticketList'=>$tickets,
			'title'=>'Entered Cases',
			'limit'=>10,
			'disableLinks'=>$disableLinks,
			'moreLink'=>BASE_URL."/tickets?enteredByPerson={$person->getId()}"
		)
	);
}

echo $template->render();
