<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
include '../../../configuration.inc';
$mongo = Database::getConnection();

$ticketList = new TicketList();
$ticketList->findByMongoQuery(array('issues[0].date'=>array('$ne'=>'history[0].actionDate')));

foreach ($ticketList as $ticket) {
	$ticketModified = false;
	$issue = $ticket->getIssue();
	if ($issue) {
		$issueDate = $issue->getDate('Y-m-d');


		$history = $ticket->getHistory();
		echo "Ticket {$ticket->getId()} $issueDate (";

		foreach ($history as $index=>$action) {
			$actionDate = $action->getActionDate('Y-m-d');
			if ($actionDate == '2011-06-28') {
				$action->setActionDate($issueDate);
				$ticket->updateHistory($action,$index);
				$ticketModified = true;
			}
		}

		$dates = array();
		$history = $ticket->getHistory();
		foreach ($history as $action) {
			$dates[] = "{$action->getActionDate('Y-m-d')}";
		}
		echo implode(',',$dates).")";

		if ($ticketModified) {
			// Do a Mongo Save, bypassing any ticket validation
			$mongo->tickets->save($ticket->getData(),array('safe'=>true));
			echo " Ticket Updated";
		}
		echo "\n";
	}
}
