<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../../configuration.inc';
$search = new Search();

$tickets = new TicketList();
$tickets->find();
$c = 0;
foreach ($tickets as $ticket) {
	$search->add($ticket);
	$c++;
	echo "$c: {$ticket->getId()}\n";
}
echo "Committing\n";
$search->solrClient->commit();
echo "Optimizing\n";
$search->solrClient->optimize();
echo "Done\n";