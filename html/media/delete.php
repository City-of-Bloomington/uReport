<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET media_id
 */
try {
	$media = new Media($_GET['media_id']);
	$issue = $media->getIssue();
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL);
	exit();
}

if (userIsAllowed('Issues')) {
	$media->delete();
}
else {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
}

header('Location: '.$issue->getCase()->getURL());