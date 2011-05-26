<?php
/**
 * @copyright 2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
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
	$url->person_id = (string)$this->ticket->getPersonData('assignedPerson','_id');
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
$changeLocationButton = '';

if (!$this->disableButtons && userIsAllowed('Tickets')) {
	$editStatusButton = $this->template->linkButton(
		'Edit Status',
		BASE_URL.'/tickets/changeStatus.php?ticket_id='.$this->ticket->getId(),
		'edit'
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
			BASE_URL."/people?return_url=$return_url",
			'edit'
		);
		$changeLocationButton = $this->template->linkButton(
			'Change Location',
			BASE_URL.'/tickets/changeLocation.php?ticket_id='.$this->ticket->getId(),
			'edit'
		);
	}

}

echo "
<div class=\"ticketInfo\">
	<h1>Case</h1>
	<div>$location $changeLocationButton</div>
	<table>
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
