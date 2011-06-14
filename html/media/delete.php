<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET ticket_id
 * @param GET issueIndex
 * @param GET mediaIndex
 */
try {
	$ticket = new Ticket($_GET['ticket_id']);
}
catch (Exception $e) {
	$_SESSION['errorMessages'][] = $e;
	header('Location: '.BASE_URL);
	exit();
}

if (userIsAllowed('Tickets')) {
	$ticket->deleteMedia($_GET['issueIndex'],$_GET['mediaIndex']);
}
else {
	$_SESSION['errorMessages'][] = new Exception('noAccessAllowed');
}

header('Location: '.$ticket->getURL());
