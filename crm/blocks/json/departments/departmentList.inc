<?php
/**
 * @copyright 2011-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param DepartmentList $this->departmentList
 */
$departments = array();
foreach ($this->departmentList as $department) {
	$departments[] = array(
		'id'              => $department->getId(),
		'name'            => $department->getName(),
		'defaultPerson_id'=> $department->getDefaultPerson_id(),
		'categories'      => $department->getCategories(),
		'actions'         => $department->getActions()
	);
}
echo json_encode($departments);
