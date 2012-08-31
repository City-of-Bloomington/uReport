<?php
/**
 * @copyright 2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include './configuration.inc';
$FILE = fopen('./output.txt', 'w');
$header = "Ticket ID|Entered Date|Status|Resolution|Location|Close Date|Person|Private Property|Public Property|Business\n";
fwrite($FILE, $header);

$list = new TicketList(array('category_id'=>37));
foreach ($list as $ticket) {
	$history = $ticket->getHistory();
	$finalHistory = end($history);


	foreach ($ticket->getIssues() as $issue) {
		$customFields = $issue->getCustomFields();
		$line = array(
			$ticket->getId(),
			$ticket->getEnteredDate(DATE_FORMAT),
			$ticket->getStatus(),
			$ticket->getResolution(),
			$ticket->getLocation(),
			$ticket->getStatus()=='closed' ? $finalHistory->getEnteredDate(DATE_FORMAT) : '',
			$ticket->getStatus()=='closed' ? $finalHistory->getEnteredByPerson()->getFullname() : '',
			fieldHasValue($customFields, 'propertytype', 'Private Property'),
			fieldHasValue($customFields, 'propertytype', 'Public Property'),
			fieldHasValue($customFields, 'propertytype', 'Business')
		);
		$line = implode('|', $line);
		fwrite($FILE, $line."\n");
	}
}

function fieldHasValue($o, $field, $value) {
	return (isset($o->$field) && in_array($value, $o->$field));
}