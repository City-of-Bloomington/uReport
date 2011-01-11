<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST note_id
 */
if (!userIsAllowed('IssueTypes')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

if (isset($_REQUEST['note_id']) && $_REQUEST['note_id']) {
	try {
		$note = new IssueTypeNote($_REQUEST['note_id']);
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
		header('Location: '.BASE_URL.'/issueTypes');
		exit();
	}
}

$type = $note->getIssueType();
$note->delete();

header('Location: '.BASE_URL.'/issueTypes/notes.php?issueType_id='.$type->getId());
