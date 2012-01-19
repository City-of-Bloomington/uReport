<?php
/**
 * @copyright 2009-2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 * @param bool $this->disableButtons
 */
$username = '';
if (userIsAllowed('People')) {
	$username = $this->person->getUsername();
	if (!$username && userIsAllowed('Users') && !$this->disableButtons) {
		$username = "
		<a class=\"add button\"
			href=\"".BASE_URL."/users/updateUser.php?person_id={$this->person->getId()}\">
			Create Account
		</a>
		";
	}

}

$editButton = '';
$deleteButton = '';
if (userIsAllowed('People') && !$this->disableButtons) {
	$editButton = "
	<a class=\"edit button\"
		href=\"".BASE_URL."/people/updatePerson.php?person_id={$this->person->getId()}\">
		Edit
	</a>
	";
	if (!$this->person->hasTickets()) {
		$deleteButton = "
		<a class=\"delete button\"
			href=\"".BASE_URL."/people/deletePerson.php?person_id={$this->person->getId()}\">
			Delete Person
		</a>
		";
	}
}


$name = View::escape($this->person->getFullname());
$phone = View::escape($this->person->getPhoneNumber());
$email = View::escape($this->person->getEmail());
$organization = View::escape($this->person->getOrganization());
$address = View::escape($this->person->getAddress());
$city = View::escape($this->person->getCity());
$state = View::escape($this->person->getState());
$zip = View::escape($this->person->getZip());
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
