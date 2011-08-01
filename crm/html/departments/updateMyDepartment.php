<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @author W Sibo <sibow@bloomington.in.gov>
 */
$return_url = BASE_URL.'/admin';

if (!isset($_SESSION['USER'])) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

// Load the User's department
$department = $_SESSION['USER']->getDepartment();
if ($department) {
	try {
		$department = new Department($department['_id']);
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
		header('Location: '.$return_url);
		exit();
	}
}
else {
	$_SESSION['errorMessages'][] = new Exception('departments/unknownDepartment');
	header('Location: '.$return_url);
	exit();
}

// Handle any data they post
if (isset($_POST['name'])) {
	$department->setName($_POST['name']);
	$department->setCustomStatuses($_POST['customStatuses']);
	try {
		if ($_POST['defaultPerson']) {
			$department->setDefaultPerson($_POST['defaultPerson']);
		}
		$categories = isset($_POST['categories']) ? array_keys($_POST['categories']) : array();
		$actions = isset($_POST['actions']) ? array_keys($_POST['actions']) : array();

		$department->setCategories($categories);
		$department->setActions($actions);

		$department->save();
		header('Location: '.$return_url);
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

// Display the form
$template = new Template('two-column');
$template->blocks[] = new Block(
	'departments/updateDepartmentForm.inc',
	array('department'=>$department,'return_url'=>$return_url)
);
echo $template->render();
