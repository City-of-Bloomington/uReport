<?php
/**
 * Goes though all possible Person fields for Tickets and updates them with fresh data
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../configuration.inc';

$personFields = array(
	'enteredByPerson',
	'assignedPerson',
	'referredPerson',
	'history.enteredByPerson',
	'history.actionPerson',
	'issues.enteredByPerson',
	'issues.reportedByPerson',
	'issues.responses.person'
);
$idsToUpdate = array();
foreach ($personFields as $field) {
	echo "Loading $field\n";
	foreach (Ticket::getDistinct("$field._id") as $id) {
		$id = "$id";
		if (!in_array($id,$idsToUpdate)) {
			$idsToUpdate[] = $id;
		}
	}
}

foreach ($idsToUpdate as $id) {
	$person = new Person($id);
	echo "Updating {$person->getFullname()}\n";
	$person->updatePersonInTicketData();
}