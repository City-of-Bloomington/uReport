<?php
/**
 * @copyright 2012-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../../configuration.inc';
$search = new Search();
$search->solrClient->deleteByQuery('*:*');
$search->solrClient->commit();

$sql = 'select * from tickets';
$zend_db = Database::getConnection();
$query = $zend_db->query($sql);

$c = 0;
while ($row = $query->fetch()) {
	$ticket = new Ticket($row);
	$search->add($ticket);
	$c++;
	echo "$c: {$ticket->getId()}\n";
}
echo "Committing\n";
$search->solrClient->commit();
echo "Optimizing\n";
$search->solrClient->optimize();
echo "Done\n";
