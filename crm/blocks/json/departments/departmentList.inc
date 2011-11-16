<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param DepartmentList $this->departmentList
 */
$departments = array();
foreach ($this->departmentList as $department) {

	$defaultPerson = $department->getDefaultPerson();
	if ($defaultPerson) {
		$defaultPerson = $defaultPerson->getFullname();
	}

	$departments[] = array(
		'id'=>"{$department->getId()}",
		'name'=>$department->getName(),
		'defaultPerson'=>$defaultPerson,
		'categories'=>$department->getCategories(),
		'actions'=>$department->getActions(),
		'customStatuses'=>$department->getCustomStatuses()
	);
}
echo json_encode($departments);