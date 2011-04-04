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
	$ticket->setAddressServiceCache(AddressService::getLocationData($ticket->getLocation()));
}
if (isset($_REQUEST['person_id'])) {
	$issue->setReportedByPerson_id($_REQUEST['person_id']);
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
	$ticket->setAddressServiceCache(AddressService::getLocationData($ticket->getLocation()));

	$ticket->setEnteredDate(new Date());
	$ticket->setAssignedPerson_id($_POST['assignedPerson_id']);
	$ticket->setEnteredByPerson_id($_SESSION['USER']->getPerson_id());

	// Create the issue
	$fields = array(
		'issueType_id','reportedByPerson_id',
		'contactMethod_id','responseMethod_id',
		'case_number','notes'
	);
	foreach ($fields as $field) {
		$set = 'set'.ucfirst($field);
		$issue->$set($_POST['issue'][$field]);
	}
	$issue->setEnteredByPerson_id($_SESSION['USER']->getPerson_id());

	// Create the TicketHistory entries
	$open = new TicketHistory();
	$open->setAction('open');
	$open->setEnteredDate($ticket->getEnteredDate());
	$open->setActionDate($ticket->getEnteredDate());
	$open->setEnteredByPerson($_SESSION['USER']->getPerson());
	$open->setActionPerson($_SESSION['USER']->getPerson());

	$assignment = new TicketHistory();
	$assignment->setAction('assignment');
	$assignment->setEnteredDate($ticket->getEnteredDate());
	$assignment->setActionDate($ticket->getEnteredDate());
	$assignment->setEnteredByPerson($_SESSION['USER']->getPerson());
	$assignment->setActionPerson_id($_POST['assignedPerson_id']);

	// Validate Everything and save
	try {
		$ticket->validate();
		$issue->validate(true);
		$open->validate(true);
		$assignment->validate(true);

		$ticket->save();

		$issue->setTicket($ticket);
		$open->setTicket($ticket);
		$assignment->setTicket($ticket);

		$issue->save();
		$open->save();
		$assignment->save();

		header('Location: '.$ticket->getURL());
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
		print_r($e);
		exit();
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

$personPanel = $issue->getReportedByPerson()
	? new Block('people/personInfo.inc',array('person'=>$issue->getReportedByPerson()))
	: new Block('people/searchForm.inc',array('return_url'=>$return_url));
$template->blocks['person-panel'][] = $personPanel;

$addTicketForm = new Block(
	'tickets/addTicketForm.inc',
	array('ticket'=>$ticket,'issue'=>$issue)
);
$template->blocks['ticket-panel'][] = $addTicketForm;


#$template->addToAsset('scripts',YUI.'/yui/yui-min.js');
echo $template->render();
