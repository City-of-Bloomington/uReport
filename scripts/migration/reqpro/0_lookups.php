<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../../../configuration.inc';

$resolutions = array(
	'Resolved'=>'This case has been taken care of',
	'Duplicate'=>'This case is a duplicate of another case',
	'Bogus'=>'This case is not actually a problem or has already been taken care of'
);
foreach ($resolutions as $name=>$description) {
	$resolution = new Resolution();
	$resolution->setName($name);
	$resolution->setDescription($description);
	$resolution->save();
	echo "$resolution\n";
}

#$issueTypes = array('Request','Complaint','Violation');
#foreach ($issueTypes as $name) {
#	$type = new IssueType();
#	$type->setName($name);
#	$type->save();
#	echo "$type\n";
#}

#$actions = array(
#	array('name'=>'inspection','description'=>'{actionPerson} inspected this Location','formLabel'=>'inspected by'),
#	array('name'=>'followup','description'=>'{actionPerson} followed up on this case','formLabel'=>'followed up')
#);
#foreach ($actions as $a) {
#	$action = new Action();
#	$action->setName($a['name']);
#	$action->setDescription($a['description']);
#	$action->setFormLabel($a['formLabel']);
#	$action->save();
#	echo "{$action->getName()}\n";
#}