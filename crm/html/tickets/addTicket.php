<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
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
	$category = new Category($_REQUEST['category_id']);
	if ($category->allowsPosting($_SESSION['USER'])) {
		$ticket->setCategory($_REQUEST['category_id']);
	}
	else {
		$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
		header('Location: '.BASE_URL);
		exit();
	}
}

// Handle any Department choice passed in
// Choosing a department here will cause the assignment form
// to pre-select that department's defaultPerson
if (isset($_GET['department_id'])) {
	try {
		$currentDepartment = new Department($_GET['department_id']);
	}
	catch (Exception $e) {
	}
}
// If they haven't chosen a department, start by assigning
// the ticket to the current User, and use the current user's department
if (!isset($currentDepartment)) {
	$ticket->setAssignedPerson($_SESSION['USER']);

	$dept = $_SESSION['USER']->getDepartment();
	$currentDepartment = new Department((string)$dept['_id']);
}

// Process the ticket form when it's posted
if(isset($_POST['ticket'])){
	if (isset($_POST['assignedPerson'])) {
		$_POST['ticket']['assignedPerson'] = $_POST['assignedPerson'];
		$_POST['ticket']['notes'] = $_POST['notes'];
	}
	// Validate Everything and save
	try {
		$ticket->set($_POST['ticket']);
		$issue->set($_POST['issue']);
		$ticket->updateIssues($issue);
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
	$template->blocks['location-panel'][] = new Block(
		'locations/locationInfo.inc',
		array('location'=>$ticket->getLocation(),'disableButtons'=>true)
	);

	$template->blocks['location-panel'][] = new Block(
		'tickets/ticketList.inc',
		array(
			'ticketList'=>new TicketList(array('location'=>$ticket->getLocation())),
			'title'=>'Cases Associated with this Location',
			'disableLinks'=>true
		)
	);
}

//-------------------------------------------------------------------
// Person Panel
//-------------------------------------------------------------------
if (isset($person)) {
	$template->blocks['person-panel'][] = new Block(
		'people/personInfo.inc',
		array(
			'person'=>$person,
			'disableButtons'=>true
		)
	);
	$reportedTickets = $person->getTickets('reportedBy');
	if (count($reportedTickets)) {
		$template->blocks['person-panel'][] = new Block(
			'tickets/ticketList.inc',
			array(
				'ticketList'=>$reportedTickets,
				'title'=>'Reported Cases',
				'disableButtons'=>true,
				'disableLinks'=>true,
				'limit'=>10,
				'moreLink'=>BASE_URL."/tickets?reportedByPerson={$person->getId()}"
			)
		);
	}
}

//-------------------------------------------------------------------
// Ticket Panel
//-------------------------------------------------------------------
$template->blocks['ticket-panel'][] = new Block('tickets/changeLocationButton.inc');
$template->blocks['ticket-panel'][] = new Block('tickets/changePersonButton.inc');
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
