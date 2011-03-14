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
// If the user has chosen a location, they'll pass it in here
if (isset($_GET['location'])) {
	$ticket->setLocation($_GET['location']);

	// Look up the rest of the data in Master Address
	$url = new URL(MASTER_ADDRESS.'/home.php');
	$url->queryType = 'address';
	$url->format = 'xml';
	$url->query = $ticket->getLocation();

	$xml = new SimpleXMLElement($url,null,true);
	if (count($xml)==1) {
		$ticket->setLocation($xml->address->streetAddress);
		$ticket->setStreet_address_id($xml->address->id);
		$ticket->setTownship($xml->address->township);
		$ticket->setLatitude($xml->address->latitude);
		$ticket->setLongitude($xml->address->longitude);

		// See if there's a neighborhood association
		$neighborhood = $xml->xpath("//purpose[@type='NEIGHBORHOOD ASSOCIATION']");
		if ($neighborhood) {
			$ticket->setNeighborhoodAssociation($neighborhood[0]);
		}

		// See if this is a subunit
		$url = new URL(MASTER_ADDRESS.'/addresses/parse.php');
		$url->format = 'xml';
		$url->address = $ticket->getLocation();
		$parsed = new SimpleXMLElement($url,null,true);
		if ($parsed->subunitIdentifier) {
			$subunit = $xml->xpath("//subunit[identifier='{$parsed->subunitIdentifier}']");
			if ($subunit) {
				$ticket->setSubunit_id($subunit[0]['id']);
				$ticket->setLocation($ticket->getLocation()." {$subunit[0]->type} {$subunit[0]->identifier}");
			}
		}
	}
}
if(isset($_POST['ticket'])){
	try {
		$fields = array('location','street_address_id','subunit_id',
			'neighborhoodAssociation','township','latitude','logitude'
		);
		foreach ($fields as $field) {
			$set = 'set'.ucfirst($field);
			$ticket->$set($_POST['ticket'][$field]);
		}
		$ticket->setEnteredDate(new Date());
		$ticket->setStatus("open");
		$ticket->setAssignedPerson_id($_SESSION['USER']->getPerson_id());
		$ticket->setEnteredByPerson_id($_SESSION['USER']->getPerson_id());
		$ticket->save();
		//
		// add a record to the history
		//
		

	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}


$template = new Template('locations');

$locationPanel = $ticket->getLocation() ?
	new Block('locations/locationInfo.inc',array('location'=>$ticket->getLocation()))
	: new Block(
		'locations/findLocationForm.inc',
		array('return_url'=>BASE_URL.'/tickets/addTicket.php','includeExternalResults'=>true)
	);
	
$template->blocks['location-panel'][] = $locationPanel;

#$template->blocks['person-panel'][] = new Block('people/searchForm.inc');

$addTicketForm = new Block('tickets/addTicketForm.inc',array('ticket'=>$ticket));
// If we've chosen a location, look up whatever data we can in Master Address
if ($ticket->getLocation()) {
}
$template->blocks['ticket-panel'][] = $addTicketForm;


#$template->addToAsset('scripts',YUI.'/yui/yui-min.js');
echo $template->render();
