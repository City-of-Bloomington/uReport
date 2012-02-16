<?php
/**
 * @copyright 2011-2012 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Ticket $this->ticket
 * @param bool $this->disableButtons
 */
$location = View::escape($this->ticket->getLocation());
$location = $location
	? "<a href=\"".BASE_URL."/locations/view?location=".urlencode($location)."\">$location</a>"
	: '';

if (userIsAllowed('people','view')) {
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

if (!$this->disableButtons && userIsAllowed('tickets','update')) {
	$editStatusButton = "
	<a class=\"edit button\"
		href=\"".BASE_URL."/tickets/changeStatus?ticket_id={$this->ticket->getId()}\">
		Edit Status
	</a>
	";
	$deleteButton = "
	<a class=\"delete button\"
		href=\"".BASE_URL."/tickets/delete?ticket_id={$this->ticket->getId()}\">
		Delete Case
	</a>
	";

	if ($this->ticket->getStatus()!='closed') {
		$changeCategoryButton = "
		<a class=\"edit button\"
			href=\"".BASE_URL."/tickets/changeCategory?ticket_id={$this->ticket->getId()}\">
			Change Category
		</a>
		";
		$changeLocationButton = "
		<a class=\"edit button\"
			href=\"".BASE_URL."/tickets/changeLocation?ticket_id={$this->ticket->getId()}\">
			Change Location
		</a>
		";
		if (userIsAllowed('people','view')) {
			$assignButton = "
			<a class=\"edit button\"
				href=\"".BASE_URL."/tickets/assign?ticket_id={$this->ticket->getId()}\">
				Assign
			</a>
			";

			$return_url = $this->ticket->getURL();
			$referralButton = "
			<a class=\"edit button\"
				href=\"".BASE_URL."/tickets/refer?ticket_id={$this->ticket->getId()}\">
				Refer
			</a>
			";
		}
	}

}

$id = $this->ticket->getNumber() ? $this->ticket->getNumber() : $this->ticket->getId();
$category = $this->ticket->getData('category.name') ? View::escape($this->ticket->getData('category.name')) : '';
echo "
<div class=\"ticketInfo\">
	<h2><a href=\"{$this->ticket->getURL()}\">Case #$id</a> $deleteButton</h2>
	<table>
		<tr>
			<th>Category:</th>
			<td>$category</td>
			<td>$changeCategoryButton</td>
		</tr>
		<tr>
			<th>Location:</th>
			<td>$location</td>
			<td>$changeLocationButton</td>
		</tr>
		<tr>
			<th>Status:</th>
			<td>$status</td>
			<td>$editStatusButton</td>
		</tr>
";
if (userIsAllowed('people','view')) {
	echo "
		<tr>
			<th>Assigned To:</th>
			<td>$assigned</td>
			<td>$assignButton</td>
		</tr>
		<tr>
			<th>Referred To:</th>
			<td>$referred</td>
			<td>$referralButton</td>
		</tr>
		<tr>
			<th>Created By:</th>
			<td>$enteredBy</td>
			<td></td>
		</tr>
	";
}
	$client = $this->ticket->getClient();
	if ($client) {
		$name = View::escape($client->getName());
		echo "
		<tr>
			<th>Received Via:</th>
			<td>$name</td>
			<td></td>
		</tr>
		";
	}
echo "
		<tr>
			<th>Date Opened:</th>
			<td>{$this->ticket->getEnteredDate(DATE_FORMAT)}</td>
			<td></td>
		</tr>
	</table>
</div>
";