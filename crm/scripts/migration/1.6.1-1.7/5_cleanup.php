<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../../../configuration.inc';
$zend_db = Database::getConnection();

$openAction   = new Action('open');
$closedAction = new Action('close');

/**
 * About 270 or so Tickets came across with bad dates.
 *
 * These were tickets that did not have dates from the old ReqPro.
 * When we originally imported them, they received a mix of
 * 1970-01-01 (the original unix timestamp) and CURRENT_TIME
 *
 * We don't know when these things were originally entered, but
 * cannot leave the date as zero.  So, we're setting all dates on
 * these tickets to 1970-01-01.
 */
$sql = "select t.id,t.enteredDate,h.enteredDate,h.actionDate
		from tickets t
		left join ticketHistory h on (t.id=h.ticket_id and h.action_id=?)
		where t.enteredDate='1970-01-01'";
$result = $zend_db->fetchAll($sql, $openAction->getId());
foreach ($result as $row) {
	$zend_db->update(
		'ticketHistory',
		array(
			'enteredDate'=>'1970-01-01',
			'actionDate'=>'1970-01-01',
			'notes'=>'[data cleanup] Dates were missing and have been set to 1970'
		),
		"ticket_id=$row[id]"
	);
	echo "$row[id] set to 1970-01-01\n";
}


/**
 * A ton a records are closed, yet do not have a ticketHistory with a closed action
 * We are relying on the ticketHistory date for Reporting, so we need to have dates set
 * We're going to have to just invent the close date

 * This should use the date of the last TicketHistory, if available,
 * otherwise it will set the close date as the same as the Ticket enteredDate
 */
$sql = "select t.id, t.enteredDate, t.assignedPerson_id
		from tickets t
		left join ticketHistory h on t.id=h.ticket_id and h.action_id=?
		where t.status='closed' and h.actionDate is null";
$result = $zend_db->fetchAll($sql, $closedAction->getId());
foreach ($result as $row) {
	$sql = 'select max(actionDate) from ticketHistory where ticket_id=?';
	$actionDate = $zend_db->fetchOne($sql, array($row['id']));
	$history = array(
		'ticket_id'=>$row['id'],
		'actionPerson_id'=>$row['assignedPerson_id'],
		'action_id'=>$closedAction->getId(),
		'actionDate'=>$actionDate ? $actionDate : $row['enteredDate'],
		'notes'=>'[data cleanup] Closing date was missing and has been provided by the system as a best guess'
	);
	$zend_db->insert('ticketHistory', $history);
	echo "$row[id] $row[enteredDate] $history[actionDate]\n";
}

$q = $zend_db->query('update tickets set latitude=null, longitude=null where latitude=0');
$q->execute();
