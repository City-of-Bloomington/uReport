<?php
/**
 * @copyright 2012-2020 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
use Application\Models\Search;
use Application\Models\Ticket;
use Application\Database;

include '../../bootstrap.inc';
$search = new Search();
$search->solrClient->deleteByQuery('*:*');
$search->solrClient->commit();

$sql = 'select * from tickets';
$db = Database::getConnection();
$result = $db->query($sql)->execute();
$count = count($result);
foreach ($result as $c=>$row) {
	$ticket = new Ticket($row);
	$search->add($ticket);
	echo "$c/$count: {$ticket->getId()}\n";
}
echo "Committing\n";
$search->solrClient->commit();
echo "Optimizing\n";
$search->solrClient->optimize();
echo "Done\n";
