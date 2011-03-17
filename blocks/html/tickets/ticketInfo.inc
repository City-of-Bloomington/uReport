<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 */
$location = View::escape($this->ticket->getLocation());
$assigned = $this->ticket->getAssignedPerson() ? View::escape($this->ticket->getAssignedPerson()->getFullname()) : '';
$enteredBy = $this->ticket->getEnteredByPerson() ? View::escape($this->ticket->getEnteredByPerson()->getFullname()) : '';
$referred = $this->ticket->getReferredPerson() ? View::escape($this->ticket->getReferredPerson()->getFullname()) : '';

$status = $this->ticket->getStatus();
if ($this->ticket->getResolution()) {
	$status.= ': '.$this->ticket->getResolution();
}

$assignButton = '';
$referralButton = '';
$closeTicketButton = '';
if (userIsAllowed('Tickets') && $this->ticket->getStatus()!='closed') {
	$assignButton = $this->template->linkButton(
		'Assign',
		BASE_URL.'/tickets/assignTicket.php?ticket_id='.$this->ticket->getId(),
		'edit'
	);

	$return_url = new URL(BASE_URL.'/tickets/referTicket.php?ticket_id='.$this->ticket->getId());
	$referralButton = $this->template->linkButton(
		'Refer',
		BASE_URL."/people?return_url=$return_url",
		'edit'
	);
	$closeTicketButton = $this->template->linkButton(
		'Close Ticket',
		BASE_URL.'/tickets/closeTicket.php?ticket_id='.$this->ticket->getId(),
		'delete'
	);
}
echo "
<div class=\"ticketInfo\">
	<h1>Ticket #{$this->ticket->getId()}</h1>
	<div>$location</div>
	<table>
		<tr><th>Date Opened</th>
			<td>{$this->ticket->getEnteredDate()}</td>
			<td></td>
		</tr>
		<tr><th>Status</th>
			<td>$status</td>
			<td>$closeTicketButton</td>
		</tr>
		<tr><th>Assigned To:</th>
			<td>$assigned</td>
			<td>$assignButton</td>
		</tr>
		<tr><th>Referred To:</th>
			<td>$referred</td>
			<td>$referralButton</td>
		</tr>
		<tr><th>Received By</th>
			<td>$enteredBy</td>
		</tr>
	</table>
</div>
";
