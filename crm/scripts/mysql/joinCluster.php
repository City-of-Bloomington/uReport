<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Quan Zhang <quanzhang@acm.org>
 */
include '../../configuration.inc';

$zend_db = Database::getConnection();

for($i = 0;$i <= 6; $i++) {
	$sql = "UPDATE tickets SET cluster_id_lv$i=NULL";
	echo "$sql\n";
	$zend_db->query($sql);
}

$prevID = 0;
$count = 1;
while(true) {
	$sql = "
	SELECT * FROM tickets
	WHERE latitude  IS NOT NULL
	  AND longitude IS NOT NULL
	  AND id > $prevID
	LIMIT 1
	";
	$query = $zend_db->query($sql);
	$row = $query->fetch();
	if($row) {
		$ticket = new Ticket($row);
		$ticket->setRecalculateClusters(true);
		try {
			$ticket->save();
		}
		catch (Exception $e) {
			die($e->getMessage());
		}
		echo "Count: $count, TicketID: {$ticket->getId()}\n";
		$prevID = $ticket->getId();
		$count ++;
	}
	else {
		break;
	}
}
