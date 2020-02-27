<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Department $this->department
 */
$department = array(
	'id'              =>$this->department->getId(),
	'name'            =>$this->department->getName(),
	'defaultPerson_id'=>$this->department->getDefaultPerson_id()
);
$categories = array();
foreach ($this->department->getCategories() as $id=>$c) {
	$categories[] = array('id'=>$id, 'name'=>$c->getName());
}
$department['categories'] = $categories;

$actions = array();
foreach ($this->department->getActions()    as $id=>$a) {
	$actions[]    = array('id'=>$id, 'name'=>$a->getName());
}
$department['actions'] = $actions;

echo json_encode($department);
