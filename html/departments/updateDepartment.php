<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (!userIsAllowed('Departments')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
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


$return_url = isset($_REQUEST['return_url']) ? $_REQUEST['return_url'] : BASE_URL.'/departments';

if (isset($_POST['name'])) {
	$department->setName($_POST['name']);
	$department->setCustomStatuses($_POST['customStatuses']);
	try {
		$department->setDefaultPerson($_POST['defaultPerson']);
		$department->setCategories(array_keys($_POST['categories']));
		
		$department->save();
		header('Location: '.$return_url);
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template('two-column');
$template->blocks[] = new Block(
	'departments/updateDepartmentForm.inc',
	array('department'=>$department,'return_url'=>$return_url)
);
echo $template->render();
