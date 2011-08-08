<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../../../configuration.inc';

$mongo = Database::getConnection();
$results = $mongo->tickets->find();
foreach ($results as $ticket) {
	echo "Updating ticket $ticket[_id]\n";
	foreach ($ticket['issues'] as $i=>$issue) {
		if (isset($issue['notes'])) {
			$ticket['issues'][$i]['description'] = $issue['notes'];
			unset($ticket['issues'][$i]['notes']);
		}
	}
	$mongo->tickets->save($ticket);
}
