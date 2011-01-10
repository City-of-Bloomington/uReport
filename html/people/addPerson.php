<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (!userIsAllowed('Users')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

if (isset($_POST['person'])) {
	$person = new Person();
	foreach ($_POST['person'] as $field=>$value) {
		$set = 'set'.ucfirst($field);
		$person->$set($value);
	}

	try {
		$person->save();
		header('Location: '.BASE_URL.'/people');
		exit();
	}
	catch(Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template();
$template->title = 'Add a person';
$template->blocks[] = new Block('people/addPersonForm.inc');
echo $template->render();
