<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>, W Sibo <sibow@bloomington.in.gov>
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
else{
	$department = $_SESSION['USER']->getDepatment();
}

if (isset($_POST['department_id'])) {
	$department->setDefault_person_id($_POST['default_person_id']);

	try {
		$department->save();
		$department->saveCategories(array_keys($_POST['categories']));
		$department->saveActions(array_keys($_POST['actions']));

		header('Location: '.BASE_URL.'/departments');
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template('two-column');
$template->blocks[] = new Block('departments/updateMyDepartmentForm.inc',array('department'=>$department));
echo $template->render();
