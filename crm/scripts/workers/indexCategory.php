<?php
/**
 * Reindex all tickets for the given category_id
 *
 * @copyright 2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param int $argv[1] The category_id to reindex tickets for
 */
if (isset($argv[1]) && is_numeric($argv[1])) {
	include __DIR__.'/../../configuration.inc';
	$search = new Search();

	$sql = 'select * from tickets where category_id=?';
	$zend_db = Database::getConnection();
	$query = $zend_db->query($sql, array($argv[1]));

	$filename = APPLICATION_HOME.'/data/workers/indexCategory_'.uniqid();
	$LOG = fopen($filename, 'a');

	$c = 0;
	while ($row = $query->fetch()) {
		$ticket = new Ticket($row);
		$search->add($ticket);
		$c++;
		fwrite($LOG, "$c: {$ticket->getId()}\n");;
	}
	fwrite($LOG, "Committing\n");
	$search->solrClient->commit();
	fwrite($LOG, "Optimizing\n");
	$search->solrClient->optimize();
	fclose($LOG);

	unlink($filename);
}
