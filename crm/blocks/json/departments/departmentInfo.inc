<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Department $this->department
 */
$department = array(
	'id'              =>$this->department->getId(),
	'name'            =>$this->department->getName(),
	'defaultPerson_id'=>$this->department->getDefaultPerson_id(),
	'categories'      =>$this->department->getCategories(),
	'actions'         =>$this->department->getActions(),
	'customStatuses'  =>$this->department->getCustomStatuses()
);

echo json_encode($department);