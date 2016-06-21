<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
include '../bootstrap.inc';

// We want to find all tickets, not just the ones that are public.
// This can only be done with a logged in user, which we don't have
// when this is run from the CRON.
// Instead, we create a Mock user with Admin privileges.
$_SESSION['USER'] = new Person();
$_SESSION['USER']->setRole('Administrator');

$zend_db = Database::getConnection();
$sql = "select distinct assignedPerson_id
		from tickets
		where status='open'";
$ids = $zend_db->fetchCol($sql);
foreach ($ids as $id) {
	$person = new Person($id);

	$tickets = new TicketList();
	$tickets->find(
		array('assignedPerson_id'=>$person->getId(), 'status'=>'open'),
		't.enteredDate'
	);

	$template = new Template('email', 'txt');
	$template->blocks[] = new Block(
		'notifications/digestNotification.inc',
		array('person'=>$person)
	);
	$template->blocks[] = new Block(
		'tickets/ticketList.inc',
		array(
			'ticketList'    => $tickets,
			'title'         => 'Outstanding cases',
			'disableButtons'=> true
		)
	);

	$text = $template->render();

	$count = count($tickets);
	$person->sendNotification($text, "$count open cases in ".APPLICATION_NAME);
}
