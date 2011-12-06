<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
$department = new Department($_GET['department_id']);

$template = isset($_GET['format']) ? new Template('default',$_GET['format']) : new Template('two-column');
$template->blocks[] = new Block('departments/departmentInfo.inc',array('department'=>$department));
echo $template->render();