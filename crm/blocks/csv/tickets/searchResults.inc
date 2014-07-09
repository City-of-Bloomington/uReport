<?php
/**
 * Displays a list of Tickets
 *
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 */
use Blossom\Classes\Block;

$ticketList = new TicketList();
$ticketList->findRawData($_GET);

if (count($ticketList)) {
	$header = new Block(
		'tickets/partials/searchResultsColumnHeaders.inc',
		array('ticketList'=>$ticketList)
	);
	echo $header->render('csv',$this->template);

	$body = new Block(
		'tickets/partials/searchResultsRows.inc',
		array('ticketList'=>$ticketList)
	);
	echo $body->render('csv',$this->template);
}
