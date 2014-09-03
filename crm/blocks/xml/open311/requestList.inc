<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TicketList $this->ticketList
 */
use Blossom\Classes\View;

echo "<service_requests>";
foreach ($this->ticketList as $t) {
	$c = $t->getCategory();
	$service_name = $c ? View::escape($c->getName()) : '';
	$service_code = $c ? $c->getId()   : '';

	$history = $t->getHistory();
	$h = end($history);

	$agency_responsible = '';
	$p = $t->getAssignedPerson();
	if ($p) {
		$d = $p->getDepartment();
		if ($d) {
			$agency_responsible = View::escape($d->getName());
		}
	}

	echo "
	<request>
		<service_request_id>{$t->getId()}</service_request_id>
		<status>{$t->getStatus()}</status>
		<service_name>$service_name</service_name>
		<service_code>$service_code</service_code>
		<agency_responsible>$agency_responsible</agency_responsible>
		<requested_datetime>{$t->getEnteredDate('c')}</requested_datetime>
		<updated_datetime>{$h->getEnteredDate('c')}</updated_datetime>
	";
	if ($t->getLocation())  { echo '<address>'   .View::escape($t->getLocation()) .'</address>';    }
	if ($t->getAddressId()) { echo '<address_id>'.View::escape($t->getAddressId()).'</address_id>'; }
	if ($t->getZip())       { echo '<zipcode>'   .View::escape($t->getZip())      .'</zipcode>';    }
	if ($t->getLatitude())  { echo '<lat>'       .View::escape($t->getLatitude()) .'</lat>';        }
	if ($t->getLongitude()) { echo '<long>'      .View::escape($t->getLongitude()).'</long>';       }

	$issue = $t->getIssue();
	if ($issue) {
		echo '<description>'.View::escape($issue->getDescription()).'</description>';

		$media = $issue->getMedia();
		if (count($media)) {
            $first = $media->current();
			echo "<media_url>{$first->getURL()}</media_url>";
		}
	}
	echo "</request>";
}
echo "</service_requests>";
