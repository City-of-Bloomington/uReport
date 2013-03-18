<?php
/**
 * @copyright 2009-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param PersonList $this->personList
 */
$people = array();
foreach ($this->personList as $person) {
	$p = array(
		'id'          => $person->getId(),
		'username'    => $person->getUsername(),
		'name'        => $person->getFullname(),
		'organization'=> $person->getOrganization(),
		'email'       => $person->getEmail(),
	);
	$list = $person->getPhones();
	if (count($list)) {
		$p['phones'] = array();
		foreach ($list as $phone) {
			$p['phones'][] = $phone->getNumber();
		}
	}
	$people[] = $p;
}
echo json_encode($people);