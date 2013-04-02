<?php
/**
 * @copyright 2009-2013 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 * @param bool $this->disableButtons
 */
// This screen is also used when a person views their own account.
// When a person is viewing their own account, the URLs need to point
// to the "account" versions of updating things.
// For the account URLs, you should never include a person_id parameter.
// The account controller only modifies with the $_SESSION[USER]
if ($this->template->controller == 'AccountController') {
	$resource = 'account';
	$person_param = '';
}
else {
	$resource = 'people';
	$person_param = '?person_id='.$this->person->getId();
}


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
if (userIsAllowed($resource,'update') && !$this->disableButtons) {
	$editButton = "
	<a class=\"edit button\"
		href=\"".BASE_URI."/$resource/update$person_param\">
		Edit
	</a>
	";
	if (!$this->person->hasTickets()) {
		$deleteButton = "
		<a class=\"delete button\"
			href=\"".BASE_URI."/$resource/delete$person_param\">
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
			$addressButtons = $buttonHelper->commonEditDeleteButtons($resource, 'address', $a);
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
					href=\"".BASE_URI."/$resource/updateAddress$person_param\">
					Add Address
				</a>
			</td>
		</tr>
		";
	}

	$emailButtons = '';
	foreach ($this->person->getEmails() as $e) {
		if ($editButton) {
			$emailButtons = $buttonHelper->commonEditDeleteButtons($resource, 'email', $e);
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
					href=\"".BASE_URI."/$resource/updateEmail$person_param\">
					Add Email
				</a>
			</td>
		</tr>
		";
	}
	$phoneButtons = '';
	foreach ($this->person->getPhones() as $p) {
		if ($editButton) {
			$phoneButtons = $buttonHelper->commonEditDeleteButtons($resource, 'phone', $p);
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
					href=\"".BASE_URI."/$resource/updatePhone$person_param\">
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
