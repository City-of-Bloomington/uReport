<?php
/**
 * Handles adding or editing CategoryNotes
 *
 * Either a note_id or an category_id is required
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST note_id
 * @param REQUEST category_id
 */

if (!userIsAllowed('Categories')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

// Load the CategoryNote for editing
if (isset($_REQUEST['note_id']) && $_REQUEST['note_id']) {
	try {
		$note = new CategoryNote($_REQUEST['note_id']);
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
		header('Location: '.BASE_URL.'/categories');
		exit();
	}
}
elseif (isset($_REQUEST['category_id']) && $_REQUEST['category_id']) {
	try {
		$note = new CategoryNote();
		$note->setCategory(new Category($_REQUEST['category_id']));
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
		header('Location: '.BASE_URL.'/categories');
		exit();
	}
}
else {
	$_SESSION['errorMessages'][] = new Exception('categories/unknownCategory');
	header('Location: '.BASE_URL.'/categories');
	exit();
}


// Handle POST data
if (isset($_POST['note'])) {
	$note->setNote($_POST['note']);

	try {
		$note->save();
		header('Location: '.BASE_URL.'/categories/notes.php?category_id='.$note->getCategory_id());
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template('two-column');
$template->blocks[] = new Block('categories/updateNoteForm.inc',array('note'=>$note));
echo $template->render();
