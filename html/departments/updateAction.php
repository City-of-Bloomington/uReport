<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST department_id
 * @param REQUEST index
 */
if (!userIsAllowed('Departments')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

try {
	$department = new Department($_REQUEST['department_id']);

	if (isset($_REQUEST['index']) && preg_match('/[0-9]+/',$_REQUEST['index'])) {
		$index = $_REQUEST['index'];
		$actions = $department->getActions();
		$action = $actions[$index];
	}
	else {
		$index = null;
		$action = array();
	}
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL.'/departments');
	exit();
}

if (isset($_POST['action'])) {
	$department->updateActions($_POST['action'],$index);

	try {
		$department->save();
		header('Location: '.BASE_URL.'/departments/actions.php?department_id='.$department->getId());
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template('two-column');
$template->blocks[] = new Block(
	'departments/updateActionForm.inc',
	array('department'=>$department,'action'=>$action,'index'=>$index)
);
echo $template->render();
