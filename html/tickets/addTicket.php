<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (!userIsAllowed('Tickets')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

$ticket = new Ticket();
$issue = new Issue();

// If the user has chosen a location, they'll pass it in here
if (isset($_GET['location']) && $_GET['location']) {
	$ticket->setLocation($_GET['location']);
	$ticket->setAddressServiceData(AddressService::getLocationData($ticket->getLocation()));
}

if (isset($_REQUEST['person_id'])) {
	$issue->setReportedByPerson(new Person($_REQUEST['person_id']));
}

if (isset($_REQUEST['category_id'])) {
	$issue->setCategory($_REQUEST['category_id']);
}

if(isset($_POST['ticket'])){
	// Create the ticket
	$fields = array(
		'location','latitude','longitude','address_id','city','state','zip'
	);
	foreach ($fields as $field) {
		if (isset($_POST['ticket'][$field])) {
			$set = 'set'.ucfirst($field);
			$ticket->$set($_POST['ticket'][$field]);
		}
	}
	$ticket->setAddressServiceData(AddressService::getLocationData($ticket->getLocation()));

	$ticket->setAssignedPerson($_POST['assignedPerson']);
	$ticket->setEnteredByPerson($_SESSION['USER']);

	// Create the issue
	$fields = array(
		'type','reportedByPerson',
		'contactMethod','responseMethod',
		'category','notes'
	);
	foreach ($fields as $field) {
		$set = 'set'.ucfirst($field);
		$issue->$set($_POST['issue'][$field]);
	}
	$issue->setEnteredByPerson($_SESSION['USER']);

	// Create the History entries
	$open = new History();
	$open->setAction('open');
	$open->setEnteredByPerson($_SESSION['USER']);
	$open->setActionPerson($_SESSION['USER']);

	$assignment = new History();
	$assignment->setAction('assignment');
	$assignment->setEnteredByPerson($_SESSION['USER']);
	$assignment->setActionPerson($_POST['assignedPerson']);

	// Validate Everything and save
	try {
		$ticket->updateIssues($issue);
		$ticket->updateHistory($open);
		$ticket->updateHistory($assignment);
		$ticket->save();

		header('Location: '.$ticket->getURL());
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}


$template = new Template('ticketCreation');

$return_url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);

if ($ticket->getLocation()) {
	$template->blocks['location-panel'][] = new Block(
		'locations/locationInfo.inc',
		array('location'=>$ticket->getLocation())
	);

	$template->blocks['location-panel'][] = new Block(
		'tickets/ticketList.inc',
		array(
			'ticketList'=>new TicketList(array('location'=>$ticket->getLocation())),
			'title'=>'Tickets Associated with this Location'
		)
	);
}
else {
	$template->blocks['location-panel'][] = new Block(
		'locations/findLocationForm.inc',
		array('return_url'=>$return_url,'includeExternalResults'=>true)
	);
}

if ($issue->getReportedByPerson()) {
	$personPanel = new Block(
		'people/personInfo.inc',
		array(
			'person'=>$issue->getPersonObject('reportedByPerson'),
			'disableButtons'=>true
		)
	);
}
else {
	$personPanel = new Block('people/searchForm.inc',array('return_url'=>$return_url));
}
$template->blocks['person-panel'][] = $personPanel;

$addTicketForm = new Block(
	'tickets/addTicketForm.inc',
	array('ticket'=>$ticket,'issue'=>$issue)
);
$template->blocks['ticket-panel'][] = $addTicketForm;

echo $template->render();
