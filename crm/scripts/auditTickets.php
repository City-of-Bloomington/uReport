<?php
/**
 * This runs through all the tickets and calls the validation function
 * for each one.  Any bad tickets will be reported.
 *
 * @copyright 2013-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 */
include '../bootstrap.inc';

$sql = 'select * from tickets';
$zend_db = Database::getConnection();
$query = $zend_db->query($sql);
while ($row = $query->fetch()) {
	$ticket = new Ticket($row);
	try {
		$ticket->validate();
	}
	catch (Exception $e) {
		echo "Ticket {$ticket->getId()} fails validation: {$e->getMessage()}\n";
	}
}
