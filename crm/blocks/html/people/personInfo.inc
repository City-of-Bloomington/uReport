<?php
/**
 * @copyright 2009-2014 City of Bloomington, Indiana
 * @license http://www.gnu.org/licenses/agpl.txt GNU/AGPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 * @param bool $this->disableButtons
 */
use Application\Models\Person;
use Blossom\Classes\View;
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
if (Person::isAllowed('people','view')) {
	$username = $this->person->getUsername();
	if ($username && $resource=='account' && $_SESSION['USER']->getAuthenticationMethod()=='local') {
		$username.= "
		<a href=\"".BASE_URI."/account/changePassword\">
			{$this->translate('labels.change_password')}
		</a>
		";
	}
	if (!$username && Person::isAllowed('users','add') && !$this->disableButtons) {
		$username = $helper->buttonLink(
			BASE_URI.'/users/update?person_id='.$this->person->getId(),
			$this->translate('labels.create_account'),
			'add'
		);
	}
}

$editButton   = '';
$deleteButton = '';
if (Person::isAllowed($resource,'update') && !$this->disableButtons) {
	$editButton = $helper->buttonLink(
		BASE_URI."/$resource/update$person_param",
		$this->translate('labels.edit'),
		'edit'
	);
	if (!$this->person->hasTickets()) {
		$deleteButton = $helper->buttonLink(
			BASE_URI."/$resource/delete$person_param",
			$this->translate('labels.delete'),
			'delete'
		);
	}
}

$name         = View::escape($this->person->getFullname());
$organization = View::escape($this->person->getOrganization());
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
	echo "
	<h3>{$this->translate(array('labels.address', 'labels.addresses', 2))}</h3>
	<table>
	";
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
		$addButton = $helper->buttonLink(
			BASE_URI."/$resource/updateAddress$person_param",
			"{$this->translate('labels.add')} {$this->translate(array('labels.address', 'labels.addresses', 1))}",
			'add'
		);
		echo "<tr><td colspan=\"3\">$addButton</td></tr>";
	}

	echo "
	</table>

	<h3>{$this->translate(array('labels.email', 'labels.emails', 2))}</h3>
	<table>
	";
	$emailButtons = '';
	foreach ($this->person->getEmails() as $e) {
		if ($editButton) {
			$emailButtons = $buttonHelper->commonEditDeleteButtons($resource, 'email', $e);
		}
		$label = View::escape($e->getLabel());
		$email = View::escape($e->getEmail());
		$class = $e->isUsedForNotifications() ? 'class="usedForNotifications"' : '';
		echo "
		<tr $class>
			<th>$label</th>
			<td>$email</td>
			<td>$emailButtons</td>
		</tr>
		";
	}
	if ($editButton) {
		$addButton = $helper->buttonLink(
			BASE_URI."/$resource/updateEmail$person_param",
			"{$this->translate('labels.add')} {$this->translate(array('labels.email', 'labels.emails', 1))}",
			'add'
		);
		echo "<tr><td colspan=\"3\">$addButton</td></tr>";
	}
	echo "
	</table>

	<h3>{$this->translate(array('labels.phone', 'labels.phones', 2))}</h3>
	<table>
	";
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
		$addButton = $helper->buttonLink(
			BASE_URI."/$resource/updatePhone$person_param",
			"{$this->translate('labels.add')} {$this->translate(array('labels.phone', 'labels.phones', 1))}",
			'add'
		);
		echo "<tr><td colspan=\"3\">$addButton</td></tr>";
	}
	echo "
	</table>
</div>
<table>
	<tr class=\"usedForNotifications\">
		<td>{$this->translate('messages.usedForNotifications')}</td>
	</tr>
</table>
";
