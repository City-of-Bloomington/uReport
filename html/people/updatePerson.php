<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST person_id
 * @param REQUEST return_url (optional)
 */
$errorURL = isset($_REQUEST['return_url']) ? $_REQUEST['return_url'] : BASE_URL.'/people';
$return_url = isset($_REQUEST['return_url'])
	? new URL($_REQUEST['return_url'])
	: new URL(BASE_URL.'/people/viewPerson.php');

if (!userIsAllowed('People')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header("Location: $errorURL");
	exit();
}

if (isset($_REQUEST['person_id']) && $_REQUEST['person_id']) {
	try {
		$person = new Person($_REQUEST['person_id']);
		$return_url->person_id = $person->getId();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
		header("Location: $errorURL");
		exit();
	}
}
else {
	$person = new Person();
}

if (isset($_POST['firstname'])) {
	$fields = array(
		'firstname','middlename','lastname','email','phone','organization',
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
		$return_url->person_id = $person->getId();

		header("Location: $return_url");
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->title = 'Update a person';
$template->blocks[] = new Block(
	'people/updatePersonForm.inc',
	array('person'=>$person,'return_url'=>$return_url)
);
echo $template->render();
