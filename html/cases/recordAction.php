<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST case_id
 */
// Make sure they're supposed to be here
if (!userIsAllowed('Cases')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

// Load the case
try {
	$case = new Case($_REQUEST['case_id']);
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL);
	exit();
}

// Handle any stuff the user posts
if (isset($_POST['action'])) {
	// add a record to case history
	$history = new History();
	$history->setAction($_POST['action']);
	$history->setActionDate($_POST['actionDate']);
	$history->setEnteredByPerson($_SESSION['USER']);
	$history->setActionPerson($_SESSION['USER']);
	$history->setNotes($_POST['notes']);
	$case->updateHistory($history);

	try {
		$case->save();
		header('Location: '.$case->getURL());
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

header('Location: '.$case->getURL());