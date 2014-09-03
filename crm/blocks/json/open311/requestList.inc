<?php
/**
 * @copyright 2011-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TicketList $this->ticketList
 */
$service_requests = array();
foreach ($this->ticketList as $t) {
	$c = $t->getCategory();
	$service_name = $c ? $c->getName() : '';
	$service_code = $c ? $c->getId()   : '';

	$history = $t->getHistory();
	$h = end($history);

	$agency_responsible = '';
	$p = $t->getAssignedPerson();
	if ($p) {
		$d = $p->getDepartment();
		if ($d) {
			$agency_responsible = $d->getName();
		}
	}

	$sr = array(
		'service_request_id'=>$t->getId(),
		'status'            =>$t->getStatus(),
		'service_name'      =>$service_name,
		'service_code'      =>$service_code,
		'agency_responsible'=>$agency_responsible,
		'requested_datetime'=>$t->getEnteredDate('c'),
		'updated_datetime'  =>$h->getEnteredDate('c')
	);
	if ($t->getLocation())  { $sr['address']    = $t->getLocation();  }
	if ($t->getAddressId()) { $sr['address_id'] = $t->getAddressId(); }
	if ($t->getZip())       { $sr['zipcode']    = $t->getZip();       }
	if ($t->getLatitude())  { $sr['lat']        = $t->getLatitude();  }
	if ($t->getLongitude()) { $sr['long']       = $t->getLongitude(); }

	$issue = $t->getIssue();
	if ($issue) {
		$sr['description'] = $issue->getDescription();

		$media = $issue->getMedia();
		if (count($media)) {
            $first = $media->current();
			$sr['media_url'] = $first->getURL();
		}
	}

	$service_requests[] = $sr;
}

echo json_encode($service_requests);
