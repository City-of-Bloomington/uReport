<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Request person_id
 */
if (!userIsAllowed('Users')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

$person = new Person($_REQUEST['person_id']);

if (isset($_POST['person'])) {
	foreach ($_POST['person'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$person->$set($value);
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
