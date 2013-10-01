<?php
/**
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Quan Zhang <quanzhang@acm.org>
 */
include '../../../configuration.inc';

$zend_db = Database::getConnection();
$zend_db->delete('ticket_geodata');
$zend_db->query('truncate table geoclusters');

$sql = "select id from tickets
		where latitude is not null
		  and longitude is not null";
$results = $zend_db->fetchAll($sql);
$c = count($results);
foreach ($results as $i=>$row) {
	$ticket = new Ticket($row['id']);
	GeoCluster::updateTicketClusters($ticket);
	echo "Ticket: $row[id] $i:$c\n";
}
