<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param TicketList $this->ticketList
 */
$service_requests = [];
foreach ($this->ticketList as $t) {
	$c = $t->getCategory();
	$service_name = $c ? $c->getName() : '';
	$service_code = $c ? $c->getId()   : '';

	$agency_responsible = '';
	$p = $t->getAssignedPerson();
	if ($p) {
		$d = $p->getDepartment();
		if ($d) {
			$agency_responsible = $d->getName();
		}
	}

	$sr = [
		'service_request_id'=>$t->getId(),
		'status'            =>$t->getStatus(),
		'service_name'      =>$service_name,
		'service_code'      =>$service_code,
		'agency_responsible'=>$agency_responsible,
		'requested_datetime'=>$t->getEnteredDate ('c'),
		'updated_datetime'  =>$t->getLastModified('c'),
		'expected_datetime' =>$t->getExpectedDate('c')
	];
	if ($t->getLocation())  { $sr['address']    = $t->getLocation();  }
	if ($t->getAddressId()) { $sr['address_id'] = $t->getAddressId(); }
	if ($t->getZip())       { $sr['zipcode']    = $t->getZip();       }
	if ($t->getLatitude())  { $sr['lat']        = $t->getLatitude();  }
	if ($t->getLongitude()) { $sr['long']       = $t->getLongitude(); }

    $sr['description'] = $t->getDescription();

    $media = $t->getMedia();
    if (count($media)) {
        $first = $media->current();
        $sr['media_url'] = $first->getURL();
    }

	$service_requests[] = $sr;
}

echo json_encode($service_requests);
