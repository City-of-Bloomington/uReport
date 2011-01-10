<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (!userIsAllowed('Departments')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL.'/departments');
	exit();
}

// Load the department for editing
if (isset($_REQUEST['department_id']) && $_REQUEST['department_id']) {
	try {
		$department = new Department($_REQUEST['department_id']);
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
		header('Location: '.BASE_URL.'/departments');
		exit();
	}
}
else {
	$department = new Department();
}


if (isset($_POST['name'])) {
	$department->setName($_POST['name']);
	$department->setDefault_user_id($_POST['default_user_id']);

	try {
		$department->save();
		header('Location: '.BASE_URL.'/departments');
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template('two-column');
$template->blocks[] = new Block('departments/updateDepartmentForm.inc',array('department'=>$department));
echo $template->render();