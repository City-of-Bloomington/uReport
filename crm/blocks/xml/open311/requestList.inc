<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TicketList $this->ticketList
 */
echo "<service_requests>";
foreach ($this->ticketList as $ticket) {
	$service_name = View::escape($ticket->getData('category.name'));
	$service_code = $ticket->getData('category._id');

	$issue = $ticket->getIssue();
	$description = View::escape($issue->getDescription());

	$department = View::escape($ticket->getData('assignedPerson.department.name'));

	$h = $ticket->getHistory();
	$history = end($h);

	$address = View::escape($ticket->getLocation());
	$zipcode = View::escape($ticket->getZip());

	$media_url = '';
	$media = $issue->getMedia();
	if (count($media)) {
		$m = $media[0];
		$media_url = $m->getURL();
	}

	echo "
	<request>
		<service_request_id>{$ticket->getId()}</service_request_id>
		<status>{$ticket->getStatus()}</status>
		<service_name>$service_name</service_name>
		<service_code>$service_code</service_code>
		<description>$description</description>
		<agency_responsible>$department</agency_responsible>
		<requested_datetime>{$ticket->getEnteredDate('c')}</requested_datetime>
		<updated_datetime>{$history->getEnteredDate('c')}</updated_datetime>
		<address>$address</address>
		<address_id>{$ticket->getAddress_id()}</address_id>
		<zipcode>$zipcode</zipcode>
		<lat>{$ticket->getLatitude()}</lat>
		<long>{$ticket->getLongitude()}</long>
		<media_url>$media_url</media_url>
	</request>
	";
}
echo "</service_requests>";
