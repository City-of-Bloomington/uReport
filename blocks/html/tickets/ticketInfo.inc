<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 * @param bool $this->disableButtons
 */
$location = View::escape($this->ticket->getLocation());
$location = $location ? "<a href=\"".BASE_URL."/locations/viewLocation.php?location=$location\">$location</a>" : '';

$url = new URL(BASE_URL.'/people/viewPerson.php');

$assigned = View::escape($this->ticket->getPersonData('assignedPerson','fullname'));
if ($assigned) {
	$url->person_id = "{$this->ticket->getPersonData('assignedPerson','_id')}";
	$assigned = "<a href=\"$url\">$assigned</a>";
}

$enteredBy = View::escape($this->ticket->getPersonData('enteredByPerson','fullname'));
if ($enteredBy) {
	$url->person_id = (string)$this->ticket->getPersonData('enteredByPerson','_id');
	$enteredBy = "<a href=\"$url\">$enteredBy</a>";
}

$referred = View::escape($this->ticket->getPersonData('referredPerson','fullname'));
if ($referred) {
	$url->person_id = (string)$this->ticket->getPersonData('referredPerson','_id');
	$referred = "<a href=\"$url\">$referred</a>";
}

$status = $this->ticket->getStatus();
if ($this->ticket->getResolution()) {
	$status.= ': '.$this->ticket->getResolution();
}

$assignButton = '';
$referralButton = '';
$editStatusButton = '';
$deleteButton = '';
$changeLocationButton = '';

if (!$this->disableButtons && userIsAllowed('Tickets')) {
	$editStatusButton = $this->template->linkButton(
		'Edit Status',
		BASE_URL.'/tickets/changeStatus.php?ticket_id='.$this->ticket->getId(),
		'edit'
	);
	$deleteButton = $this->template->linkButton(
		'Delete Case',
		BASE_URL.'/tickets/deleteTicket.php?ticket_id='.$this->ticket->getId(),
		'delete'
	);

	if ($this->ticket->getStatus()!='closed') {
		$assignButton = $this->template->linkButton(
			'Assign',
			BASE_URL.'/tickets/assignTicket.php?ticket_id='.$this->ticket->getId(),
			'edit'
		);

		$return_url = $this->ticket->getURL();
		$referralButton = $this->template->linkButton(
			'Refer',
			BASE_URL.'/tickets/referTicket.php?ticket_id='.$this->ticket->getId(),
			'edit'
		);
		$changeLocationButton = $this->template->linkButton(
			'Change Location',
			BASE_URL.'/tickets/changeLocation.php?ticket_id='.$this->ticket->getId(),
			'edit'
		);
	}

}

$id = $this->ticket->getNumber() ? $this->ticket->getNumber() : $this->ticket->getId();
echo "
<div class=\"ticketInfo\">
	<h1>Case #$id $deleteButton</h1>
	<table>
		<tr><th>Location:</th>
			<td>$location</td>
			<td>$changeLocationButton</td>
		</tr>
		<tr><th>Status:</th>
			<td>$status</td>
			<td>$editStatusButton</td>
		</tr>
		<tr><th>Assigned To:</th>
			<td>$assigned</td>
			<td>$assignButton</td>
		</tr>
		<tr><th>Referred To:</th>
			<td>$referred</td>
			<td>$referralButton</td>
		</tr>
		<tr><th>Created By:</th>
			<td>$enteredBy</td>
		</tr>
		<tr><th>Date Opened:</th>
			<td>{$this->ticket->getEnteredDate(DATE_FORMAT)}</td>
			<td></td>
		</tr>
	</table>
</div>
";