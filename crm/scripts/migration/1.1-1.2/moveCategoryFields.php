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
	if (isset($ticket['issues'][0]['category']['_id'])) {
		try {
			$category = new Category($ticket['issues'][0]['category']['_id']);
			$ticket['category'] = $category->getData();
		}
		catch (Exception $e) {
			// Unknown categories are just going to be lost
			// The only one I know of right now is ENGINEERING
		}
		foreach ($ticket['issues'] as $i=>$issue) {
			if (isset($issue['category'])) {
				unset($issue['category']);
				$ticket['issues'][$i] = $issue;
			}
		}
		$mongo->tickets->save($ticket);
	}
}
