<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../../../configuration.inc';
include './migrationConfig.inc';

$lookup = array(
	'Housing Authority'=>'simsd',
	'Telecommunications Council'=>'dietzr',
	'Citizen Services Coordinator'=>'deand',
	'Citizen Action Administrator'=>'deand'
);

$pdo = new PDO(MIGRATION_DSN,MIGRATION_USER,MIGRATION_PASS);

// Grab all the departments
$result = $pdo->query('select * from departments where dept_no!=0');
foreach ($result->fetchAll(PDO::FETCH_ASSOC) as $row) {
	unset($user);

	// Make sure we have at least one user for the department
	$query = $pdo->prepare('select userid from complain_authorized where dept=? order by role desc');
	$query->execute(array($row['dept_no']));
	$r = $query->fetch(PDO::FETCH_ASSOC);
	if ($r) {
		$person = new Person($r['userid']);
	}
	elseif (array_key_exists($row['dept_name'],$lookup)) {
		$person = new Person($lookup[$row['dept_name']]);
	}

	if (isset($person)) {
		$department = new Department();
		$department->setName($row['dept_name']);
		$department->setDefaultPerson($person);

		$query->closeCursor();

		// Load the Department's commonly used categories
		$sql = "select distinct c.comp_desc
				from ce_eng_comp a,c_types c
				where a.c_type=c.c_type1 and a.dept=?
				and a.c_type is not null
				and a.c_type!=0
				order by c.comp_desc";
		$query = $pdo->prepare($sql);
		$query->execute(array($row['dept_no']));
		$department->setCategories($query->fetchAll(PDO::FETCH_COLUMN));

		try {
			$department->save();
			echo $department->getName()."\n";
		}
		catch (Exception $e) {
			print_r($e);
			print_r($department);
			print_r($person);
			exit();
		}
	}
}

// Assign all the users to their departments
$sql = "select u.userid,d.dept_name
		from complain_authorized u,departments d
		where u.dept=d.dept_no and u.userid=?";
$query = $pdo->prepare($sql);

$people = new PersonList(array('username'=>array('$exists'=>true)));
foreach ($people as $person) {
	$query->execute(array($person->getUsername()));
	$row = $query->fetch(PDO::FETCH_ASSOC);
	if ($row) {
		try {
			$person->setDepartment($row['dept_name']);
			$person->save();
		}
		catch (Exception $e) {
			// Just skip the bad ones
		}
	}
	$query->closeCursor();

	$department = $person->getDepartment();
	echo "{$person->getUsername()} {$department['name']}\n";
}
