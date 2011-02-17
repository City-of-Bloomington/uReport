<?php
/**
 * @copyright 2009 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param GET person_id
 */
$person = new Person($_GET['person_id']);

$template = new Template('people');
$template->title = $person->getFullname();
$template->blocks['person-panel'][] = new Block('people/personInfo.inc',array('person'=>$person));



$reportedTickets = $person->getReportedTickets();
if (count($reportedTickets)) {
	$template->blocks['ticket-panel'][] = new Block(
		'tickets/searchResults.inc',
		array(
			'ticketList'=>$person->getReportedTickets(),
			'title'=>'Tickets With Issues Reported By '.$person->getFullname()
		)
	);
}

echo $template->render();
