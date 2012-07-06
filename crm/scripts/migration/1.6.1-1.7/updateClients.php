<?php
/**
 * Migrates the client_id on tickets
 *
 * After I wrote the migration scripts, I found out I had forgot
 * the client_id field.  This script runs through all the tickets
 * that had clients, and updates the tickets in the new system.
 *
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../../../configuration.inc';

// Connect to the old Mongo database
// Make sure to open up the mongo port (27017) in the firewall
$connection = new Mongo('mongodb://rogue.bloomington.in.gov');
$mongo = $connection->crm;

$result = $mongo->tickets->find(array('client_id'=>array('$exists'=>true)));
foreach ($result as $r) {
	$client = new Client("{$r['client_id']}");
	$ticket = new Ticket($r['number']);
	$ticket->setClient($client);
	$ticket->save();
	echo "{$ticket->getId()}: {$ticket->getClient()->getName()}\n";
}