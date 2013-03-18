<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
$person = array(
	'id'          => $this->person->getId(),
	'username'    => $this->person->getUsername(),
	'fullname'    => $this->person->getFullname(),
	'email'       => $this->person->getEmail(),
	'organization'=> $this->person->getOrganization(),
	'address'     => $this->person->getAddress(),
	'city'        => $this->person->getCity(),
	'state'       => $this->person->getState(),
	'zip'         => $this->person->getZip()
);
$list = $person->getPhones();
if (count($list)) {
	$person['phones'] = array();
	foreach ($list as $phone) {
		$person['phones'][] = $phone->getNumber();
	}
}
echo json_encode($person);