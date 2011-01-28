<?php
/**
 * @copyright 2009-2010 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
if (userIsAllowed('Users')) {
	$username = $this->person->getUsername();
	if (!$username && userIsAllowed('Users')) {
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
$address = View::escape($this->person->getAddress());
$phone = View::escape($this->person->getPhone());
$email = View::escape($this->person->getEmail());
echo "
<h1>$name</h1>
<div>$username</div>
<div>$address</div>
<div>$phone</div>
<div>$email</div>
";
