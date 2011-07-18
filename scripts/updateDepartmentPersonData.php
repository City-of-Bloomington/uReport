<?php
/**
 * Goes through all the departments and updates the default Person with their full data
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../configuration.inc';

$list = new DepartmentList();
$list->find();
foreach ($list as $department) {
	$person = new Person($department->getData('defaultPerson._id'));
	$department->setDefaultPerson($person);
	$department->save();
	echo "{$department->getName()} : {$person->getFullname()}\n";
}