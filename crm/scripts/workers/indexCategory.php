<?php
/**
 * Reindex all tickets for the given category_id
 *
 * @copyright 2013-2015 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param int $argv[2] The category_id to reindex tickets for
 * @param int $argv[1] The path to the SITE_HOME directory
 */
use Application\Models\Search;
use Application\Models\Ticket;
use Blossom\Classes\Database;

if (isset($argv[2]) && is_numeric($argv[2])) {
	$_SERVER['SITE_HOME'] = $argv[1];
	$openFlag = isset($argv[3]) ? $argv[3] : null;

	include_once realpath(__DIR__.'/../../configuration.inc');

    $filename = SITE_HOME.'/workers/indexCategory_'.uniqid();
    $LOG = fopen($filename, 'a');

	$sql = 'select * from tickets where category_id=?';
	if ($openFlag) { $sql.= ' and closedDate is null'; }

	fwrite($LOG, "$sql\n");
	$zend_db = Database::getConnection();
    $result = $zend_db->query($sql)->execute([$argv[2]]);
    $count = count($result);

    $search = new Search();
	foreach ($result as $c=>$row) {
		$ticket = new Ticket($row);
		$search->add($ticket);
		fwrite($LOG, "$c/$count: {$ticket->getId()}\n");
	}
	fwrite($LOG, "Committing\n");
	$search->solrClient->commit();
	fwrite($LOG, "Optimizing\n");
	$search->solrClient->optimize();
	fwrite($LOG, "Done\n");
	fclose($LOG);

	unlink($filename);
}
