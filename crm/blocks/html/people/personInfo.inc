<?php
/**
 * @copyright 2009-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 * @param bool $this->disableButtons
 */
$username = '';
if (userIsAllowed('people','view')) {
	$username = $this->person->getUsername();
	if (!$username && userIsAllowed('users','add') && !$this->disableButtons) {
		$username = "
		<a class=\"add button\"
			href=\"".BASE_URI."/users/update?person_id={$this->person->getId()}\">
			Create Account
		</a>
		";
	}
}

$editButton   = '';
$deleteButton = '';
if (userIsAllowed('people','update') && !$this->disableButtons) {
	$editButton = "
	<a class=\"edit button\"
		href=\"".BASE_URI."/people/update?person_id={$this->person->getId()}\">
		Edit
	</a>
	";
	if (!$this->person->hasTickets()) {
		$deleteButton = "
		<a class=\"delete button\"
			href=\"".BASE_URI."/people/delete?person_id={$this->person->getId()}\">
			Delete Person
		</a>
		";
	}
}


$name         = View::escape($this->person->getFullname());
$organization = View::escape($this->person->getOrganization());
echo "
<div class=\"personInfo\">
	<h1>$name $editButton</h1>
	<h2>$organization</h2>
	<div>$username</div>
	<table>
";
	$buttonHelper = $this->template->getHelper('commonEditDeleteButtons');

	$addressButtons = '';
	foreach ($this->person->getAddresses() as $a) {
		if ($editButton) {
			$addressButtons = $buttonHelper->commonEditDeleteButtons('people', 'Address', $a);
		}
		$label   = View::escape($a->getLabel());
		$address = View::escape($a->getAddress());
		$city    = View::escape($a->getCity());
		$state   = View::escape($a->getState());
		$zip     = View::escape($a->getZip());
		echo "
		<tr><th>$label</th>
			<td><div>$address</div>
				<div>$city $state $zip</div>
			</td>
			<td>$addressButtons</td>
		</tr>
		";
	}
	if ($editButton) {
		echo "
		<tr><td colspan=\"3\">
				<a class=\"add button\"
					href=\"".BASE_URI."/people/updateAddress?person_id={$this->person->getId()}\">
					Add Address
				</a>
			</td>
		</tr>
		";
	}

	$emailButtons = '';
	foreach ($this->person->getEmails() as $e) {
		if ($editButton) {
			$emailButtons = $buttonHelper->commonEditDeleteButtons('people', 'email', $e);
		}
		$label = View::escape($e->getLabel());
		$email = View::escape($e->getEmail());
		echo "
		<tr><th>$label</th>
			<td>$email</td>
			<td>$emailButtons</td>
		</tr>
		";
	}
	if ($editButton) {
		echo "
		<tr><td colspan=\"3\">
				<a class=\"add button\"
					href=\"".BASE_URI."/people/updateEmail?person_id={$this->person->getId()}\">
					Add Email
				</a>
			</td>
		</tr>
		";
	}
	$phoneButtons = '';
	foreach ($this->person->getPhones() as $p) {
		if ($editButton) {
			$phoneButtons = $buttonHelper->commonEditDeleteButtons('people', 'phone', $p);
		}
		$label  = View::escape($p->getLabel());
		$number = View::escape($p->getNumber());
		echo "
		<tr><th>$label</th>
			<td>$number</td>
			<td>$phoneButtons</td>
		</tr>
		";
	}
	if ($editButton) {
		echo "
		<tr><td colspan=\"3\">
				<a class=\"add button\"
					href=\"".BASE_URI."/people/updatePhone?person_id={$this->person->getId()}\">
					Add Phone
				</a>
			</td>
		</tr>
		";
	}
	echo "
	</table>
	<div>$deleteButton</div>
</div>
";
