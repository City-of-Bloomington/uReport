<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
$person = array(
	'id'          => $this->person->getId(),
	'username'    => $this->person->getUsername(),
	'fullname'    => $this->person->getFullname(),
	'organization'=> $this->person->getOrganization()
);
$list = $this->person->getEmails();
if (count($list)) {
	$person['emails'] = array();
	foreach ($list as $email) {
		$person['emails'][] = $email->getEmail();
	}
}
$list = $this->person->getPhones();
if (count($list)) {
	$person['phones'] = array();
	foreach ($list as $phone) {
		$person['phones'][] = $phone->getNumber();
	}
}
$list = $this->person->getAddresses();
if (count($list)) {
	$person['addresses'] = array();
	foreach ($list as $address) {
		$person['addresses'][] = array(
			'address'=> $address->getAddress(),
			'city'   => $address->getCity(),
			'state'  => $address->getState(),
			'zip'    => $address->getZip()
		);
	}
}
echo json_encode($person);