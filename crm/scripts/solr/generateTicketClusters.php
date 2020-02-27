<?php
/**
 * @copyright 2013-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
use Application\Models\Ticket;
use Application\Models\GeoCluster;
use Application\Database;

include '../../bootstrap.inc';

$db = Database::getConnection();
$db->query('delete from ticket_geodata')->execute();
$db->query('truncate table geoclusters')->execute();

$sql = "select id from tickets
		where latitude  is not null
		  and longitude is not null";
$results = $db->query($sql)->execute();
$c = count($results);
foreach ($results as $i=>$row) {
	$ticket = new Ticket($row['id']);
	GeoCluster::updateTicketClusters($ticket);
	echo "Ticket: $row[id] $i:$c\n";
}
