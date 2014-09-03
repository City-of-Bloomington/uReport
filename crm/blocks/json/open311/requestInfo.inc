<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
$c = $this->ticket->getCategory();
$service_name = $c ? $c->getName() : '';
$service_code = $c ? $c->getId() : '';

$issue = $this->ticket->getIssue();
$h = $this->ticket->getHistory();
$history = end($h);

$media_url = '';
$media = $issue->getMedia();
if (count($media)) {
	$m = $media[0];
	$media_url = $m->getURL();
}

$agency_responsible = '';
$p = $this->ticket->getAssignedPerson();
if ($p) {
	$d = $p->getDepartment();
	if ($d) {
		$agency_responsible = $d->getName();
	}
}

$request = array(array(
	'service_request_id'=>$this->ticket->getId(),
	'status'=>strtolower($this->ticket->getStatus()),
	'service_name'=>$service_name,
	'service_code'=>$service_code,
	'description'=>$issue->getDescription(),
	'agency_responsible'=>$agency_responsible,
	'requested_datetime'=>$this->ticket->getEnteredDate('c'),
	'updated_datetime'=>$history->getEnteredDate('c'),
	'address'=>$this->ticket->getLocation(),
	'address_id'=>$this->ticket->getAddressId(),
	'zipcode'=>$this->ticket->getZip(),
	'lat'=>$this->ticket->getLatitude(),
	'long'=>$this->ticket->getLongitude(),
	'media_url'=>$media_url
));
echo json_encode($request);
