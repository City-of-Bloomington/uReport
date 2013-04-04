<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param TicketList $this->ticketList
 *
 * @param string $this->title
 * @param bool $this->disableButtons
 * @param Ticket $this->filterTicket A ticket to leave out of the display
 * @param string $this->moreLink
 */
echo $this->title ? $this->title : 'Cases';
echo "\n---------------------------------------------------------------------\n";
foreach ($this->ticketList as $ticket) {
	if ($this->filterTicket
		&& "{$this->filterTicket->getId()}"=="{$ticket->getId()}") {
		continue;
	}
	$status      = $ticket->getStatus();
	$location    = $ticket->getLocation();
	$enteredDate = $ticket->getEnteredDate(DATE_FORMAT);
	$sla         = $ticket->getSlaPercentage();
	$sla = $sla ? "$sla%" : '';
	$sla = str_pad($sla,             4, ' ', STR_PAD_RIGHT);

	$category = $ticket->getCategory_id()
		? $ticket->getCategory()->getName()
		: '';
	echo "$status $enteredDate $sla $category $location\n";
}