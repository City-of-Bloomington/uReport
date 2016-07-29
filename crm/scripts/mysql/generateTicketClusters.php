<?php
/**
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
use Application\Models\Ticket;
use Application\Models\GeoCluster;
use Blossom\Classes\Database;

include '../../bootstrap.inc';

$zend_db = Database::getConnection();
$zend_db->query('delete from ticket_geodata')->execute();
$zend_db->query('truncate table geoclusters')->execute();

$sql = "select id from tickets
		where latitude  is not null
		  and longitude is not null";
$results = $zend_db->query($sql)->execute();
$c = count($results);
foreach ($results as $i=>$row) {
	$ticket = new Ticket($row['id']);
	GeoCluster::updateTicketClusters($ticket);
	echo "Ticket: $row[id] $i:$c\n";
}
