<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param POST ticket_id
 * @param POST index
 * @param POST attachment
 * @param POST return_url
 */
try {
	$ticket = new Ticket($_POST['ticket_id']);
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL);
	exit();
}

if (userIsAllowed('Tickets')) {
	try {
		$ticket->attachMedia($_FILES['attachment'],$_POST['index']);
		$ticket->save();
	}
	catch (Exception $e) {
		$_SESSION['errorMessages'][] = $e;
	}
}
else {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
}


$return_url = isset($_POST['return_url']) ? $_POST['return_url'] : $ticket->getURL();
header('Location: '.$return_url);
