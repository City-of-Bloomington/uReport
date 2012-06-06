<?php
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
 *
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../../../configuration.inc';

$openAction = new Action('open');
$zend_db = Database::getConnection();
$sql = "select t.id,t.enteredDate,h.enteredDate,h.actionDate
		from tickets t
		left join ticketHistory h on (t.id=h.ticket_id and h.action_id=?)
		where t.enteredDate='1970-01-01'";
$result = $zend_db->fetchAll($sql, $openAction->getId());
foreach ($result as $row) {
	$zend_db->update(
		'ticketHistory',
		array('enteredDate'=>'1970-01-01','actionDate'=>'1970-01-01'),
		"ticket_id=$row[id]"
	);
}
