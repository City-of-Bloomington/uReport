<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
if (!userIsAllowed('NeighborhoodAssociations')) {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
	header('Location: '.BASE_URL);
	exit();
}

// Load the $neighborhoodAssociation for editing
if (isset($_REQUEST['id']) && $_REQUEST['id']) {
	try {
		$neighborhoodAssociation = new NeighborhoodAssociation($_REQUEST['id']);
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
		header('Location: '.BASE_URL.'/neighborhoodAssociations');
		exit();
	}
}
else {
	$neighborhoodAssociation = new NeighborhoodAssociation();
}


if (isset($_POST['id'])) {
	$fields = array('name');
	foreach ($fields as $field) {
		if (isset($_POST[$field])) {
			$set = 'set'.ucfirst($field);
			$neighborhoodAssociation->$set($_POST[$field]);
		}
	}

	try {
		$neighborhoodAssociation->save();
		header('Location: '.BASE_URL.'/neighborhoodAssociations');
		exit();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}

$template = new Template('two-column');
$template->blocks[] = new Block('neighborhoodAssociations/updateNeighborhoodAssociationForm.inc',
								array('neighborhoodAssociation'=>$neighborhoodAssociation));
echo $template->render();