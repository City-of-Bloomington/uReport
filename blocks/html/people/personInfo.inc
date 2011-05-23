<?php
/**
 * @copyright 2009-2011 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
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
if (userIsAllowed('People')) {
	$editButton = $this->template->linkButton(
		'Edit',
		BASE_URL.'/people/updatePerson.php?person_id='.$this->person->getId(),
		'edit'
	);
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
</div>
";

if (userIsAllowed('Tickets')) {
	echo "<div>";
	echo $this->linkButton(
			'Add New Case',BASE_URL.'/tickets/addTicket.php?person_id='.$this->person->getId(),'add',null,null,'button'
	);
	echo "</div>";
}