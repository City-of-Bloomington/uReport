<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (!userIsAllowed('Actions')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

$actionTypeList = new ActionTypeList();
$actionTypeList->find();

$template = new Template('two-column');
$template->blocks[] = new Block('actions/actionTypeList.inc',array('actionTypeList'=>$actionTypeList));
echo $template->render();