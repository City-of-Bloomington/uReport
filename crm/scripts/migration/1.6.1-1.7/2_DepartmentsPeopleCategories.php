<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
require_once './config.inc';

// Departments, People, and Categories foreign key each other
// We'll need to do an initial pass, then add the foreign keys
// Start with Departments adding only basic information
$result = $mongo->departments->find();
foreach ($result as $r) {
	$d = new Department();
	$d->setName($r['name']);
	if (isset($r['customStatuses'])) {
		$d->setCustomStatuses(implode(',',$r['customStatuses']));
	}
	if (isset($r['actions'])) {
		$actions = array();
		foreach ($r['actions'] as $a) {
			$actions[] = $a['name'];
		}
		$d->setActions($actions);
	}
	$d->save();
	echo "Department: {$d->getName()}\n";
}

// Load People records from Mongo
$result = $mongo->people->find();
foreach ($result as $row) {
	$person = new Person();
	$fields = array(
		'firstname', 'lastname', 'middlename', 'email', 'organization',
		'address', 'city', 'state', 'zip',
		'username', 'authenticationMethod', 'role'
	);
	foreach ($fields as $field) {
		if (!empty($row[$field])) {
			$set = 'set'.ucfirst($field);
			$person->$set($row[$field]);
		}
	}
	if (!empty($row['department']['name'])) {
		try {
			$d = new Department($row['department']['name']);
			$person->setDepartment($d);
		}
		catch (Exception $e) {
			// We may have deleted a department that person was a member of
			// The person will just not get put in a department
		}
	}

	if (!empty($row['phone']['number'])) {
		$person->setPhoneNumber($row['phone']['number']);
	}
	if (!empty($row['phone']['device_id'])) {
		$person->setPhoneDeviceId($row['phone']['device_id']);
	}
	$person->save();

	$zend_db->insert('people_crosswalk', array(
		'person_id'=>$person->getId(),
		'mongo_id' =>(string)$row['_id']
	));
	echo "Person: {$person->getFullname()}\n";
}

// Load the Categories
$result = $mongo->categories->find();
foreach ($result as $r) {
	$c = new Category();
	$c->setName($r['name']);
	$c->setDescription($r['description']);
	$c->setDisplayPermissionLevel($r['displayPermissionLevel']);
	$c->setPostingPermissionLevel($r['postingPermissionLevel']);

	$g = new CategoryGroup($r['group']['name']);
	$c->setCategoryGroup($g);

	$d = new Department($r['department']['name']);
	$c->setDepartment($d);

	if (!empty($r['customFields'])) {
		$c->setCustomFields(json_encode($r['customFields']));
	}
	$c->save();
	echo "Category: {$c->getName()}\n";
}

// Now that we've got People and Categories in the database,
// Link the Departments with their Categories and Default Person
$result = $mongo->departments->find();
foreach ($result as $r) {
	$d = new Department($r['name']);
	if (!empty($r['defaultPerson']['_id'])) {
		$id = getPersonIdFromCrosswalk($r['defaultPerson']['_id']);
		$d->setDefaultPerson_id($id);
	}

	if (!empty($r['categories'])) {
		$ids = array();
		foreach ($r['categories'] as $c) {
			$category = new Category($c['name']);
			$ids[] = $category->getId();
		}
		$d->setCategories($ids);
	}
	$d->save();
	echo "Department: Linked defaultPerson and categories\n";
}
