<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param POST issue_id
 * @param POST return_url
 * @param POST attachment
 */
try {
	$issue = new Issue($_POST['issue_id']);
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL);
	exit();
}

if (userIsAllowed('Issues')) {
	try {
		$media = new Media();
		$media->setFile($_FILES['attachment']);
		$media->save();

		$issue->attachMedia($media);
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}
else {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
}


$return_url = isset($_POST['return_url']) ? $_POST['return_url'] : $issue->getCase()->getURL();
header('Location: '.$return_url);