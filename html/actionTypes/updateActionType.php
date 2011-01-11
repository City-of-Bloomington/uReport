<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */

if (!userIsAllowed('ActionTypes')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

// Load the $actionType for editing
if (isset($_REQUEST['id']) && $_REQUEST['id']) {
	try {
		$actionType = new ActionType($_REQUEST['id']);
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
		header('Location: '.BASE_URL.'/actionTypes');
		exit();
	}
}
else {
	$actionType = new ActionType();
}


if (isset($_POST['id'])) {
	$fields = array('name');
	foreach ($fields as $field) {
		if (isset($_POST[$field])) {
			$set = 'set'.ucfirst($field);
			$actionType->$set($_POST[$field]);
		}
	}

	try {
		$actionType->save();
		header('Location: '.BASE_URL.'/actionTypes');
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template('two-column');
$template->blocks[] = new Block('actionTypes/updateActionTypeForm.inc',array('actionType'=>$actionType));
echo $template->render();