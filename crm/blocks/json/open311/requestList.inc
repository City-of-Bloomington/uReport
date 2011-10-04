<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TicketList $this->ticketList
 */
$service_requests = array();
foreach ($this->ticketList as $ticket) {
	$issue = $ticket->getIssue();
	$history = end($ticket->getHistory());

	$media_url = '';
	$media = $issue->getMedia();
	if (count($media)) {
		$m = $media[0];
		$media_url = $m->getURL();
	}

	$service_requests[] = array(
		'service_request_id'=>$ticket->getId(),
		'status'=>$ticket->getStatus(),
		'service_name'=>$ticket->getData('category.name'),
		'service_code'=>$ticket->getData('category._id'),
		'description'=>$issue->getDescription(),
		'requested_datetime'=>$ticket->getEnteredDate('c'),
		'updated_datetime'=>$history->getEnteredDate('c'),
		'address'=>$ticket->getLocation(),
		'zipcode'=>$ticket->getZip(),
		'lat'=>$ticket->getLatitude(),
		'long'=>$ticket->getLongitude(),
		'media_url'=>$media_url
	);
}

echo json_encode($service_requests);