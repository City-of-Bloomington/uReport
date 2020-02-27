<?php
/**
 * @copyright 2011-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @param TicketList $this->ticketList
 */
echo "<service_requests>";
foreach ($this->ticketList as $t) {
	$c = $t->getCategory();
	$service_name = $c ? self::escape($c->getName()) : '';
	$service_code = $c ? $c->getId()   : '';

	$agency_responsible = '';
	$p = $t->getAssignedPerson();
	if ($p) {
		$d = $p->getDepartment();
		if ($d) {
			$agency_responsible = self::escape($d->getName());
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
		<updated_datetime>{$t->getLastModified('c')}</updated_datetime>
	";
	if ($t->getLocation())  { echo '<address>'   .self::escape($t->getLocation()) .'</address>';    }
	if ($t->getAddressId()) { echo '<address_id>'.self::escape($t->getAddressId()).'</address_id>'; }
	if ($t->getZip())       { echo '<zipcode>'   .self::escape($t->getZip())      .'</zipcode>';    }
	if ($t->getLatitude())  { echo '<lat>'       .self::escape($t->getLatitude()) .'</lat>';        }
	if ($t->getLongitude()) { echo '<long>'      .self::escape($t->getLongitude()).'</long>';       }

    echo '<description>'.self::escape($t->getDescription()).'</description>';

    $media = $t->getMedia();
    if (count($media)) {
        $first = $media->current();
        echo "<media_url>{$first->getURL()}</media_url>";
    }
	echo "</request>";
}
echo "</service_requests>";
