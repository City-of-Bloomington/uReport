<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */

if (!userIsAllowed('ContactMethods')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

// Load the $contactMethod for editing
if (isset($_REQUEST['id']) && $_REQUEST['id']) {
	try {
		$contactMethod = new ContactMethod($_REQUEST['id']);
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
		header('Location: '.BASE_URL.'/contactMethods');
		exit();
	}
}
else {
	$contactMethod = new ContactMethod();
}


if (isset($_POST['id'])) {
	$fields = array('name');
	foreach ($fields as $field) {
		if (isset($_POST[$field])) {
			$set = 'set'.ucfirst($field);
			$contactMethod->$set($_POST[$field]);
		}
	}

	try {
		$contactMethod->save();
		header('Location: '.BASE_URL.'/contactMethods');
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template('two-column');
$template->blocks[] = new Block('contactMethods/updateContactMethodForm.inc',array('contactMethod'=>$contactMethod));
echo $template->render();