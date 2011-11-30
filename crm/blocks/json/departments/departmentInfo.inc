<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Department $this->department
 */
$department = array(
	'id'=>"{$this->department->getId()}",
	'name'=>$this->department->getName(),
	'defaultPerson'=>$this->department->getData('defaultPerson'),
	'categories'=>$this->department->getCategories(),
	'actions'=>$this->department->getActions(),
	'customStatuses'=>$this->department->getCustomStatuses()
);

echo json_encode($department);