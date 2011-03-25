<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */

if (!userIsAllowed('Actions')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

// Load the $action for editing
if (isset($_REQUEST['action_id']) && $_REQUEST['action_id']) {
	try {
		$action = new Action($_REQUEST['action_id']);
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
		header('Location: '.BASE_URL.'/actions');
		exit();
	}
}
else {
	$action = new Action();
}


if (isset($_POST['name'])) {
	$action->setName($_POST['name']);
	$action->setDescription($_POST['description']);
	$action->setFormLabel($_POST['formLabel']);

	try {
		$action->save();
		header('Location: '.BASE_URL.'/actions');
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template('two-column');
$template->blocks[] = new Block('actions/updateActionForm.inc',array('action'=>$action));
echo $template->render();