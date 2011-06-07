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

// Handle any Location choice passed in
if (isset($_GET['location']) && $_GET['location']) {
	$ticket->setLocation($_GET['location']);
	$ticket->setAddressServiceData(AddressService::getLocationData($ticket->getLocation()));
}

// Handle any Person choice passed in
if (isset($_REQUEST['person_id'])) {
	$person = new Person($_REQUEST['person_id']);
	$issue->setReportedByPerson($person);
}

// Handle any Category choice passed in
if (isset($_REQUEST['category_id'])) {
	$issue->setCategory($_REQUEST['category_id']);
}

// Handle any Department choice passed in
if (isset($_GET['department_id'])) {
	try {
		$currentDepartment = new Department($_GET['department_id']);
	}
	catch (Exception $e) {
	}
}
if (!isset($currentDepartment)) {
	$dept = $_SESSION['USER']->getDepartment();
	$currentDepartment = new Department((string)$dept['_id']);
}

// Process the ticket form when it's posted
if(isset($_POST['ticket'])){
	$ticket->setAssignedPerson($_POST['assignedPerson']);
	$ticket->setEnteredByPerson($_SESSION['USER']);

	// Set all the location information using any fields the user posted
	$fields = array(
		'location','latitude','longitude','city','state','zip'
	);
	foreach ($fields as $field) {
		if (isset($_POST['ticket'][$field])) {
			$set = 'set'.ucfirst($field);
			$ticket->$set($_POST['ticket'][$field]);
		}
	}

	// If the location the user posted is a valid address, overwrite what
	// the user posted with data from the AddressService
	$data = AddressService::getLocationData($ticket->getLocation());
	if ($data) {
		$ticket->setAddressServiceData($data);
	}


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

//-------------------------------------------------------------------
// Location Panel
//-------------------------------------------------------------------
if ($ticket->getLocation()) {
	$template->blocks['location-panel'][] = new Block('tickets/changeLocationButton.inc');
	$template->blocks['location-panel'][] = new Block(
		'locations/locationInfo.inc',
		array('location'=>$ticket->getLocation(),'disableButtons'=>true)
	);

	$template->blocks['location-panel'][] = new Block(
		'tickets/ticketList.inc',
		array(
			'ticketList'=>new TicketList(array('location'=>$ticket->getLocation())),
			'title'=>'Cases Associated with this Location',
			'disableButtons'=>true
		)
	);
}
else {
	$template->blocks['location-panel'][] = new Block(
		'locations/findLocationForm.inc',
		array(
			'return_url'=>$return_url,
			'includeExternalResults'=>true,
			'title'=>'Associate a Location',
			'description'=>'Search for a location to associate it with the case.'
		)
	);
}

//-------------------------------------------------------------------
// Person Panel
//-------------------------------------------------------------------
if (isset($person)) {
	$template->blocks['person-panel'][] = new Block('tickets/changePersonButton.inc');
	$template->blocks['person-panel'][] = new Block(
		'people/personInfo.inc',
		array(
			'person'=>$person,
			'disableButtons'=>true
		)
	);
	$reportedTickets = $person->getTickets('issues.reportedBy');
	if (count($reportedTickets)) {
		$template->blocks['person-panel'][] = new Block(
			'tickets/ticketList.inc',
			array(
				'ticketList'=>$reportedTickets,
				'title'=>'Reported Cases',
				'disableButtons'=>true,
				'limit'=>10,
				'moreLink'=>BASE_URL."/tickets?reportedByPerson={$person->getId()}"
			)
		);
	}
}
else {
	$template->blocks['person-panel'][] = new Block(
		'people/searchForm.inc',
		array('return_url'=>$return_url,'title'=>'Person Reporting the Issue')
	);
}

//-------------------------------------------------------------------
// Ticket Panel
//-------------------------------------------------------------------
$template->blocks['ticket-panel'][] = new Block(
	'tickets/addTicketForm.inc',
	array(
		'ticket'=>$ticket,
		'issue'=>$issue,
		'return_url'=>$return_url,
		'currentDepartment'=>$currentDepartment
	)
);

echo $template->render();
