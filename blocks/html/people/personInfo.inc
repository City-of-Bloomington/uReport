<?php
/**
 * @copyright 2009-2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 * @param bool $this->disableButtons
 */
if (userIsAllowed('People')) {
	$username = $this->person->getUsername();
	if (!$username && userIsAllowed('Users') && !$this->disableButtons) {
		$username = $this->template->linkButton(
			'Create Account',
			BASE_URL.'/users/updateUser.php?person_id='.$this->person->getId(),
			'add',null,null,'largeButton'
		);
	}

}
else {
	$username = '';
}

$editButton = '';
$deleteButton = '';
if (userIsAllowed('People') && !$this->disableButtons) {
	$editButton = $this->template->linkButton(
		'Edit',
		BASE_URL.'/people/updatePerson.php?person_id='.$this->person->getId(),
		'edit'
	);
	if (!$this->person->hasTickets()) {
		$deleteButton = $this->template->linkButton(
			'Delete Person',
			BASE_URL.'/people/deletePerson.php?person_id='.$this->person->getId(),
			'delete'
		);
	}
}


$name = View::escape($this->person->getFullname());
$phone = View::escape($this->person->getPhone());
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

if (userIsAllowed('Tickets') && !$this->disableButtons) {
	echo $this->linkButton(
		'Report New Case by This Person',BASE_URL.'/tickets/addTicket.php?person_id='.$this->person->getId(),
		'add',null,null,'button'
	);
}
