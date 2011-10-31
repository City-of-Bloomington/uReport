<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
$status = strtolower($this->ticket->getStatus());
$service_name = View::escape($this->ticket->getData('category.name'));
$service_code = $this->ticket->getData('category._id');

$issue = $this->ticket->getIssue();
$description = View::escape($issue->getDescription());

$history = end($this->ticket->getHistory());

$address = View::escape($this->ticket->getLocation());
$zipcode = View::escape($this->ticket->getZip());

$media_url = '';
$media = $issue->getMedia();
if (count($media)) {
	$m = $media[0];
	$media_url = $m->getURL();
}

echo "
<request>
	<service_request_id>{$this->ticket->getId()}</service_request_id>
	<status>$status</status>
	<service_name>$service_name</service_name>
	<service_code>$service_code</service_code>
	<description>$description</description>
	<requested_datetime>{$this->ticket->getEnteredDate('c')}</requested_datetime>
	<updated_datetime>{$history->getEnteredDate('c')}</updated_datetime>
	<address>$address</address>
	<address_id>{$this->ticket->getAddress_id()}</address_id>
	<zipcode>$zipcode</zipcode>
	<lat>{$this->ticket->getLatitude()}</lat>
	<long>{$this->ticket->getLongitude()}</long>
	<media_url>$media_url</media_url>
</request>
";