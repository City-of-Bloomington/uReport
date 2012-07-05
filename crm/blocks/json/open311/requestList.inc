<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TicketList $this->ticketList
 */
if (isset($_GET['page']) || isset($_GET['page_size'])) {
	$page      = !empty($_GET['page'])      ? (int)$_GET['page']      : 0;
	$page_size = !empty($_GET['page_size']) ? (int)$_GET['page_size'] : 50;
	$this->ticketList->setPagination($page_size, $page);
}

$service_requests = array();
foreach ($this->ticketList as $t) {
	$c = $t->getCategory();
	$service_name = $c ? $c->getName() : '';
	$service_code = $c ? $c->getId()   : '';

	$issue = $t->getIssue();
	$description = $issue->getDescription();

	$history = $t->getHistory();
	$h = end($history);

	$media = $issue->getMedia();
	$media_url = count($media) ? $media[0]->getURL() : '';

	$agency_responsible = '';
	$p = $t->getAssignedPerson();
	if ($p) {
		$d = $p->getDepartment();
		if ($d) {
			$agency_responsible = $d->getName();
		}
	}

	$service_requests[] = array(
		'service_request_id'=>$t->getId(),
		'status'            =>$t->getStatus(),
		'service_name'      =>$service_name,
		'service_code'      =>$service_code,
		'description'       =>$description,
		'agency_responsible'=>$agency_responsible,
		'requested_datetime'=>$t->getEnteredDate('c'),
		'updated_datetime'  =>$h->getEnteredDate('c'),
		'address'           =>$t->getLocation(),
		'address_id'        =>$t->getAddressId(),
		'zipcode'           =>$t->getZip(),
		'lat'               =>$t->getLatitude(),
		'long'              =>$t->getLongitude(),
		'media_url'         =>$media_url
	);
}

echo json_encode($service_requests);