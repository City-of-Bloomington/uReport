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
$status = $this->ticket->getStatus();
if ($this->ticket->getResolution()) {
	$status.= ': '.$this->ticket->getResolution();
}

if (userIsAllowed('Tickets')) {
	$assignButton = $this->template->linkButton(
		'Assign',
		BASE_URL.'/tickets/assignTicket.php?ticket_id='.$this->ticket->getId(),
		'edit'
	);
	$changeStatusButton = $this->template->linkButton(
		'Change Status',
		BASE_URL.'/tickets/changeStatus.php?ticket_id='.$this->ticket->getId(),
		'edit'
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
			<td>$changeStatusButton</td>
		</tr>
		<tr><th>Assigned To:</th>
			<td>$assigned</td>
			<td>$assignButton</td>
		</tr>
		<tr><th>Received By</th>
			<td>$enteredBy</td>
		</tr>
	</table>
</div>
";
