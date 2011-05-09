<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST ticket_id
 */
if (!userIsAllowed('Tickets')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

try {
	$ticket = new Ticket($_REQUEST['ticket_id']);
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL);
	exit();
}

// Once the user has chosen a location, they'll pass it in here
if (isset($_REQUEST['location']) && $_REQUEST['location']) {
	$ticket->clearAddressServiceCache();
	$ticket->setLocation($_REQUEST['location']);
	$ticket->setAddressServiceCache(AddressService::getLocationData($ticket->getLocation()));
	try {
		$ticket->save();
		header('Location: '.$ticket->getURL());
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$return_url = new URL($_SERVER['SERVER_NAME'].$_SERVER['REQUEST_URI']);

$template = new Template('tickets');
$template->blocks['ticket-panel'][] = new Block(
	'locations/findLocationForm.inc',
	array('return_url'=>$return_url,'includeExternalResults'=>true)
);

$template->blocks['history-panel'][] = new Block(
	'tickets/history.inc',
	array('ticketHistory'=>$ticket->getHistory())
);

$template->blocks['issue-panel'][] = new Block(
	'tickets/issueList.inc',
	array('issueList'=>$ticket->getIssues(),'ticket'=>$ticket)
);

if ($ticket->getLocation()) {
	$template->blocks['location-panel'][] = new Block(
		'locations/locationInfo.inc',
		array('location'=>$ticket->getLocation())
	);

	$ticketList = new TicketList(array('location'=>$ticket->getLocation()));
	if (count($ticketList) > 1) {
		$template->blocks['location-panel'][] = new Block(
			'tickets/ticketList.inc',
			array(
				'ticketList'=>$ticketList,
				'title'=>'Other tickets for this location',
				'filterTicket'=>$ticket
			)
		);
	}
}
echo $template->render();