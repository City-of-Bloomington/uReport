<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
$issue = $this->ticket->getIssue();
$history = end($this->ticket->getHistory());

$media_url = '';
$media = $issue->getMedia();
if (count($media)) {
	$m = $media[0];
	$media_url = $m->getURL();
}

$request = array(
	'service_request_id'=>"{$this->ticket->getId()}",
	'status'=>$this->ticket->getStatus(),
	'service_name'=>$this->ticket->getData('category.name'),
	'service_code'=>"{$this->ticket->getData('category._id')}",
	'description'=>$issue->getDescription(),
	'requested_datetime'=>$this->ticket->getEnteredDate('c'),
	'updated_datetime'=>$history->getEnteredDate('c'),
	'address'=>$this->ticket->getLocation(),
	'zipcode'=>$this->ticket->getZip(),
	'lat'=>$this->ticket->getLatitude(),
	'long'=>$this->ticket->getLongitude(),
	'media_url'=>$media_url
);
echo json_encode($request);
