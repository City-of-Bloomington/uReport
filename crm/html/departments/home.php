<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$departmentList = new DepartmentList();
$departmentList->find();

$template = !empty($_GET['format']) ? new Template('default',$_GET['format']) : new Template('two-column');
$template->blocks[] = new Block(
	'departments/departmentList.inc',
	array('departmentList'=>$departmentList)
);
echo $template->render();
