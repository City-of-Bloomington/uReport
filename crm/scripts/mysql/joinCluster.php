<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Quan Zhang <quanzhang@acm.org>
 */
include '../../configuration.inc';

$zend_db = Database::getConnection();

for($i = 0;$i <= 6; $i++) {
	$zend_db->query("update tickets set cluster_id_lv$i=NULL");
}

$prevID = 0;
$count = 1;
while(true) {
	$sql = "
	SELECT * FROM tickets
	WHERE
		latitude IS NOT NULL AND
		longitude IS NOT NULL AND
		id > $prevID
	LIMIT 1
	";
	$query = $zend_db->query($sql);
	$row = $query->fetch();
	if($row) {
		$ticket = new Ticket($row);
		echo "Count: $count, TicketID: {$ticket->getId()}\n";
		$ticket->assignClusterIds();
		$prevID = $ticket->getId();
		$count ++;
	}
	else {
		break;
	}
}
