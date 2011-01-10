<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */

$departmentList = new DepartmentList();
$departmentList->find();

$template = new Template();
$template->blocks[] = new Block('departments/departmentList.inc',array('departmentList'=>$departmentList));
echo $template->render();