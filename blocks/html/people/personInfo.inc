<?php
/**
 * @copyright 2009-2010 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
if (userIsAllowed('Users')) {
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

$name = View::escape($this->person->getFullname());
$phone = View::escape($this->person->getPhone());
$email = View::escape($this->person->getEmail());
$address = View::escape($this->person->getAddress());
$city = View::escape($this->person->getCity());
$state = View::escape($this->person->getState());
$zip = View::escape($this->person->getZip());
echo "
<div class=\"personInfo\">
	<h1>$name</h1>
	<div>$username</div>
	<div>$phone</div>
	<div>$email</div>
	<div>$address</div>
	<div>$city $state $zip</div>
</div>
";
