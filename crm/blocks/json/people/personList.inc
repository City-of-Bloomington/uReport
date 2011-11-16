<?php
/**
 * @copyright 2009-2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param PersonList $this->personList
 */
$people = array();
foreach ($this->personList as $person) {
	$people[] = array(
		'id'=>"{$person->getId()}",
		'username'=>$person->getUsername(),
		'name'=>$person->getFullname(),
		'organization'=>$person->getOrganization(),
		'email'=>$person->getEmail(),
	);
}
echo json_encode($people);