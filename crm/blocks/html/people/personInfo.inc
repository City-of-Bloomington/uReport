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
$email        = View::escape($this->person->getEmail());
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
		<tr><th>email</th><td>$email</td></tr>
		<tr><th>address</th>
			<td><div>$address</div>
				<div>$city $state $zip</div>
			</td>
		</tr>
";
	$phoneButtons = '';
	foreach ($this->person->getPhones() as $p) {
		$label  = View::escape($p->getLabel());
		$number = View::escape($p->getNumber());
		if ($editButton) {
			$phoneButtons = "
			<a class=\"edit button\"
				href=\"".BASE_URI."/phones/update?phone_id={$p->getId()}\">
				Edit Phone
			</a>
			<a class=\"delete button\"
				href=\"".BASE_URI."/phones/delete?phone_id={$p->getId()}\">
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
	echo "
	</table>
	";
	if ($phoneButtons) {
		echo "
		<div>
			<a class=\"add button\"
				href=\"".BASE_URI."/phones/update?person_id={$this->person->getId()}\">
				Add Phone
			</a>
		</div>
		";
	}
echo "
	<div>$deleteButton</div>
</div>
";
