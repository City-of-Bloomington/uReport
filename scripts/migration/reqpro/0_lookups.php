<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
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
