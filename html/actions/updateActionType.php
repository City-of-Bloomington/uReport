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
if (isset($_REQUEST['actionType_id']) && $_REQUEST['actionType_id']) {
	try {
		$actionType = new ActionType($_REQUEST['actionType_id']);
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
		header('Location: '.BASE_URL.'/actions');
		exit();
	}
}
else {
	$actionType = new ActionType();
}


if (isset($_POST['name'])) {
	$actionType->setName($_POST['name']);
	$actionType->setVerb($_POST['verb']);

	try {
		$actionType->save();
		header('Location: '.BASE_URL.'/actions');
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template('two-column');
$template->blocks[] = new Block('actions/updateActionTypeForm.inc',array('actionType'=>$actionType));
echo $template->render();