<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Request person_id
 */
if (!userIsAllowed('Users')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

if (isset($_REQUEST['person_id']) && $_REQUEST['person_id']) {
	try {
		$person = new Person($_REQUEST['person_id']);
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
		header('Location: '.BASE_URL.'/people');
		exit();
	}
}
else {
	$person = new Person();
}


if (isset($_POST['firstname'])) {
	$fields = array(
		'firstname','middlename','lastname','email','phone',
		'address','city','state','zip'
	);
	foreach ($fields as $field) {
		if (isset($_POST[$field])) {
			$set = 'set'.ucfirst($field);
			$person->$set($_POST[$field]);
		}
	}

	try {
		$person->save();
		header('Location: '.BASE_URL.'/people');
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->title = 'Update a person';
$template->blocks[] = new Block('people/updatePersonForm.inc',array('person'=>$person));
echo $template->render();
