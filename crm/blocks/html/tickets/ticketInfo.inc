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

if (userIsAllowed('People')) {
	$url = new URL(BASE_URL.'/people/viewPerson.php');
	$personFields = array('enteredBy','assigned','referred');
	foreach ($personFields as $field) {
		$$field = '';
		$get = 'get'.ucfirst($field).'Person';
		$person = $this->ticket->$get();
		if ($person) {
			$url->person_id = "{$person->getId()}";
			$$field = "<a href=\"$url\">".View::escape($person->getFullname())."</a>";
		}

	}
}

$status = $this->ticket->getStatus();
if ($this->ticket->getResolution()) {
	$status.= ': '.$this->ticket->getResolution();
}

$assignButton = '';
$referralButton = '';
$editStatusButton = '';
$deleteButton = '';
$changeCategoryButton = '';
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
		$changeCategoryButton = $this->template->linkButton(
			'Change Category',
			BASE_URL.'/tickets/changeCategory.php?ticket_id='.$this->ticket->getId(),
			'edit'
		);
		$changeLocationButton = $this->template->linkButton(
			'Change Location',
			BASE_URL.'/tickets/changeLocation.php?ticket_id='.$this->ticket->getId(),
			'edit'
		);
		if (userIsAllowed('People')) {
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
		}
	}

}

$id = $this->ticket->getNumber() ? $this->ticket->getNumber() : $this->ticket->getId();
$category = $this->ticket->getData('category.name') ? View::escape($this->ticket->getData('category.name')) : '';
echo "
<div class=\"ticketInfo\">
	<h1>Case #$id $deleteButton</h1>
	<table>
		<tr><td>$changeCategoryButton</td>
			<th>Category:</th>
			<td>$category</td>
		</tr>
		<tr><td>$changeLocationButton</td>
			<th>Location:</th>
			<td>$location</td>
		</tr>
		<tr><td>$editStatusButton</td>
			<th>Status:</th>
			<td>$status</td>
		</tr>
";
if (userIsAllowed('People')) {
	echo "
		<tr><td>$assignButton</td>
			<th>Assigned To:</th>
			<td>$assigned</td>
		</tr>
		<tr><td>$referralButton</td>
			<th>Referred To:</th>
			<td>$referred</td>
		</tr>
		<tr><td></td>
			<th>Created By:</th>
			<td>$enteredBy</td>
		</tr>
	";
}
echo "
		<tr><td></td>
			<th>Date Opened:</th>
			<td>{$this->ticket->getEnteredDate(DATE_FORMAT)}</td>
		</tr>
	</table>
</div>
";