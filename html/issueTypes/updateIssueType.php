<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */

if (!userIsAllowed('IssueTypes')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

// Load the $issueType for editing
if (isset($_REQUEST['id']) && $_REQUEST['id']) {
	try {
		$issueType = new IssueType($_REQUEST['id']);
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
		header('Location: '.BASE_URL.'/issueTypes');
		exit();
	}
}
else {
	$issueType = new IssueType();
}


if (isset($_POST['id'])) {
	$fields = array('name','department_id');
	foreach ($fields as $field) {
		if (isset($_POST[$field])) {
			$set = 'set'.ucfirst($field);
			$issueType->$set($_POST[$field]);
		}
	}

	try {
		$issueType->save();
		header('Location: '.BASE_URL.'/issueTypes');
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template('two-column');
$template->blocks[] = new Block('issueTypes/updateIssueTypeForm.inc',array('issueType'=>$issueType));
echo $template->render();