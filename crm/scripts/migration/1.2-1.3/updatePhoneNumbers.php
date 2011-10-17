<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../../../configuration.inc';
$mongo = Database::getConnection();

echo "Loading....\n";
$people = $mongo->people->find();
$phoneNumbers = array();
foreach ($people as $person) {
	$number = isset($person['phone']) ? $person['phone'] : null;
	$phoneNumbers["{$person['_id']}"] = $number;
}


foreach ($phoneNumbers as $id=>$number) {
	$mongo->people->update(
		array('_id'=>new MongoId($id)),
		array('$set'=>array('phone'=>array('number'=>$number)))
	);
	echo "$number\n";
}
