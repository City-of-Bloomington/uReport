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
$address      = View::escape($this->person->getAddress());
$city         = View::escape($this->person->getCity());
$state        = View::escape($this->person->getState());
$zip          = View::escape($this->person->getZip());
echo "
<div class=\"personInfo\">
	<h1>$name $editButton</h1>
	<h2>$organization</h2>
	<div>$username</div>
	<table>
		<tr><th>address</th>
			<td><div>$address</div>
				<div>$city $state $zip</div>
			</td>
		</tr>
";
	$emailButtons = '';
	foreach ($this->person->getEmails() as $e) {
		$label = View::escape($e->getLabel());
		$email = View::escape($e->getEmail());
		if ($editButton) {
			$emailButtons = "
			<a class=\"edit button\"
				href=\"".BASE_URI."/people/updateEmail?email_id={$e->getId()}\">
				Edit Email
			</a>
			<a class=\"delete button\"
				href=\"".BASE_URI."/people/deleteEmail?email_id={$e->getId()}\">
				Delete Email
			</a>
			";
		}
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
		$label  = View::escape($p->getLabel());
		$number = View::escape($p->getNumber());
		if ($editButton) {
			$phoneButtons = "
			<a class=\"edit button\"
				href=\"".BASE_URI."/people/updatePhone?phone_id={$p->getId()}\">
				Edit Phone
			</a>
			<a class=\"delete button\"
				href=\"".BASE_URI."/people/deletePhone?phone_id={$p->getId()}\">
				Delete Phone
			</a>
			";
		}
		echo "
		<tr><th>$label</th>
			<td>$number</td>
			<td>$phoneButtons</td>
		</tr>
		";
	}
	if ($phoneButtons) {
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
