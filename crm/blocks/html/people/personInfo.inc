<?php
/**
 * @copyright 2009-2012 City of Bloomington, Indiana
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
			href=\"".BASE_URL."/users/update?person_id={$this->person->getId()}\">
			Create Account
		</a>
		";
	}

}

$editButton = '';
$deleteButton = '';
if (userIsAllowed('people','update') && !$this->disableButtons) {
	$editButton = "
	<a class=\"edit button\"
		href=\"".BASE_URL."/people/update?person_id={$this->person->getId()}\">
		Edit
	</a>
	";
	if (!$this->person->hasTickets()) {
		$deleteButton = "
		<a class=\"delete button\"
			href=\"".BASE_URL."/people/delete?person_id={$this->person->getId()}\">
			Delete Person
		</a>
		";
	}
}


$name         = View::escape($this->person->getFullname());
$phone        = View::escape($this->person->getPhoneNumber());
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
	<div>$phone</div>
	<div>$email</div>
	<div>$address</div>
	<div>$city $state $zip</div>
	<div>$deleteButton</div>
</div>
";
