<?php
/**
 * @copyright 2009-2010 City of Bloomington, Indiana
 * @license http://www.gnu.org/copyleft/gpl.html GNU/GPL, see LICENSE.txt
 * @author Cliff Ingham <inghamn@bloomington.in.gov>
 * @param Person $this->person
 */
$username = $this->person->getUsername();
if (!$username && userIsAllowed('Users')) {
	$username = $this->template->linkButton(
		'Create Account',BASE_URL.'/users/addUser.php?person_id='.$this->person->getId(),'add'
	);
}

$name = View::escape($this->person->getFullname());
echo "
<h1>$name</h1>
<table>
	<tr><th>Username</th>
		<td>$username</td>
	</tr>
	<tr><th>Email</th>
		<td>{$this->person->getEmail()}</td>
	</tr>
</table>
";
