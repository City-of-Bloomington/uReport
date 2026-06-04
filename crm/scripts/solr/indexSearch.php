<?php
/**
 * @copyright 2012-2021 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE
 */
require_once __DIR__ . '/../../vendor/autoload.php';
require_once __DIR__ . '/../../bootstrap.php';

use Application\Models\Search;
use Application\Models\Ticket;
use Application\Database;

$search = new Search();
$delete = $search->solr->createUpdate();
$delete->addDeleteQuery('*:*');
$delete->addCommit();
$search->solr->update($delete);


$sql    = 'select * from tickets';
$result = Database::query($sql, []);
$count  = count($result);
foreach ($result as $c=>$row) {
    $ticket   = new Ticket($row);
    $update   = $search->solr->createUpdate();
    $document = $search->createDocument($ticket, $update);
    $update->addDocument($document);
    $search->solr->update($update);

    echo "$c/$count: {$ticket->getId()}\n";
}
echo "Committing\n";
$commit = $search->solr->createUpdate();
$commit->addCommit();
$commit->addOptimize();
$search->solr->update($commit);
echo "Done\n";
