<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
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
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL.'/departments');
	exit();
}


if (isset($_REQUEST['index'])) {
	$department->removeAction($_REQUEST['index']);
}
try {
	$department->save();
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
}

header('Location: '.BASE_URL."/departments/actions.php?department_id={$department->getId()}");
