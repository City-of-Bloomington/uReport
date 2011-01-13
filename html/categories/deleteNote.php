<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST note_id
 */
if (!userIsAllowed('Categories')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

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

$type = $note->getCategory();
$note->delete();

header('Location: '.BASE_URL.'/categories/notes.php?category_id='.$type->getId());
