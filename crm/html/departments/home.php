<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (!userIsAllowed('Departments')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

$departmentList = new DepartmentList();
$departmentList->find();

$template = new Template('two-column');
$template->blocks[] = new Block(
	'departments/departmentList.inc',
	array('departmentList'=>$departmentList)
);
echo $template->render();
