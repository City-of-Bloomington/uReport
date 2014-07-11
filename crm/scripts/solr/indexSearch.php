<?php
/**
 * @copyright 2012-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Application\Models\Search;
use Application\Models\Ticket;
use Blossom\Classes\Database;

include '../../configuration.inc';
$search = new Search();
$search->solrClient->deleteByQuery('*:*');
$search->solrClient->commit();

$sql = 'select * from tickets';
$zend_db = Database::getConnection();
$result = $zend_db->query($sql)->execute();
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
