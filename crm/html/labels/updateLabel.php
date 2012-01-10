<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (!userIsAllowed('Labels')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

// Load the $client for editing
if (isset($_REQUEST['label_id']) && $_REQUEST['label_id']) {
	try {
		$label = new Label($_REQUEST['label_id']);
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
		header('Location: '.BASE_URL.'/labels');
		exit();
	}
}
else {
	$label = new Label();
}

if (isset($_POST['name'])) {
	$label->set($_POST);
	try {
		$label->save();
		header('Location: '.BASE_URL.'/labels');
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template('two-column');
$template->blocks[] = new Block('labels/updateLabelForm.inc',array('label'=>$label));
echo $template->render();