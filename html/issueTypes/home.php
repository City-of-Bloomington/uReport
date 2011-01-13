<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (!userIsAllowed('IssueTypes')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL.'/issueTypes');
	exit();
}

$issueTypeList = new IssueTypeList();
$issueTypeList->find();

$template = new Template('two-column');
$template->blocks[] = new Block('issueTypes/issueTypeList.inc',array('issueTypeList'=>$issueTypeList));
echo $template->render();