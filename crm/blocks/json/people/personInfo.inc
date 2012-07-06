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
	'phone'       => $this->person->getPhoneNumber(),
	'email'       => $this->person->getEmail(),
	'organization'=> $this->person->getOrganization(),
	'address'     => $this->person->getAddress(),
	'city'        => $this->person->getCity(),
	'state'       => $this->person->getState(),
	'zip'         => $this->person->getZip()
);
echo json_encode($person);