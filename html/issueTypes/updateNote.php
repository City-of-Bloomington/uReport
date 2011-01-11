<?php
/**
 * Handles adding or editing IssueTypeNotes
 *
 * Either a note_id or an issueType_id is required
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST note_id
 * @param REQUEST issueType_id
 */

if (!userIsAllowed('IssueTypes')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

// Load the IssueTypeNote for editing
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
elseif (isset($_REQUEST['issueType_id']) && $_REQUEST['issueType_id']) {
	try {
		$note = new IssueTypeNote();
		$note->setIssueType(new IssueType($_REQUEST['issueType_id']));
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
		header('Location: '.BASE_URL.'/issueTypes');
		exit();
	}
}
else {
	$_SESSION['errorMessages'][] = new Exception('issueTypes/unknownIssueType');
	header('Location: '.BASE_URL.'/issueTypes');
	exit();
}


// Handle POST data
if (isset($_POST['note'])) {
	$note->setNote($_POST['note']);

	try {
		$note->save();
		header('Location: '.BASE_URL.'/issueTypes/notes.php?issueType_id='.$note->getIssueType_id());
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template('two-column');
$template->blocks[] = new Block('issueTypes/updateNoteForm.inc',array('note'=>$note));
echo $template->render();
