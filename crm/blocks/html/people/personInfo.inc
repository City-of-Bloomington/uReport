<?php
/**
 * @copyright 2009-2016 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @param Person $this->person
 * @param bool $this->disableButtons
 */
use Application\Models\Person;

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

$helper = $this->template->getHelper('buttonLink');

$username = '';
if (Person::isAllowed('people', 'view')) {
	$username = $this->person->getUsername();
	if ($username && $resource=='account' && $_SESSION['USER']->getAuthenticationMethod()=='local') {
		$username.= "
		<a href=\"".BASE_URI."/account/changePassword\">
			{$this->_('change_password')}
		</a>
		";
	}
	if (!$username && Person::isAllowed('users', 'add') && !$this->disableButtons) {
		$username = $helper->buttonLink(
			BASE_URI.'/users/update?person_id='.$this->person->getId(),
			$this->_('create_account'),
			'add'
		);
	}
}

$editButton   = '';
$deleteButton = '';
if (Person::isAllowed($resource, 'update') && !$this->disableButtons) {
	$editButton = $helper->buttonLink(
		BASE_URI."/$resource/update$person_param",
		$this->_('edit'),
		'edit'
	);
	if (!$this->person->hasTickets()) {
		$deleteButton = $helper->buttonLink(
			BASE_URI."/$resource/delete$person_param",
			$this->_('delete'),
			'delete'
		);
	}
}

$name         = self::escape($this->person->getFullname());
$organization = self::escape($this->person->getOrganization());
if ($organization) {
	$organization = "
		<tr><th>Organization</th>
			<td>$organization</td>
		</tr>
	";
}
if ($username) {
	$username = "
		<tr><th>Username</th>
			<td>$username</td>
		</tr>
	";

}
echo "
<div class=\"personInfo\">
	<h2>$name $editButton $deleteButton</h2>
";
	if ($organization || $username) {
		echo "
		<table>
			$organization
			$username
		</table>
		";
	}
	$buttonHelper = $this->template->getHelper('commonEditDeleteButtons');

	$addressButtons = '';
	$addressList    = $this->person->getAddresses();
	$c              = count($addressList);
	echo "
	<h3>{$this->_(['address', 'addresses', $c])}</h3>
	<table>
	";
	foreach ($addressList as $a) {
		if ($editButton) {
			$addressButtons = $buttonHelper->commonEditDeleteButtons($resource, 'address', $a);
		}
		$label   = self::escape($a->getLabel());
		$address = self::escape($a->getAddress());
		$city    = self::escape($a->getCity());
		$state   = self::escape($a->getState());
		$zip     = self::escape($a->getZip());
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
		$addButton = $helper->buttonLink(
			BASE_URI."/$resource/updateAddress$person_param",
			"{$this->_('add')} {$this->_(array('address', 'addresses', 1))}",
			'add'
		);
		echo "<tr><td colspan=\"3\">$addButton</td></tr>";
	}

	echo "
	</table>

	<h3>{$this->_(array('email', 'emails', 2))}</h3>
	<table>
	";
	$emailButtons = '';
	foreach ($this->person->getEmails() as $e) {
		if ($editButton) {
			$emailButtons = $buttonHelper->commonEditDeleteButtons($resource, 'email', $e);
		}
		$label = self::escape($e->getLabel());
		$email = self::escape($e->getEmail());
		$class = $e->isUsedForNotifications() ? 'class="usedForNotifications"' : '';
		echo "
		<tr $class>
			<th>$label</th>
			<td><a href=\"mailto:$email\">$email</a></td>
			<td>$emailButtons</td>
		</tr>
		";
	}
	if ($editButton) {
		$addButton = $helper->buttonLink(
			BASE_URI."/$resource/updateEmail$person_param",
			"{$this->_('add')} {$this->_(array('email', 'emails', 1))}",
			'add'
		);
		echo "<tr><td colspan=\"3\">$addButton</td></tr>";
	}
	echo "
	</table>
	";

	$phoneButtons = '';
	$phones = $this->person->getPhones();
	$c      = count($phones);
	echo "
	<h3>{$this->_(['phone', 'phones', $c])}</h3>
	<table>
	";
	foreach ($phones as $p) {
		if ($editButton) {
			$phoneButtons = $buttonHelper->commonEditDeleteButtons($resource, 'phone', $p);
		}
		$label  = self::escape($p->getLabel());
		$number = self::escape($p->getNumber());
		echo "
		<tr><th>$label</th>
			<td>$number</td>
			<td>$phoneButtons</td>
		</tr>
		";
	}
	if ($editButton) {
		$addButton = $helper->buttonLink(
			BASE_URI."/$resource/updatePhone$person_param",
			"{$this->_('add')} {$this->_('phone')}",
			'add'
		);
		echo "<tr><td colspan=\"3\">$addButton</td></tr>";
	}
	echo "
	</table>
</div>
<table>
	<tr class=\"usedForNotifications\">
		<td>{$this->_('usedForNotifications', 'messages')}</td>
	</tr>
</table>
";
