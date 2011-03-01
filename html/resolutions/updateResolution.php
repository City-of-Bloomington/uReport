<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param REQUEST resolution_id
 */

if (!userIsAllowed('Resolutions')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL.'/resolutions');
	exit();
}

// Load the $resolution for editing
if (isset($_REQUEST['resolution_id']) && $_REQUEST['resolution_id']) {
	try {
		$resolution = new Resolution($_REQUEST['resolution_id']);
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
		header('Location: '.BASE_URL.'/resolutions');
		exit();
	}
}
else {
	$resolution = new Resolution();
}


if (isset($_POST['name'])) {
	$resolution->setName($_POST['name']);
	$resolution->setDescription($_POST['description']);

	try {
		$resolution->save();
		header('Location: '.BASE_URL.'/resolutions');
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template('two-column');
$template->blocks[] = new Block('resolutions/updateResolutionForm.inc',array('resolution'=>$resolution));
echo $template->render();