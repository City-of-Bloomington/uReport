<?php
/**
 * When we initially migrated data from the old sysytem,
 * there were still a ton of tickets open.  In the old system,
 * people usually never closed anything, they just entered stuff
 * and let it sit.
 *
 * Now that we're displaying reports and stats, these old tickets
 * are skewing the stats.  We need to try and close them out, but
 * still give them some reasonable dates so they don't skew the stats
 *
 * These old tickets do not usually pass validation, so we have to do
 * raw SQL queries to do the work.
 *
 * @copyright 2012-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
include '../bootstrap.inc';
$db = Database::getConnection();
$closedAction = new Action('close');

$cutoff_date = '2009-01-01';

$sql = "select t.id, t.enteredDate, t.assignedPerson_id
		from tickets t
		where t.status='open'
		and t.enteredDate<'$cutoff_date'";
$result = $db->fetchAll($sql);
foreach ($result as $row) {
	$sql = 'select max(actionDate) from ticketHistory where ticket_id=?';
	$actionDate = $db->fetchOne($sql, array($row['id']));
	$history = array(
		'ticket_id'=>$row['id'],
		'actionPerson_id'=>$row['assignedPerson_id'],
		'action_id'=>$closedAction->getId(),
		'actionDate'=>$actionDate ? $actionDate : $row['enteredDate'],
		'notes'=>'[data cleanup] Closed old ticket from ReqPro'
	);
	$db->insert('ticketHistory', $history);
	$db->update('tickets', array('status'=>'closed'), 'id='.$row['id']);

	$ticket = new Ticket($row['id']);
	$search = new Search();
	$search->add($ticket);
	$search->solrClient->commit();

	echo "$row[id] $row[enteredDate] $history[actionDate]\n";
}
