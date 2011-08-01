<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../../../configuration.inc';

$resolutions = array(
	'Resolved'=>'This ticket has been taken care of',
	'Duplicate'=>'This ticket is a duplicate of another ticket',
	'Bogus'=>'This ticket is not actually a problem or has already been taken care of'
);
foreach ($resolutions as $name=>$description) {
	$resolution = new Resolution();
	$resolution->setName($name);
	$resolution->setDescription($description);
	$resolution->save();
	echo "$resolution\n";
}

$actions = array(
	array('name'=>'open','description'=>'Opened by {actionPerson}','type'=>'system'),
	array('name'=>'assignment','description'=>'{enteredByPerson} assigned this case to {actionPerson}','type'=>'system'),
	array('name'=>'close','description'=>'Closed by {actionPerson}','type'=>'system'),
	array('name'=>'referral','description'=>'{enteredByPerson} referred this case to {actionPerson}','type'=>'system'),
	array('name'=>'Inspection','description'=>'{actionPerson} inspected this Location','type'=>'department'),
	array('name'=>'Follow up','description'=>'{actionPerson} followed up on this ticket','type'=>'department')
);
foreach ($actions as $a) {
	$action = new Action();
	$action->setName($a['name']);
	$action->setDescription($a['description']);
	$action->setType($a['type']);
	$action->save();
	echo "{$action->getName()}\n";
}